@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Edit Survei</h1>
            <form action="{{ route('admin.surveys.update', $survey) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Survei <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" class="form-input w-full border-gray-300 rounded" value="{{ old('title', $survey->title) }}" required>
                    @error('title')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="form-input w-full border-gray-300 rounded">{{ old('description', $survey->description) }}</textarea>
                    @error('description')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-input w-full border-gray-300 rounded" value="{{ old('start_date', $survey->start_date) }}">
                        @error('start_date')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex-1">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-input w-full border-gray-300 rounded" value="{{ old('end_date', $survey->end_date) }}">
                        @error('end_date')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="form-select w-full border-gray-300 rounded" required>
                        <option value="draft" {{ old('status', $survey->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $survey->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ old('status', $survey->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="flex justify-between mt-6">
                    <a href="{{ route('admin.surveys.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded transition">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded transition">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
