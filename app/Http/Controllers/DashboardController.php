<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Force clear cache untuk memastikan data terbaru
        Cache::flush();

        // Tambahkan header untuk mencegah browser cache
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Sementara disable cache untuk debugging
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
        $filteredResponseIds = $responseQuery->pluck('id');

        // Hitung responden unik - setiap submit survey = 1 responden
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
        $totalRespondents = $responseQuery->distinct('session_id')->count('session_id');

        $totalQuestions = $surveyId ? Question::where('survey_id', $surveyId)->count() : Question::count();
        $totalCategories = Category::count();
        $totalResponses = Response::whereIn('id', $filteredResponseIds)->count();

        // Statistik per kategori
        $categories = Category::with(['questions' => function($q) use ($surveyId) {
            if ($surveyId) $q->where('survey_id', $surveyId);
        }, 'questions.responses' => function($q) use ($filteredResponseIds) {
            $q->whereIn('id', $filteredResponseIds);
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

        // Statistik Tingkat Kepuasan (Satisfaction) - Hanya 5 terbaru dengan pagination
        $satisfactionStats = Question::where('type', 'scale');
        if ($surveyId) {
            $satisfactionStats->where('survey_id', $surveyId);
        }
        if ($categoryId) {
            $satisfactionStats->where('category_id', $categoryId);
        }
        $satisfactionStats = $satisfactionStats->with(['responses' => function($q) use ($filteredResponseIds) {
            $q->whereIn('id', $filteredResponseIds)->whereNotNull('satisfaction');
        }])->orderBy('created_at', 'desc')->get();

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

        // Pagination untuk satisfaction data (5 per halaman)
        $currentPage = request()->get('satisfaction_page', 1);
        $perPage = 5;
        $satisfactionDataPaginated = $satisfactionData->forPage($currentPage, $perPage);
        $satisfactionDataPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $satisfactionDataPaginated,
            $satisfactionData->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'satisfaction_page']
        );

        // Statistik Tingkat Kepentingan (Importance) - Hanya 5 terbaru dengan pagination
        $importanceStats = Question::where('type', 'scale');
        if ($surveyId) {
            $importanceStats->where('survey_id', $surveyId);
        }
        if ($categoryId) {
            $importanceStats->where('category_id', $categoryId);
        }
        $importanceStats = $importanceStats->with(['responses' => function($q) use ($filteredResponseIds) {
            $q->whereIn('id', $filteredResponseIds)->whereNotNull('importance');
        }])->orderBy('created_at', 'desc')->get();

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

        // Pagination untuk importance data (5 per halaman)
        $currentPageImportance = request()->get('importance_page', 1);
        $perPageImportance = 5;
        $importanceDataPaginated = $importanceData->forPage($currentPageImportance, $perPageImportance);
        $importanceDataPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $importanceDataPaginated,
            $importanceData->count(),
            $perPageImportance,
            $currentPageImportance,
            ['path' => request()->url(), 'pageName' => 'importance_page']
        );

        // Gap Analysis (Kepentingan - Kepuasan)
        $gapAnalysis = [];
        foreach ($satisfactionData as $index => $satisfaction) {
            if (isset($importanceData[$index])) {
                $importance = $importanceData[$index];
                $gap = $importance['average'] - $satisfaction['average'];
                $gapAnalysis[] = [
                    'question' => $satisfaction['question'],
                    'satisfaction_avg' => $satisfaction['average'],
                    'importance_avg' => $importance['average'],
                    'gap' => round($gap, 2),
                    'status' => $this->getGapStatus($gap)
                ];
            }
        }

        // Rekap saran responden
        $suggestions = Response::whereNotNull('suggestion')
            ->where('suggestion', '!=', '')
            ->whereIn('id', $filteredResponseIds)
            ->select('suggestion', 'user_id', 'session_id', 'created_at')
            ->groupBy('session_id', 'suggestion', 'user_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($response) {
                $userType = $response->user_id ? 'User ID: ' . $response->user_id : 'Tamu';
                return [
                    'suggestion' => $response->suggestion,
                    'user_type' => $userType,
                    'date' => $response->created_at->format('d/m/Y H:i')
                ];
            });



        // Data untuk chart
        $chartData = [
            'categories' => $categoryStats->pluck('name'),
            'response_counts' => $categoryStats->pluck('response_count'),
            'satisfaction_averages' => $satisfactionData->pluck('average'),
            'importance_averages' => $importanceData->pluck('average'),
            'gap_values' => collect($gapAnalysis)->pluck('gap'),
            'questions' => $satisfactionData->pluck('question')
        ];

        return view('dashboard', compact(
            'totalRespondents',
            'totalQuestions',
            'totalCategories',
            'totalResponses',
            'categoryStats',
            'satisfactionData',
            'satisfactionDataPaginator',
            'importanceData',
            'importanceDataPaginator',
            'gapAnalysis',
            'chartData',
            'suggestions'
        ));
    }

    public function resetSurveyData()
    {
        try {
            // Log sebelum reset
            Log::info('Starting survey data reset. Current response count: ' . Response::count());

            // Hapus semua data response survey
            Response::truncate();

            // Clear semua cache
            Cache::flush();

            // Log setelah reset
            Log::info('Survey data reset completed. New response count: ' . Response::count());

            return redirect()->route('dashboard')->with('success', 'Data survey berhasil direset! Semua jawaban survey telah dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to reset survey data: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal mereset data survey: ' . $e->getMessage());
        }
    }

    private function getGapStatus($gap)
    {
        if ($gap <= 0) return 'Baik';
        if ($gap <= 0.5) return 'Cukup';
        if ($gap <= 1.0) return 'Kurang';
        return 'Sangat Kurang';
    }
}
