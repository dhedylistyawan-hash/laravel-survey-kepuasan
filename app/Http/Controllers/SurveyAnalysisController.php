<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SurveyAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $surveyId = $request->input('survey_id');
        $period = $request->input('period', 'current'); // current, jan-jun, jul-dec
        $year = $request->input('year', date('Y'));

        // Filter berdasarkan periode
        $dateFilter = $this->getDateFilter($period, $year);

        $data = $this->getAnalysisData($surveyId, $dateFilter);

        return view('admin.survey-analysis.index', compact('data', 'period', 'year'));
    }

    private function getDateFilter($period, $year)
    {
        switch ($period) {
            case 'jan-jun':
                return [
                    'start' => Carbon::create($year, 1, 1)->startOfDay(),
                    'end' => Carbon::create($year, 6, 30)->endOfDay()
                ];
            case 'jul-dec':
                return [
                    'start' => Carbon::create($year, 7, 1)->startOfDay(),
                    'end' => Carbon::create($year, 12, 31)->endOfDay()
                ];
            default:
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear()
                ];
        }
    }

    private function getAnalysisData($surveyId, $dateFilter)
    {
        // 1. Banyaknya responden
        $totalRespondents = $this->getTotalRespondents($surveyId, $dateFilter);

        // 2. Tingkat kualitas pelayanan dan harapan
        $serviceQualityData = $this->getServiceQualityData($surveyId, $dateFilter);
        $expectationData = $this->getExpectationData($surveyId, $dateFilter);

        // 3. Nilai rata-rata setiap pertanyaan
        $questionAverages = $this->getQuestionAverages($surveyId, $dateFilter);

        // 4. Nilai rata-rata seluruh pertanyaan
        $overallAverages = $this->getOverallAverages($surveyId, $dateFilter);

        // 5. Gap analysis
        $gapAnalysis = $this->getGapAnalysis($surveyId, $dateFilter);

        // 6. Identifikasi layanan yang perlu perbaikan
        $improvementAreas = $this->getImprovementAreas($gapAnalysis);

        // 7. Data demografis untuk chart
        $demographicData = $this->getDemographicChartData($surveyId, $dateFilter);

        return [
            'total_respondents' => $totalRespondents,
            'service_quality' => $serviceQualityData,
            'expectations' => $expectationData,
            'question_averages' => $questionAverages,
            'overall_averages' => $overallAverages,
            'gap_analysis' => $gapAnalysis,
            'improvement_areas' => $improvementAreas,
            'satisfaction_distribution' => $serviceQualityData['distribution'],
            'importance_distribution' => $expectationData['distribution'],
            'status_distribution' => $this->getStatusDistribution($questionAverages),
            'final_results_table' => $this->getFinalResultsTable($overallAverages),
            'demographic_data' => $demographicData,
            'demographic_table_data' => $this->getDemographicData($surveyId, $dateFilter),
            'period' => $dateFilter
        ];
    }

    private function getTotalRespondents($surveyId, $dateFilter)
    {
        // Hitung responden unik - setiap submit survey = 1 responden
        $responseQuery = Response::query();
        if ($surveyId) {
            $responseQuery->where('survey_id', $surveyId);
        }
        $responseQuery->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        $totalRespondents = $responseQuery->distinct('session_id')->count('session_id');
        return $totalRespondents;
    }

    private function getServiceQualityData($surveyId, $dateFilter)
    {
        $query = Response::whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']])
            ->where('satisfaction', '!=', null);

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        return [
            'total_responses' => $query->count(),
            'average_satisfaction' => $query->avg('satisfaction'),
            'distribution' => $this->getDistribution($query->pluck('satisfaction'))
        ];
    }

    private function getExpectationData($surveyId, $dateFilter)
    {
        $query = Response::whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']])
            ->where('importance', '!=', null);

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        return [
            'total_responses' => $query->count(),
            'average_importance' => $query->avg('importance'),
            'distribution' => $this->getDistribution($query->pluck('importance'))
        ];
    }

    private function getQuestionAverages($surveyId, $dateFilter)
    {
        $questions = Question::with(['responses' => function($q) use ($dateFilter) {
            $q->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);
        }]);

        if ($surveyId) {
            $questions->where('survey_id', $surveyId);
        }

        return $questions->get()->map(function($question) {
            $satisfactionAvg = $question->responses->whereNotNull('satisfaction')->avg('satisfaction');
            $importanceAvg = $question->responses->whereNotNull('importance')->avg('importance');

            // Hitung hasil akhir berdasarkan rata-rata kepuasan
            $finalResult = $this->getFinalResultForQuestion($satisfactionAvg);

            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'category' => $question->category->name,
                'satisfaction_avg' => round($satisfactionAvg, 2),
                'importance_avg' => round($importanceAvg, 2),
                'gap' => round($importanceAvg - $satisfactionAvg, 2),
                'final_result' => $finalResult
            ];
        });
    }

    private function getOverallAverages($surveyId, $dateFilter)
    {
        $query = Response::whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']]);

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        return [
            'overall_satisfaction' => round($query->whereNotNull('satisfaction')->avg('satisfaction'), 2),
            'overall_importance' => round($query->whereNotNull('importance')->avg('importance'), 2),
            'overall_gap' => round(
                $query->whereNotNull('importance')->avg('importance') -
                $query->whereNotNull('satisfaction')->avg('satisfaction'), 2
            )
        ];
    }

    private function getGapAnalysis($surveyId, $dateFilter)
    {
        // Gunakan data yang sama dengan getQuestionAverages untuk konsistensi
        $query = Response::with(['question.category'])
            ->whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']])
            ->whereNotNull('satisfaction')
            ->whereNotNull('importance');

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        return $query->get()
            ->groupBy('question_id')
            ->map(function($responses, $questionId) {
                $question = $responses->first()->question;
                $satisfactionAvg = $responses->avg('satisfaction');
                $importanceAvg = $responses->avg('importance');
                $gap = $importanceAvg - $satisfactionAvg;

                return [
                    'question_id' => $questionId,
                    'question_text' => $question->question_text,
                    'category' => $question->category->name,
                    'satisfaction_avg' => round($satisfactionAvg, 2),
                    'importance_avg' => round($importanceAvg, 2),
                    'gap' => round($gap, 2),
                    'gap_status' => $this->getGapStatus($gap)
                ];
            })
            ->sortByDesc('gap');
    }

    private function getImprovementAreas($gapAnalysis)
    {
        return $gapAnalysis->filter(function($item) {
            return $item['gap'] > 0.5; // Gap signifikan (hanya tampil jika gap > 0.5)
        })->sortByDesc('gap')->take(10);
    }

    private function getDistribution($values)
    {
        $distribution = [];
        for ($i = 1; $i <= 4; $i++) {
            $distribution[$i] = $values->filter(function($value) use ($i) {
                return $value == $i;
            })->count();
        }
        return $distribution;
    }

    private function getGapStatus($gap)
    {
        if ($gap <= 0) return 'Baik';
        if ($gap <= 0.5) return 'Cukup';
        if ($gap <= 1.0) return 'Perlu Perbaikan';
        return 'Sangat Perlu Perbaikan';
    }

    private function getStatusDistribution($questionAverages)
    {
        $distribution = [
            'Baik' => 0,
            'Cukup' => 0,
            'Perlu Perbaikan' => 0,
            'Sangat Perlu Perbaikan' => 0
        ];

        foreach ($questionAverages as $question) {
            $status = $this->getGapStatus($question['gap']);
            $distribution[$status]++;
        }

        return $distribution;
    }

    private function getFinalResultsTable($overallAverages)
    {
        $overallSatisfaction = $overallAverages['overall_satisfaction'];
        $overallImportance = $overallAverages['overall_importance'];
        
        // Tentukan nilai persepsi berdasarkan rata-rata keseluruhan
        $satisfactionPerception = $this->getPerceptionValue($overallSatisfaction);
        $importancePerception = $this->getPerceptionValue($overallImportance);
        
        // Tentukan nilai interval
        $satisfactionInterval = $this->getIntervalValue($overallSatisfaction);
        $importanceInterval = $this->getIntervalValue($overallImportance);
        
        // Tentukan nilai interval konversi
        $satisfactionNIK = $this->getNIKValue($overallSatisfaction);
        $importanceNIK = $this->getNIKValue($overallImportance);
        
        // Tentukan mutu pelayanan dan kinerja unit pelayanan
        $serviceQuality = $this->getServiceQualityGrade($overallSatisfaction);
        $unitPerformance = $this->getUnitPerformanceGrade($overallSatisfaction);
        
        return [
            'satisfaction' => [
                'perception_value' => $satisfactionPerception,
                'interval' => $satisfactionInterval,
                'nik' => $satisfactionNIK,
                'service_quality' => $serviceQuality,
                'unit_performance' => $unitPerformance
            ],
            'importance' => [
                'perception_value' => $importancePerception,
                'interval' => $importanceInterval,
                'nik' => $importanceNIK,
                'service_quality' => $this->getServiceQualityGrade($overallImportance),
                'unit_performance' => $this->getUnitPerformanceGrade($overallImportance)
            ]
        ];
    }

    private function getPerceptionValue($average)
    {
        if ($average >= 3.26) return 4;
        if ($average >= 2.51) return 3;
        if ($average >= 1.76) return 2;
        return 1;
    }

    private function getIntervalValue($average)
    {
        if ($average >= 3.26) return '3.26 - 4.00';
        if ($average >= 2.51) return '2.51 - 3.25';
        if ($average >= 1.76) return '1.76 - 2.50';
        return '1.00 - 1.75';
    }

    private function getNIKValue($average)
    {
        if ($average >= 3.26) return '81.26 - 100.00';
        if ($average >= 2.51) return '62.51 - 81.25';
        if ($average >= 1.76) return '43.76 - 62.50';
        return '25.00 - 43.75';
    }

    private function getServiceQualityGrade($average)
    {
        if ($average >= 3.26) return 'A';
        if ($average >= 2.51) return 'B';
        if ($average >= 1.76) return 'C';
        return 'D';
    }

    private function getUnitPerformanceGrade($average)
    {
        if ($average >= 3.26) return 'Sangat Baik';
        if ($average >= 2.51) return 'Baik';
        if ($average >= 1.76) return 'Kurang Baik';
        return 'Tidak Baik';
    }

    private function getFinalResultForQuestion($satisfactionAvg)
    {
        // Tentukan mutu pelayanan dan kinerja unit pelayanan berdasarkan rata-rata kepuasan
        $serviceQuality = $this->getServiceQualityGrade($satisfactionAvg);
        $unitPerformance = $this->getUnitPerformanceGrade($satisfactionAvg);
        
        return [
            'service_quality' => $serviceQuality,
            'unit_performance' => $unitPerformance,
            'display_text' => $serviceQuality . ' (' . $unitPerformance . ')',
            'color_class' => $this->getFinalResultColorClass($serviceQuality)
        ];
    }

    private function getFinalResultColorClass($serviceQuality)
    {
        switch ($serviceQuality) {
            case 'A':
                return 'text-blue-600 bg-blue-100';
            case 'B':
                return 'text-green-600 bg-green-100';
            case 'C':
                return 'text-yellow-600 bg-yellow-100';
            case 'D':
                return 'text-red-600 bg-red-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    }

    public static function getShortQuestionText($questionText)
    {
        // Mapping untuk menyingkat pertanyaan menjadi tema pokok
        $shortMappings = [
            'Kecepatan dalam menyampaikan informasi terkait kegiatan dan layanan pembinaan jabatan fungsional' => 'Kecepatan Informasi',
            'Ketepatan waktu pelaksanaan kegiatan atau penyampaian layanan' => 'Ketepatan Waktu',
            'Kecepatan merespon pertanyaan, permintaan, atau pengaduan dari' => 'Kecepatan Respons',
            'Ketepatan dan keakuratan informasi yang disampaikan' => 'Keakuratan Informasi',
            'Profesionalisme dan etika pegawai dalam memberikan layanan' => 'Profesionalisme Pegawai',
            'Sikap dan tutur kata pegawai dalam melayani' => 'Sikap Pegawai',
            'Pemahaman pegawai terhadap subjek layanan' => 'Pemahaman Pegawai',
            'Kemudahan peserta atau pengguna dalam mengakses layanan' => 'Kemudahan Akses',
            'Komitmen dan kesungguhan Pusbin dalam memberikan layanan' => 'Komitmen Pusbin'
        ];

        // Cek apakah ada mapping yang cocok
        foreach ($shortMappings as $full => $short) {
            if (strpos($questionText, $full) !== false) {
                return $short;
            }
        }

        // Jika tidak ada mapping, ambil kata kunci pertama
        $words = explode(' ', $questionText);
        if (count($words) >= 2) {
            return $words[0] . ' ' . $words[1];
        }

        return Str::limit($questionText, 20);
    }

    public function export(Request $request)
    {
        $surveyId = $request->input('survey_id');
        $period = $request->input('period', 'current');
        $year = $request->input('year', date('Y'));

        $dateFilter = $this->getDateFilter($period, $year);
        $data = $this->getAnalysisData($surveyId, $dateFilter);

        $filename = 'survey_analysis_' . $period . '_' . $year . '_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $request, $dateFilter) {
            $file = fopen('php://output', 'w');

            // Header laporan
            fputcsv($file, ['LAPORAN ANALISIS SURVEY KEPUASAN']);
            fputcsv($file, ['']);

            // 1. Periode dan jumlah responden
            fputcsv($file, ['1. PERIODE PENILAIAN']);
            fputcsv($file, ['Periode', $data['period']['start']->format('d/m/Y') . ' - ' . $data['period']['end']->format('d/m/Y')]);
            fputcsv($file, ['Jumlah Responden', $data['total_respondents']]);
            fputcsv($file, ['']);

            // 2. Rata-rata keseluruhan
            fputcsv($file, ['2. RATA-RATA KESELURUHAN']);
            fputcsv($file, ['Tingkat Kepuasan', $data['overall_averages']['overall_satisfaction']]);
            fputcsv($file, ['Tingkat Kepentingan', $data['overall_averages']['overall_importance']]);
            fputcsv($file, ['Gap', $data['overall_averages']['overall_gap']]);
            fputcsv($file, ['']);

            // 2.5. Tabel Hasil Akhir
            fputcsv($file, ['2.5. TABEL HASIL AKHIR']);
            fputcsv($file, ['Nilai Persepsi', 'Mutu Pelayanan (x)', 'Kinerja Unit Pelayanan (y)']);
            
            // Baris untuk Tingkat Kepuasan
            fputcsv($file, [
                $data['final_results_table']['satisfaction']['perception_value'],
                $data['final_results_table']['satisfaction']['service_quality'],
                $data['final_results_table']['satisfaction']['unit_performance']
            ]);
            
            // Baris untuk Tingkat Kepentingan
            fputcsv($file, [
                $data['final_results_table']['importance']['perception_value'],
                $data['final_results_table']['importance']['service_quality'],
                $data['final_results_table']['importance']['unit_performance']
            ]);
            fputcsv($file, ['']);
            
            // 2.6. Rumusan Penilaian
            fputcsv($file, ['2.6. RUMUSAN PENILAIAN']);
            fputcsv($file, ['Nilai Interval (N)', 'Nilai Interval Konversi (NIK)', 'Nilai Persepsi']);
            fputcsv($file, ['1.00 - 1.75', '25.00 - 43.75', '1']);
            fputcsv($file, ['1.76 - 2.50', '43.76 - 62.50', '2']);
            fputcsv($file, ['2.51 - 3.25', '62.51 - 81.25', '3']);
            fputcsv($file, ['3.26 - 4.00', '81.26 - 100.00', '4']);
            fputcsv($file, ['']);

            // 3. Chart 1: Gap Analysis
            fputcsv($file, ['3. CHART 1: GAP ANALYSIS (KEPENTINGAN VS KEPUASAN)']);
            fputcsv($file, ['No', 'Pertanyaan', 'Rata-rata Kepuasan', 'Rata-rata Kepentingan', 'Gap', 'Status', 'Hasil Akhir']);

            foreach ($data['question_averages'] as $index => $question) {
                $finalResult = isset($question['final_result']) ? $question['final_result']['display_text'] : 'N/A';
                fputcsv($file, [
                    $index + 1,
                    $question['question_text'],
                    $question['satisfaction_avg'],
                    $question['importance_avg'],
                    $question['gap'],
                    $this->getGapStatus($question['gap']),
                    $finalResult
                ]);
            }

            fputcsv($file, ['']);
            
            // 3.1. Keterangan Hasil Akhir
            fputcsv($file, ['3.1. KETERANGAN HASIL AKHIR']);
            fputcsv($file, ['Hasil Akhir ditentukan berdasarkan Rata-rata Kepuasan dari setiap pertanyaan menggunakan skala yang sama dengan Tabel Hasil Akhir:']);
            fputcsv($file, ['A (Sangat Baik)', 'Rata-rata Kepuasan 3.26 - 4.00']);
            fputcsv($file, ['B (Baik)', 'Rata-rata Kepuasan 2.51 - 3.25']);
            fputcsv($file, ['C (Kurang Baik)', 'Rata-rata Kepuasan 1.76 - 2.50']);
            fputcsv($file, ['D (Tidak Baik)', 'Rata-rata Kepuasan 1.00 - 1.75']);
            fputcsv($file, ['Catatan: Hasil Akhir menunjukkan mutu pelayanan dan kinerja unit pelayanan untuk setiap pertanyaan berdasarkan tingkat kepuasan responden.']);
            fputcsv($file, ['']);

            // 4. Chart 2: Distribusi Jawaban
            fputcsv($file, ['4. CHART 2: DISTRIBUSI JAWABAN']);

            // Distribusi Kepuasan
            fputcsv($file, ['4.1. DISTRIBUSI TINGKAT KEPUASAN']);
            fputcsv($file, ['Level', 'Jumlah', 'Persentase']);
            $totalSatisfaction = array_sum($data['satisfaction_distribution']);
            foreach ($data['satisfaction_distribution'] as $level => $count) {
                $percentage = $totalSatisfaction > 0 ? round(($count / $totalSatisfaction) * 100, 1) : 0;
                $levelText = $this->getLevelText($level, 'satisfaction');
                fputcsv($file, [$levelText, $count, $percentage . '%']);
            }

            fputcsv($file, ['']);

            // Distribusi Kepentingan
            fputcsv($file, ['4.2. DISTRIBUSI TINGKAT KEPENTINGAN']);
            fputcsv($file, ['Level', 'Jumlah', 'Persentase']);
            $totalImportance = array_sum($data['importance_distribution']);
            foreach ($data['importance_distribution'] as $level => $count) {
                $percentage = $totalImportance > 0 ? round(($count / $totalImportance) * 100, 1) : 0;
                $levelText = $this->getLevelText($level, 'importance');
                fputcsv($file, [$levelText, $count, $percentage . '%']);
            }

            fputcsv($file, ['']);

            // 5. Chart 3: Top 10 Area Perbaikan
            fputcsv($file, ['5. CHART 3: TOP 10 AREA YANG PERLU PERBAIKAN']);
            fputcsv($file, ['No', 'Pertanyaan', 'Kategori', 'Gap', 'Status']);

            foreach ($data['improvement_areas'] as $index => $area) {
                fputcsv($file, [
                    $index + 1,
                    $area['question_text'],
                    $area['category'],
                    $area['gap'],
                    $area['gap_status']
                ]);
            }

            fputcsv($file, ['']);

            // 6. Chart 4: Distribusi Status
            fputcsv($file, ['6. CHART 4: DISTRIBUSI STATUS PERBAIKAN']);
            fputcsv($file, ['Status', 'Jumlah Pertanyaan', 'Persentase', 'Deskripsi']);
            $totalQuestions = $data['question_averages']->count();
            $baikCount = $data['question_averages']->filter(function($q) { return $q['gap'] <= 0; })->count();
            $cukupCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 0 && $q['gap'] <= 0.5; })->count();
            $perluCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 0.5 && $q['gap'] <= 1.0; })->count();
            $sangatCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 1.0; })->count();

            fputcsv($file, ['Baik', $baikCount, round(($baikCount / $totalQuestions) * 100, 1) . '%', 'Gap ≤ 0 (Kepuasan ≥ Kepentingan)']);
            fputcsv($file, ['Cukup', $cukupCount, round(($cukupCount / $totalQuestions) * 100, 1) . '%', 'Gap 0.1 - 0.5']);
            fputcsv($file, ['Perlu Perbaikan', $perluCount, round(($perluCount / $totalQuestions) * 100, 1) . '%', 'Gap 0.6 - 1.0']);
            fputcsv($file, ['Sangat Perlu Perbaikan', $sangatCount, round(($sangatCount / $totalQuestions) * 100, 1) . '%', 'Gap > 1.0']);

            fputcsv($file, ['']);

                        // 7. Data Demografis (jika tersedia)
            fputcsv($file, ['7. DATA DEMOGRAFIS RESPONDEN (10 DATA TERATAS)']);
            fputcsv($file, ['Menampilkan 10 data demografis teratas berdasarkan jumlah responden terbanyak']);
            fputcsv($file, ['No', 'Jenis Kelamin', 'Umur', 'Pendidikan', 'Unit Kerja', 'Jabatan Fungsional', 'Jumlah']);

            // Ambil data demografis dari responses (sudah dibatasi 10)
            $demographicData = $this->getDemographicData($request->input('survey_id'), $dateFilter);
            foreach ($demographicData as $index => $demo) {
                fputcsv($file, [
                    $index + 1,
                    $demo['jenis_kelamin'] ?? '-',
                    $demo['umur'] ?? '-',
                    $demo['pendidikan'] ?? '-',
                    $demo['unit_kerja'] ?? '-',
                    $demo['jabatan_fungsional'] ?? '-',
                    $demo['count']
                ]);
            }

            fputcsv($file, ['']);

            // 7.1. Chart Demografis - Jenis Kelamin
            fputcsv($file, ['7.1. CHART DEMOGRAFIS - JENIS KELAMIN']);
            fputcsv($file, ['Jenis Kelamin', 'Jumlah', 'Persentase']);
            $demographicChartData = $this->getDemographicChartData($request->input('survey_id'), $dateFilter);
            $totalDemographic = $demographicChartData['total_demographic_responses'];
            foreach ($demographicChartData['gender'] as $gender => $count) {
                $percentage = $totalDemographic > 0 ? round(($count / $totalDemographic) * 100, 1) : 0;
                fputcsv($file, [$gender, $count, $percentage . '%']);
            }

            fputcsv($file, ['']);

            // 7.2. Chart Demografis - Umur
            fputcsv($file, ['7.2. CHART DEMOGRAFIS - KELOMPOK UMUR']);
            fputcsv($file, ['Kelompok Umur', 'Jumlah', 'Persentase']);
            foreach ($demographicChartData['age'] as $age => $count) {
                $percentage = $totalDemographic > 0 ? round(($count / $totalDemographic) * 100, 1) : 0;
                fputcsv($file, [$age, $count, $percentage . '%']);
            }

            fputcsv($file, ['']);

            // 7.3. Chart Demografis - Pendidikan
            fputcsv($file, ['7.3. CHART DEMOGRAFIS - PENDIDIKAN TERAKHIR']);
            fputcsv($file, ['Pendidikan', 'Jumlah', 'Persentase']);
            foreach ($demographicChartData['education'] as $education => $count) {
                $percentage = $totalDemographic > 0 ? round(($count / $totalDemographic) * 100, 1) : 0;
                fputcsv($file, [$education, $count, $percentage . '%']);
            }

            fputcsv($file, ['']);

            // 7.4. Chart Demografis - Jabatan Fungsional
            fputcsv($file, ['7.4. CHART DEMOGRAFIS - JABATAN FUNGSIONAL']);
            fputcsv($file, ['Jabatan Fungsional', 'Jumlah', 'Persentase']);
            foreach ($demographicChartData['position'] as $position => $count) {
                $percentage = $totalDemographic > 0 ? round(($count / $totalDemographic) * 100, 1) : 0;
                fputcsv($file, [$position, $count, $percentage . '%']);
            }

            fputcsv($file, ['']);

            // 8. Informasi Pembatasan Survey
            fputcsv($file, ['8. INFORMASI PEMBATASAN SURVEY']);
            fputcsv($file, ['Deskripsi', 'Sistem pembatasan pengisian survey: 1 kali per hari per device']);
            fputcsv($file, ['Metode Tracking', 'IP Address + User Agent + Survey ID + Tanggal']);
            fputcsv($file, ['Tujuan', 'Mencegah duplikasi pengisian survey dari device yang sama']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getLevelText($level, $type)
    {
        if ($type === 'satisfaction') {
            return match($level) {
                1 => 'Tidak Puas',
                2 => 'Kurang Puas',
                3 => 'Puas',
                4 => 'Sangat Puas',
                default => 'Level ' . $level
            };
        } else {
            return match($level) {
                1 => 'Tidak Penting',
                2 => 'Kurang Penting',
                3 => 'Penting',
                4 => 'Sangat Penting',
                default => 'Level ' . $level
            };
        }
    }

    public function getDemographicData($surveyId, $dateFilter)
    {
        $query = Response::whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']])
            ->whereNotNull('jenis_kelamin');

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        return $query->selectRaw('
                jenis_kelamin, umur, pendidikan, unit_kerja, jabatan_fungsional,
                COUNT(DISTINCT session_id) as count
            ')
            ->groupBy('jenis_kelamin', 'umur', 'pendidikan', 'unit_kerja', 'jabatan_fungsional')
            ->orderBy('count', 'desc')
            ->limit(10) // Batasi hanya 10 baris
            ->get()
            ->toArray();
    }

    private function getDemographicChartData($surveyId, $dateFilter)
    {
        $query = Response::whereBetween('created_at', [$dateFilter['start'], $dateFilter['end']])
            ->whereNotNull('jenis_kelamin');

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
        }

        $responses = $query->get();

        // Data Jenis Kelamin - berdasarkan unique session_id
        $genderData = $responses->groupBy('session_id')->map(function($sessionResponses) {
            return $sessionResponses->first()->jenis_kelamin;
        })->countBy();

        // Data Umur - berdasarkan unique session_id
        $ageData = $responses->groupBy('session_id')->map(function($sessionResponses) {
            return $sessionResponses->first()->umur;
        })->countBy();

        // Data Pendidikan - berdasarkan unique session_id
        $educationData = $responses->groupBy('session_id')->map(function($sessionResponses) {
            return $sessionResponses->first()->pendidikan;
        })->countBy();

        // Data Unit Kerja - berdasarkan unique session_id
        $unitData = $responses->groupBy('session_id')->map(function($sessionResponses) {
            return $sessionResponses->first()->unit_kerja;
        })->countBy();

        // Data Jabatan Fungsional - berdasarkan unique session_id
        $positionData = $responses->groupBy('session_id')->map(function($sessionResponses) {
            return $sessionResponses->first()->jabatan_fungsional;
        })->countBy();

        return [
            'gender' => $genderData,
            'age' => $ageData,
            'education' => $educationData,
            'unit' => $unitData,
            'position' => $positionData,
            'total_demographic_responses' => $responses->groupBy('session_id')->count()
        ];
    }

    public function exportPdf(Request $request)
    {
        $surveyId = $request->input('survey_id');
        $period = $request->input('period', 'current');
        $year = $request->input('year', date('Y'));

        $dateFilter = $this->getDateFilter($period, $year);
        $data = $this->getAnalysisData($surveyId, $dateFilter);

        // Generate chart images
        $chartImages = $this->generateChartImages($data);

        // Generate demographic chart images
        $demographicChartImages = $this->generateDemographicChartImages($data);

        $pdf = Pdf::loadView('admin.survey-analysis.pdf', compact('data', 'period', 'year', 'chartImages', 'demographicChartImages'));

        $filename = 'survey_analysis_' . $period . '_' . $year . '_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    private function generateChartImages($data)
    {
        $chartImages = [];

        // Chart 1: Gap Analysis Chart
        $chartImages['gapAnalysis'] = $this->generateGapAnalysisChart($data);

        // Chart 2: Satisfaction Distribution Chart
        $chartImages['satisfaction'] = $this->generateSatisfactionChart($data);

        // Chart 3: Importance Distribution Chart
        $chartImages['importance'] = $this->generateImportanceChart($data);

        // Chart 4: Improvement Areas Chart
        $chartImages['improvement'] = $this->generateImprovementChart($data);

        // Chart 5: Question Analysis Chart
        $chartImages['questionAnalysis'] = $this->generateQuestionAnalysisChart($data);

        // Chart 6: Status Distribution Chart
        $chartImages['status'] = $this->generateStatusChart($data);

        return $chartImages;
    }

    private function generateGapAnalysisChart($data)
    {
        // Generate gap analysis chart image
        $chartData = [
            'labels' => $data['question_averages']->pluck('question_text')->toArray(),
            'satisfaction' => $data['question_averages']->pluck('satisfaction_avg')->toArray(),
            'importance' => $data['question_averages']->pluck('importance_avg')->toArray(),
        ];

        // For now, return a placeholder. In a real implementation, you would use a charting library
        // that can generate images server-side like Chart.js with canvas-to-image conversion
        return base64_encode('Gap Analysis Chart Placeholder');
    }

    private function generateSatisfactionChart($data)
    {
        $distribution = $data['satisfaction_distribution'] ?? [];
        return base64_encode('Satisfaction Distribution Chart Placeholder');
    }

    private function generateImportanceChart($data)
    {
        $distribution = $data['importance_distribution'] ?? [];
        return base64_encode('Importance Distribution Chart Placeholder');
    }

    private function generateImprovementChart($data)
    {
        $improvementAreas = $data['improvement_areas'] ?? [];
        return base64_encode('Improvement Areas Chart Placeholder');
    }

    private function generateQuestionAnalysisChart($data)
    {
        $questionAverages = $data['question_averages'] ?? [];
        return base64_encode('Question Analysis Chart Placeholder');
    }

    private function generateStatusChart($data)
    {
        $statusDistribution = $data['status_distribution'] ?? [];
        return base64_encode('Status Distribution Chart Placeholder');
    }

    private function generateDemographicChartImages($data)
    {
        $demographicChartImages = [];

        // Chart Demografis - Jenis Kelamin
        $demographicChartImages['gender'] = $this->generateGenderChart($data);

        // Chart Demografis - Umur
        $demographicChartImages['age'] = $this->generateAgeChart($data);

        // Chart Demografis - Pendidikan
        $demographicChartImages['education'] = $this->generateEducationChart($data);

        // Chart Demografis - Jabatan Fungsional
        $demographicChartImages['position'] = $this->generatePositionChart($data);

        return $demographicChartImages;
    }

    private function generateGenderChart($data)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            return null; // Will fallback to table in view
        }

        $genderData = $data['demographic_data']['gender'] ?? [];
        
        if (empty($genderData)) {
            return null;
        }

        try {
            // Generate simple chart using GD library
            $width = 400;
            $height = 300;
            $image = imagecreate($width, $height);
            
            if (!$image) {
                return null;
            }
            
            // Colors
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            $blue = imagecolorallocate($image, 59, 130, 246);
            $green = imagecolorallocate($image, 34, 197, 94);
            $red = imagecolorallocate($image, 239, 68, 68);
            $yellow = imagecolorallocate($image, 245, 158, 11);
            
            $colors = [$blue, $green, $red, $yellow];
            
            // Fill background
            imagefill($image, 0, 0, $white);
            
            // Calculate total
            $total = array_sum($genderData);
            if ($total == 0) {
                imagedestroy($image);
                return null;
            }
            
            // Draw pie chart
            $startAngle = 0;
            $colorIndex = 0;
            $centerX = $width / 2;
            $centerY = $height / 2;
            $radius = 100;
            
            foreach ($genderData as $gender => $count) {
                $percentage = ($count / $total) * 100;
                $angle = ($count / $total) * 360;
                
                $color = $colors[$colorIndex % count($colors)];
                
                // Draw pie slice
                imagefilledarc($image, $centerX, $centerY, $radius * 2, $radius * 2, 
                    $startAngle, $startAngle + $angle, $color, IMG_ARC_PIE);
                
                // Draw label
                $labelX = $centerX + cos(deg2rad($startAngle + $angle / 2)) * ($radius + 30);
                $labelY = $centerY + sin(deg2rad($startAngle + $angle / 2)) * ($radius + 30);
                
                imagestring($image, 2, $labelX - 20, $labelY - 5, 
                    $gender . ' (' . round($percentage, 1) . '%)', $black);
                
                $startAngle += $angle;
                $colorIndex++;
            }
            
            // Output as base64
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            return base64_encode($imageData);
        } catch (Exception $e) {
            return null; // Will fallback to table in view
        }
    }

    private function generateAgeChart($data)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            return null; // Will fallback to table in view
        }

        $ageData = $data['demographic_data']['age'] ?? [];
        
        if (empty($ageData)) {
            return null;
        }

        try {
            // Generate simple bar chart using GD library
            $width = 400;
            $height = 300;
            $image = imagecreate($width, $height);
            
            if (!$image) {
                return null;
            }
            
            // Colors
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            $blue = imagecolorallocate($image, 59, 130, 246);
            
            // Fill background
            imagefill($image, 0, 0, $white);
            
            // Calculate max value
            $maxValue = max($ageData);
            if ($maxValue == 0) {
                imagedestroy($image);
                return null;
            }
            
            // Draw bars
            $barWidth = ($width - 100) / count($ageData);
            $x = 50;
            
            foreach ($ageData as $age => $count) {
                $barHeight = ($count / $maxValue) * ($height - 80);
                $y = $height - 40 - $barHeight;
                
                // Draw bar
                imagefilledrectangle($image, $x, $y, $x + $barWidth - 5, $height - 40, $blue);
                
                // Draw label
                imagestring($image, 2, $x, $height - 30, $age, $black);
                imagestring($image, 2, $x, $y - 15, $count, $black);
                
                $x += $barWidth;
            }
            
            // Output as base64
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            return base64_encode($imageData);
        } catch (Exception $e) {
            return null; // Will fallback to table in view
        }
    }

    private function generateEducationChart($data)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            return null; // Will fallback to table in view
        }

        $educationData = $data['demographic_data']['education'] ?? [];
        
        if (empty($educationData)) {
            return null;
        }

        try {
            // Generate simple bar chart using GD library
            $width = 400;
            $height = 300;
            $image = imagecreate($width, $height);
            
            if (!$image) {
                return null;
            }
            
            // Colors
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            $green = imagecolorallocate($image, 34, 197, 94);
            
            // Fill background
            imagefill($image, 0, 0, $white);
            
            // Calculate max value
            $maxValue = max($educationData);
            if ($maxValue == 0) {
                imagedestroy($image);
                return null;
            }
            
            // Draw bars
            $barWidth = ($width - 100) / count($educationData);
            $x = 50;
            
            foreach ($educationData as $education => $count) {
                $barHeight = ($count / $maxValue) * ($height - 80);
                $y = $height - 40 - $barHeight;
                
                // Draw bar
                imagefilledrectangle($image, $x, $y, $x + $barWidth - 5, $height - 40, $green);
                
                // Draw label (shortened)
                $shortLabel = strlen($education) > 8 ? substr($education, 0, 8) . '...' : $education;
                imagestring($image, 2, $x, $height - 30, $shortLabel, $black);
                imagestring($image, 2, $x, $y - 15, $count, $black);
                
                $x += $barWidth;
            }
            
            // Output as base64
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            return base64_encode($imageData);
        } catch (Exception $e) {
            return null; // Will fallback to table in view
        }
    }

    private function generatePositionChart($data)
    {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            return null; // Will fallback to table in view
        }

        $positionData = $data['demographic_data']['position'] ?? [];
        
        if (empty($positionData)) {
            return null;
        }

        try {
            // Generate simple pie chart using GD library
            $width = 400;
            $height = 300;
            $image = imagecreate($width, $height);
            
            if (!$image) {
                return null;
            }
            
            // Colors
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            $blue = imagecolorallocate($image, 59, 130, 246);
            $green = imagecolorallocate($image, 34, 197, 94);
            $red = imagecolorallocate($image, 239, 68, 68);
            $yellow = imagecolorallocate($image, 245, 158, 11);
            
            $colors = [$blue, $green, $red, $yellow];
            
            // Fill background
            imagefill($image, 0, 0, $white);
            
            // Calculate total
            $total = array_sum($positionData);
            if ($total == 0) {
                imagedestroy($image);
                return null;
            }
            
            // Draw pie chart
            $startAngle = 0;
            $colorIndex = 0;
            $centerX = $width / 2;
            $centerY = $height / 2;
            $radius = 100;
            
            foreach ($positionData as $position => $count) {
                $percentage = ($count / $total) * 100;
                $angle = ($count / $total) * 360;
                
                $color = $colors[$colorIndex % count($colors)];
                
                // Draw pie slice
                imagefilledarc($image, $centerX, $centerY, $radius * 2, $radius * 2, 
                    $startAngle, $startAngle + $angle, $color, IMG_ARC_PIE);
                
                // Draw label
                $labelX = $centerX + cos(deg2rad($startAngle + $angle / 2)) * ($radius + 30);
                $labelY = $centerY + sin(deg2rad($startAngle + $angle / 2)) * ($radius + 30);
                
                $shortLabel = strlen($position) > 10 ? substr($position, 0, 10) . '...' : $position;
                imagestring($image, 2, $labelX - 20, $labelY - 5, 
                    $shortLabel . ' (' . round($percentage, 1) . '%)', $black);
                
                $startAngle += $angle;
                $colorIndex++;
            }
            
            // Output as base64
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);
            
            return base64_encode($imageData);
        } catch (Exception $e) {
            return null; // Will fallback to table in view
        }
    }
}
