<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Category;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::with('category')->get();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('questions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'category_id' => 'required|exists:categories,id',
            'question_text' => 'required|string|max:255',
            'type' => 'required|in:text,scale,choice',
            'options' => 'nullable|string',
            'scale_label_type' => 'nullable|string',
        ]);
        Question::create([
            'survey_id' => $request->survey_id,
            'category_id' => $request->category_id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'options' => $request->type === 'choice' ? $request->options : null,
            'scale_label_type' => $request->type === 'scale' ? $request->scale_label_type : null,
        ]);
        return redirect()->route('questions.index')->with('success', 'Pertanyaan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        $categories = Category::all();
        $surveys = \App\Models\Survey::all();
        return view('questions.edit', compact('question', 'categories', 'surveys'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'category_id' => 'required|exists:categories,id',
            'question_text' => 'required|string|max:255',
            'type' => 'required|in:text,scale,choice',
            'options' => 'nullable|string',
            'scale_label_type' => 'nullable|string',
        ]);
        $question->update([
            'survey_id' => $request->survey_id,
            'category_id' => $request->category_id,
            'question_text' => $request->question_text,
            'type' => $request->type,
            'options' => $request->type === 'choice' ? $request->options : null,
            'scale_label_type' => $request->type === 'scale' ? $request->scale_label_type : null,
        ]);
        return redirect()->route('questions.index')->with('success', 'Pertanyaan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Pertanyaan berhasil dihapus');
    }
}
