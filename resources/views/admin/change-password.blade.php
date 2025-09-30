@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white shadow-2xl rounded-2xl p-8 border-2 border-green-100">
            <h1 class="text-2xl font-extrabold mb-6 text-green-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ganti Password Admin
            </h1>
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded font-semibold">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded font-semibold">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.change-password') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="current_password" class="block font-bold text-green-700 mb-2">Password Lama</label>
                    <input type="password" name="current_password" id="current_password" class="form-input w-full border-2 border-green-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required autofocus>
                </div>
                <div class="mb-6">
                    <label for="admin-change-password" class="block font-bold text-green-700 mb-2">Password Baru</label>
                    <input type="password" name="password" id="admin-change-password" class="form-input w-full border-2 border-green-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                </div>
                <div class="mb-6">
                    <label for="password_confirmation" class="block font-bold text-green-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input w-full border-2 border-green-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition" required>
                </div>
                <div class="flex justify-between">
                    <a href="/dashboard" class="bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 font-bold py-2 px-6 rounded-xl transition">Batal</a>
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-8 rounded-xl transition shadow-lg">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
