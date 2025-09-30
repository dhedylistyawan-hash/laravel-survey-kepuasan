<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Survey;
use App\Models\Response;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $surveys = Survey::where('status', 'active')->orderBy('title')->get();
        $selectedSurveyId = $request->input('survey_id');
        $selectedSurvey = $selectedSurveyId ? Survey::find($selectedSurveyId) : null;
        $categories = $selectedSurvey
            ? \App\Models\Category::with(['questions' => function($q) use ($selectedSurveyId) {
                $q->where('survey_id', $selectedSurveyId);
            }])->orderBy('order')->get()
            : collect();
        return view('survey.index', compact('categories', 'surveys', 'selectedSurveyId'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'jenis_kelamin' => 'required|string',
            'umur' => 'required|string',
            'pendidikan' => 'required|string',
            'unit_kerja' => 'required|string',
            'jabatan_fungsional' => 'required|string',
            'importance' => 'required|array',
            'satisfaction' => 'required|array',
            'suggestion' => 'nullable|string',
        ]);

        // Cek apakah sudah ada pengisian hari ini dari device yang sama
        $today = Carbon::today();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Cek duplikasi berdasarkan IP + User Agent + Survey ID + Tanggal
        $existingResponse = Response::where('survey_id', $request->survey_id)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->whereDate('created_at', $today)
            ->first();

        if ($existingResponse) {
            return redirect()->route('survey.index', ['survey_id' => $request->survey_id])
                ->with('error', 'Anda sudah mengisi survey ini hari ini. Silakan coba lagi besok.');
        }

        $userId = auth()->check() ? auth()->id() : null;
        $sessionId = (string) Str::uuid();

        foreach ($request->importance as $questionId => $importance) {
            $satisfaction = $request->satisfaction[$questionId] ?? null;
            if ($satisfaction) {
                $data = [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'question_id' => $questionId,
                    'survey_id' => $request->survey_id,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'umur' => $request->umur,
                    'pendidikan' => $request->pendidikan,
                    'unit_kerja' => $request->unit_kerja,
                    'jabatan_fungsional' => $request->jabatan_fungsional,
                    'importance' => $importance,
                    'satisfaction' => $satisfaction,
                    'suggestion' => $request->suggestion,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ];

                Response::create($data);
            }
        }
        return redirect()->route('survey.index', ['survey_id' => $request->survey_id])->with('success', 'Terima kasih sudah mengisi survei!');
    }
}
