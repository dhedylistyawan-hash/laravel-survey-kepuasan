@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 dark:text-gray-100 border dark:border-gray-700 shadow-2xl rounded-2xl p-8 border-2 border-green-100">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-extrabold text-green-800 flex items-center gap-2">
                    <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Daftar Pertanyaan
                </h1>
                <a href="{{ route('questions.create') }}" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-5 rounded-xl transition shadow">Tambah Pertanyaan</a>
            </div>
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded font-semibold">{{ session('success') }}</div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900 dark:text-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pertanyaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 dark:text-gray-100 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($questions as $question)
                        <tr class="hover:bg-green-50">
                            <td class="px-4 py-2 border text-center font-semibold">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border">{{ $question->category->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $question->question_text }}</td>
                            <td class="px-4 py-2 border text-center">{{ $question->type }}</td>
                            <td class="px-4 py-2 border text-center" style="min-width: 160px;">
                                <a href="{{ route('questions.edit', $question) }}" class="bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-1 px-4 rounded-xl mr-2 transition shadow">Edit</a>
                                <form action="{{ route('questions.destroy', $question) }}" method="POST" style="display:inline-block; margin-left: 2px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 text-white font-bold py-1 px-4 rounded-xl transition shadow" onclick="return confirm('Yakin hapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
