@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-8 border-2 border-green-100">
            <h1 class="text-2xl font-extrabold mb-6 text-green-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Edit Kategori
            </h1>
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label for="name" class="block font-bold text-green-700 mb-2">Nama Kategori</label>
                    <input type="text" name="name" id="name" class="form-input w-full border-2 border-green-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" value="{{ $category->name }}" required autofocus>
                    @error('name')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="order" class="block font-bold text-green-700 mb-2">Urutan</label>
                    <input type="number" name="order" id="order" class="form-input w-full border-2 border-green-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" value="{{ $category->order }}" min="0">
                    <div class="text-gray-500 text-xs mt-1">Kategori dengan angka lebih kecil akan muncul lebih awal.</div>
                    @error('order')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex justify-between">
                    <a href="{{ route('categories.index') }}" class="bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 font-bold py-2 px-6 rounded-xl transition">Batal</a>
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-8 rounded-xl transition shadow-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
