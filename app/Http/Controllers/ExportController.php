<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Response;
use App\Models\User;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as HttpResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ResponsesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportCsv()
    {
        $filename = 'survey_results_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Header untuk CSV yang sesuai dengan form survey baru
            fputcsv($file, [
                'ID Responden',
                'Nama Responden',
                'Email Responden',
                'Jenis Kelamin',
                'Umur',
                'Pendidikan Terakhir',
                'Unit Kerja',
                'Jabatan Fungsional',
                'Kategori',
                'Pertanyaan',
                'Tipe Pertanyaan',
                'Tingkat Kepuasan',
                'Tingkat Kepentingan',
                'Saran',
                'IP Address',
                'User Agent',
                'Tanggal Jawaban'
            ]);

            // Ambil semua response dengan relasi
            $responses = Response::with(['user', 'question.category'])->get();

            foreach ($responses as $response) {
                fputcsv($file, [
                    $response->user_id ?? 'Anonim',
                    $response->user ? $response->user->name : 'Anonim',
                    $response->user ? $response->user->email : 'Anonim',
                    $response->jenis_kelamin ?? '-',
                    $response->umur ?? '-',
                    $response->pendidikan ?? '-',
                    $response->unit_kerja ?? '-',
                    $response->jabatan_fungsional ?? '-',
                    $response->question->category->name,
                    $response->question->question_text,
                    $response->question->type,
                    $response->satisfaction ?? '-',
                    $response->importance ?? '-',
                    $response->suggestion ?? '-',
                    $response->ip_address ?? '-',
                    $response->user_agent ?? '-',
                    $response->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return HttpResponse::stream($callback, 200, $headers);
    }

    public function exportSummary()
    {
        $filename = 'survey_summary_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Header untuk summary yang sesuai dengan form survey baru
            fputcsv($file, [
                'Kategori',
                'Pertanyaan',
                'Tipe Pertanyaan',
                'Total Jawaban',
                'Jawaban dengan Data Demografis',
                'Rata-rata Tingkat Kepuasan',
                'Rata-rata Tingkat Kepentingan',
                'Distribusi Kepuasan (1-4)',
                'Distribusi Kepentingan (1-4)',
                'Statistik Demografis (Jenis Kelamin)',
                'Statistik Demografis (Umur)',
                'Statistik Demografis (Pendidikan)',
                'Statistik Demografis (Unit Kerja)',
                'Statistik Demografis (Jabatan)'
            ]);

            $questions = Question::with(['category', 'responses'])->get();

            foreach ($questions as $question) {
                $responses = $question->responses;
                $totalResponses = $responses->count();

                $satisfactionAvg = 0;
                $importanceAvg = 0;
                $satisfactionDist = '';
                $importanceDist = '';

                if ($question->type === 'scale') {
                    // Rata-rata Tingkat Kepuasan
                    $satisfactionResponses = $responses->pluck('satisfaction')->filter(function($value) {
                        return is_numeric($value) && $value >= 1 && $value <= 4;
                    });
                    $satisfactionAvg = $satisfactionResponses->count() > 0 ? round($satisfactionResponses->avg(), 2) : 0;

                    // Distribusi Tingkat Kepuasan
                    $satisfactionDistArray = [];
                    for ($i = 1; $i <= 4; $i++) {
                        $count = $satisfactionResponses->filter(function($value) use ($i) {
                            return $value == $i;
                        })->count();
                        $satisfactionDistArray[] = "Skala $i: $count";
                    }
                    $satisfactionDist = implode(', ', $satisfactionDistArray);

                    // Rata-rata Tingkat Kepentingan
                    $importanceResponses = $responses->pluck('importance')->filter(function($value) {
                        return is_numeric($value) && $value >= 1 && $value <= 4;
                    });
                    $importanceAvg = $importanceResponses->count() > 0 ? round($importanceResponses->avg(), 2) : 0;

                    // Distribusi Tingkat Kepentingan
                    $importanceDistArray = [];
                    for ($i = 1; $i <= 4; $i++) {
                        $count = $importanceResponses->filter(function($value) use ($i) {
                            return $value == $i;
                        })->count();
                        $importanceDistArray[] = "Skala $i: $count";
                    }
                    $importanceDist = implode(', ', $importanceDistArray);
                } elseif ($question->type === 'choice') {
                    $options = json_decode($question->options, true) ?? [];
                    $dist = [];
                    foreach ($options as $option) {
                        $count = $responses->filter(function($response) use ($option) {
                            return $response->answer === $option;
                        })->count();
                        $dist[] = "$option: $count";
                    }
                    $satisfactionDist = implode(', ', $dist);
                    $importanceDist = '-';
                } else {
                    // Untuk text, hitung jawaban unik
                    $uniqueAnswers = $responses->pluck('answer')->unique();
                    $satisfactionDist = "Jawaban unik: " . $uniqueAnswers->count();
                    $importanceDist = '-';
                }

                // Statistik demografis untuk pertanyaan ini
                $demographicStats = [];

                // Jenis Kelamin
                $genderStats = $responses->pluck('jenis_kelamin')->filter()->countBy();
                $genderStatsStr = $genderStats->map(function($count, $gender) {
                    return "$gender: $count";
                })->implode(', ');
                $demographicStats[] = $genderStatsStr ?: 'Tidak ada data demografis';

                // Umur
                $ageStats = $responses->pluck('umur')->filter()->countBy();
                $ageStatsStr = $ageStats->map(function($count, $age) {
                    return "$age: $count";
                })->implode(', ');
                $demographicStats[] = $ageStatsStr ?: 'Tidak ada data demografis';

                // Pendidikan
                $educationStats = $responses->pluck('pendidikan')->filter()->countBy();
                $educationStatsStr = $educationStats->map(function($count, $education) {
                    return "$education: $count";
                })->implode(', ');
                $demographicStats[] = $educationStatsStr ?: 'Tidak ada data demografis';

                // Unit Kerja
                $unitStats = $responses->pluck('unit_kerja')->filter()->countBy();
                $unitStatsStr = $unitStats->map(function($count, $unit) {
                    return "$unit: $count";
                })->implode(', ');
                $demographicStats[] = $unitStatsStr ?: 'Tidak ada data demografis';

                // Jabatan
                $positionStats = $responses->pluck('jabatan_fungsional')->filter()->countBy();
                $positionStatsStr = $positionStats->map(function($count, $position) {
                    return "$position: $count";
                })->implode(', ');
                $demographicStats[] = $positionStatsStr ?: 'Tidak ada data demografis';

                // Hitung jumlah responses dengan data demografis
                $responsesWithDemographics = $responses->whereNotNull('jenis_kelamin')->count();

                fputcsv($file, [
                    $question->category->name,
                    $question->question_text,
                    $question->type,
                    $totalResponses,
                    $responsesWithDemographics,
                    $satisfactionAvg,
                    $importanceAvg,
                    $satisfactionDist,
                    $importanceDist,
                    $demographicStats[0],
                    $demographicStats[1],
                    $demographicStats[2],
                    $demographicStats[3],
                    $demographicStats[4]
                ]);
            }

            fclose($file);
        };

        return HttpResponse::stream($callback, 200, $headers);
    }

    public function exportPdfDashboard(Request $request)
    {
        // Ambil filter dari request
        $surveyId = $request->input('survey_id');
        $categoryId = $request->input('category_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Filter responses by date & survey
        $responseQuery = Response::query();
        if ($surveyId) {
            $responseQuery->where('survey_id', $surveyId);
        }
        if ($startDate) {
            $responseQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $responseQuery->whereDate('created_at', '<=', $endDate);
        }

        // Hitung responden unik berdasarkan session_id
        $totalRespondents = $responseQuery->distinct('session_id')->count('session_id');

        $totalQuestions = $surveyId ? Question::where('survey_id', $surveyId)->count() : Question::count();
        $totalCategories = Category::count();
        $totalResponses = Response::whereIn('id', $responseQuery->pluck('id'))->count();

        $categories = Category::with(['questions' => function($q) use ($surveyId) {
            if ($surveyId) $q->where('survey_id', $surveyId);
        }, 'questions.responses' => function($q) use ($responseQuery) {
            $q->whereIn('id', $responseQuery->pluck('id'));
        }]);
        if ($categoryId) {
            $categories->where('id', $categoryId);
        }
        $categoryStats = $categories->get()->map(function ($category) {
            $questionCount = $category->questions->count();
            $responseCount = $category->questions->sum(function ($question) {
                return $question->responses->count();
            });
            return [
                'name' => $category->name,
                'question_count' => $questionCount,
                'response_count' => $responseCount,
                'average_responses' => $questionCount > 0 ? round($responseCount / $questionCount, 2) : 0
            ];
        });

        // Statistik Tingkat Kepuasan (Satisfaction)
        $satisfactionStats = Question::where('type', 'scale');
        if ($surveyId) {
            $satisfactionStats->where('survey_id', $surveyId);
        }
        if ($categoryId) {
            $satisfactionStats->where('category_id', $categoryId);
        }
        $satisfactionStats = $satisfactionStats->with(['responses' => function($q) use ($responseQuery) {
            $q->whereIn('id', $responseQuery->pluck('id'))->whereNotNull('satisfaction');
        }])->get();
        $satisfactionData = $satisfactionStats->map(function ($question) {
            $responses = $question->responses->pluck('satisfaction')->filter(function ($value) {
                return is_numeric($value) && $value >= 1 && $value <= 4;
            });
            $average = $responses->count() > 0 ? round($responses->avg(), 2) : 0;
            $distribution = [];
            for ($i = 1; $i <= 4; $i++) {
                $distribution[$i] = $responses->filter(function ($value) use ($i) {
                    return $value == $i;
                })->count();
            }
            return [
                'question' => $question->question_text,
                'average' => $average,
                'total_responses' => $responses->count(),
                'distribution' => $distribution
            ];
        });

        // Statistik Tingkat Kepentingan (Importance)
        $importanceStats = Question::where('type', 'scale');
        if ($surveyId) {
            $importanceStats->where('survey_id', $surveyId);
        }
        if ($categoryId) {
            $importanceStats->where('category_id', $categoryId);
        }
        $importanceStats = $importanceStats->with(['responses' => function($q) use ($responseQuery) {
            $q->whereIn('id', $responseQuery->pluck('id'))->whereNotNull('importance');
        }])->get();
        $importanceData = $importanceStats->map(function ($question) {
            $responses = $question->responses->pluck('importance')->filter(function ($value) {
                return is_numeric($value) && $value >= 1 && $value <= 4;
            });
            $average = $responses->count() > 0 ? round($responses->avg(), 2) : 0;
            $distribution = [];
            for ($i = 1; $i <= 4; $i++) {
                $distribution[$i] = $responses->filter(function ($value) use ($i) {
                    return $value == $i;
                })->count();
            }
            return [
                'question' => $question->question_text,
                'average' => $average,
                'total_responses' => $responses->count(),
                'distribution' => $distribution
            ];
        });

        $choiceQuestions = Question::where('type', 'choice');
        if ($surveyId) {
            $choiceQuestions->where('survey_id', $surveyId);
        }
        if ($categoryId) {
            $choiceQuestions->where('category_id', $categoryId);
        }
        $choiceQuestions = $choiceQuestions->with(['responses' => function($q) use ($responseQuery) {
            $q->whereIn('id', $responseQuery->pluck('id'));
        }])->get();
        $choiceStats = $choiceQuestions->map(function ($question) {
            $options = json_decode($question->options, true) ?? [];
            $responses = $question->responses->pluck('answer');
            $distribution = [];
            foreach ($options as $option) {
                $distribution[$option] = $responses->filter(function ($value) use ($option) {
                    return $value === $option;
                })->count();
            }
            return [
                'question' => $question->question_text,
                'options' => $options,
                'distribution' => $distribution,
                'total_responses' => $responses->count()
            ];
        });

        $barChartImg = $request->input('bar_chart');
        $lineChartImg = $request->input('line_chart');
        $pdf = Pdf::loadView('admin.surveys.pdf_dashboard', [
            'totalRespondents' => $totalRespondents,
            'totalQuestions' => $totalQuestions,
            'totalCategories' => $totalCategories,
            'totalResponses' => $totalResponses,
            'categoryStats' => $categoryStats,
            'satisfactionData' => $satisfactionData,
            'importanceData' => $importanceData,
            'choiceStats' => $choiceStats,
            'filterSurvey' => $surveyId ? Survey::find($surveyId) : null,
            'barChartImg' => $barChartImg,
            'lineChartImg' => $lineChartImg,
        ]);
        $filename = 'rekap_survei_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportPdfDocumentation()
    {
        $pdf = Pdf::loadView('admin.documentation');
        $filename = 'dokumentasi_admin_survey.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel()
    {
        $filename = 'data_responden_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ResponsesExport, $filename);
    }
}
