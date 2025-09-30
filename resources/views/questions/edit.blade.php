@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-w-md mx-auto">
        <h1 class="text-xl font-bold mb-4">Edit Pertanyaan</h1>
        <form action="{{ route('questions.update', $question) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="survey_id" class="block font-semibold mb-1">Survei</label>
                <select name="survey_id" id="survey_id" class="form-input w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="">-- Pilih Survei --</option>
                    @foreach($surveys as $survey)
                        <option value="{{ $survey->id }}" {{ $question->survey_id == $survey->id ? 'selected' : '' }}>{{ $survey->title }}</option>
                    @endforeach
                </select>
                @error('survey_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="category_id" class="block font-semibold mb-1">Kategori</label>
                <select name="category_id" id="category_id" class="form-input w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $question->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="question_text" class="block font-semibold mb-1">Pertanyaan</label>
                <input type="text" name="question_text" id="question_text" class="form-input w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" value="{{ $question->question_text }}" required>
                @error('question_text')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4">
                <label for="type" class="block font-semibold mb-1">Tipe Jawaban</label>
                <select name="type" id="type" class="form-input w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required onchange="toggleOptions();">
                    <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Isian Bebas</option>
                    <option value="scale" {{ $question->type == 'scale' ? 'selected' : '' }}>Skala (1-4)</option>
                    <option value="choice" {{ $question->type == 'choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                </select>
                @error('type')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-4" id="optionsDiv" style="display:none;">
                <label for="options" class="block font-semibold mb-1">Opsi Pilihan (pisahkan dengan koma)</label>
                <textarea name="options" id="options" class="form-input w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ $question->options }}</textarea>
                @error('options')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex justify-between">
                <a href="{{ route('questions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded transition">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">Update</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleOptions() {
    var type = document.getElementById('type').value;
    document.getElementById('optionsDiv').style.display = (type === 'choice') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    toggleOptions();
    document.getElementById('type').addEventListener('change', toggleOptions);
});
</script>
@endsection
