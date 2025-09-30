@extends('layouts.app')

@section('content')
<div id="dashboard-loading" class="fixed inset-0 bg-white/80 dark:bg-gray-900/80 hidden items-center justify-center z-50">
    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-green-500 border-solid"></div>
</div>
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-2xl border-2 border-green-100 dark:border-gray-700">
            <div class="p-8 text-gray-900 dark:text-gray-100">
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                <!-- Filter Form -->
                <form method="GET" action="{{ route('dashboard') }}" class="mb-8 flex flex-wrap gap-4 items-end bg-green-50 dark:bg-gray-900 p-4 rounded-xl border border-green-200 dark:border-green-700">
                    <div>
                        <label for="filter_survey_id" class="block text-sm font-bold text-green-700 dark:text-gray-200 mb-1">Survei</label>
                        <select name="survey_id" id="filter_survey_id" class="form-select rounded-lg border-2 border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition bg-white dark:bg-white text-gray-900">
                            <option value="">Semua Survei</option>
                            @foreach(\App\Models\Survey::all() as $survey)
                                <option value="{{ $survey->id }}" {{ request('survey_id') == $survey->id ? 'selected' : '' }}>{{ $survey->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-bold text-green-700 dark:text-gray-200 mb-1">Kategori</label>
                        <select name="category_id" id="category_id" class="form-select rounded-lg border-2 border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition bg-white dark:bg-white text-gray-900">
                            <option value="">Semua Kategori</option>
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-bold text-green-700 dark:text-gray-200 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-input rounded-lg border-2 border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition bg-white dark:bg-white text-gray-900" value="{{ request('start_date') }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-bold text-green-700 dark:text-gray-200 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-input rounded-lg border-2 border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 transition bg-white dark:bg-white text-gray-900" value="{{ request('end_date') }}">
                    </div>
                    <div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>
                </form>
                
                <div class="flex flex-wrap gap-3 mb-8">
                    <a href="{{ route('export.pdf.dashboard', request()->query()) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Cetak PDF
                    </a>
                    
                    <a href="{{ route('export.csv', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Data Lengkap
                    </a>
                    
                    <a href="{{ route('export.summary', request()->query()) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Export Summary
                    </a>
                    
                    <button type="button" onclick="confirmReset()" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Data Survey
                    </button>
                </div>

                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-extrabold text-green-800 dark:text-green-300 flex items-center gap-2">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Dashboard Statistik Survei
                    </h1>
                    @if(Auth::user()->role === 'admin')
                    <div class="flex space-x-4">
                        {{--
                        <a href="{{ route('export.excel') }}" class="bg-gradient-to-r from-blue-400 to-green-500 hover:from-blue-500 hover:to-green-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                            Export Excel
                        </a>
                        --}}
                    </div>
                    @endif
                </div>

                <!-- Statistik Umum -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-xl border-2 border-blue-100 dark:border-blue-700 shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-300">Total Responden</p>
                                <p class="text-2xl font-extrabold text-blue-900 dark:text-blue-100">{{ $totalRespondents }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900 p-6 rounded-xl border-2 border-green-100 dark:border-green-700 shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-bold text-green-600 dark:text-green-300">Total Pertanyaan</p>
                                <p class="text-2xl font-extrabold text-green-900 dark:text-green-100">{{ $totalQuestions }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900 p-6 rounded-xl border-2 border-yellow-100 dark:border-yellow-700 shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-bold text-yellow-600 dark:text-yellow-300">Total Kategori</p>
                                <p class="text-2xl font-extrabold text-yellow-900 dark:text-yellow-100">{{ $totalCategories }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-xl border-2 border-purple-100 dark:border-purple-700 shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-bold text-purple-600 dark:text-purple-300">Total Jawaban</p>
                                <p class="text-2xl font-extrabold text-purple-900 dark:text-purple-100">{{ $totalResponses }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Rekap Penilaian Keseluruhan -->

                <!-- Daftar Saran -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Pembatasan Survey</h3>
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span><strong>Sistem Pembatasan:</strong> Setiap device/komputer hanya dapat mengisi survey 1 kali per hari untuk survey yang sama. Pembatasan berdasarkan kombinasi IP Address dan User Agent.</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Daftar Saran Responden</h3>
                    @if($suggestions->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($suggestions as $saran)
                                <div class="border-l-4 border-green-500 pl-4 py-3 bg-green-50 dark:bg-green-900/20 rounded-r-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                    <div class="text-gray-800 dark:text-gray-100 font-medium mb-2">{{ $saran['suggestion'] }}</div>
                                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $saran['user_type'] == 'Tamu' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $saran['user_type'] }}
                                            </span>
                                            <span class="text-gray-500">•</span>
                                            <span>{{ $saran['date'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-sm text-gray-500 text-center">
                            Total: {{ $suggestions->count() }} saran
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-lg mb-2">📝</div>
                            <div class="text-gray-500">Belum ada saran yang masuk dari responden.</div>
                            <div class="text-gray-400 text-sm mt-1">Saran akan muncul di sini setelah responden mengisi form survey.</div>
                        </div>
                    @endif
                </div>

                <!-- Chart -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Jumlah Jawaban per Kategori</h3>
                        <canvas id="categoryChart" width="400" height="200"></canvas>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow" style="height: 350px;">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Gap Analysis (Kepentingan vs Kepuasan)</h3>
                        <canvas id="gapChart"></canvas>
                    </div>
                </div>

                <!-- Chart Tambahan -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow" style="height: 350px;">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rata-rata Tingkat Kepuasan per Pertanyaan</h3>
                        <canvas id="satisfactionChart"></canvas>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow" style="height: 350px;">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rata-rata Tingkat Kepentingan per Pertanyaan</h3>
                        <canvas id="importanceChart"></canvas>
                    </div>
                </div>

                <!-- Gap Analysis Table -->
                @if(count($gapAnalysis) > 0)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Analisis Gap (Kepentingan - Kepuasan)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Kepuasan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Kepentingan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($gapAnalysis as $gap)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($gap['question'], 50) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $gap['satisfaction_avg'] }}/4</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $gap['importance_avg'] }}/4</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $gap['gap'] }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($gap['status'] == 'Baik')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                                        @elseif($gap['status'] == 'Cukup')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Cukup</span>
                                        @elseif($gap['status'] == 'Kurang')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Kurang</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Sangat Kurang</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Statistik Detail -->
                <div class="space-y-8">
                    <!-- Statistik Tingkat Kepuasan -->
                    @if($satisfactionDataPaginator->count() > 0)
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Statistik Tingkat Kepuasan</h3>
                        <div class="space-y-4">
                            @foreach($satisfactionDataPaginator as $stat)
                            <div class="border rounded-lg p-4 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">{{ Str::limit($stat['question'], 80) }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-300">Rata-rata:</span>
                                        <span class="font-semibold text-green-600">{{ $stat['average'] }}/4</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-300">Total jawaban:</span>
                                        <span class="font-semibold text-blue-600">{{ $stat['total_responses'] }}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Distribusi jawaban:</p>
                                    <div class="flex space-x-4 text-xs">
                                        @foreach($stat['distribution'] as $scale => $count)
                                        <div class="text-center">
                                            <div class="font-semibold">{{ $scale }}</div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ $count }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($satisfactionDataPaginator->hasPages())
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Menampilkan {{ $satisfactionDataPaginator->firstItem() ?? 0 }} - {{ $satisfactionDataPaginator->lastItem() ?? 0 }} dari {{ $satisfactionDataPaginator->total() }} data
                            </div>
                            <div class="flex space-x-2">
                                @if($satisfactionDataPaginator->onFirstPage())
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Sebelumnya</span>
                                @else
                                    <a href="{{ $satisfactionDataPaginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">Sebelumnya</a>
                                @endif

                                @foreach($satisfactionDataPaginator->getUrlRange(1, $satisfactionDataPaginator->lastPage()) as $page => $url)
                                    @if($page == $satisfactionDataPaginator->currentPage())
                                        <span class="px-3 py-2 text-sm text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($satisfactionDataPaginator->hasMorePages())
                                    <a href="{{ $satisfactionDataPaginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">Selanjutnya</a>
                                @else
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Selanjutnya</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Statistik Tingkat Kepentingan -->
                    @if($importanceDataPaginator->count() > 0)
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Statistik Tingkat Kepentingan</h3>
                        <div class="space-y-4">
                            @foreach($importanceDataPaginator as $stat)
                            <div class="border rounded-lg p-4 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">{{ Str::limit($stat['question'], 80) }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-300">Rata-rata:</span>
                                        <span class="font-semibold text-blue-600">{{ $stat['average'] }}/4</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-300">Total jawaban:</span>
                                        <span class="font-semibold text-purple-600">{{ $stat['total_responses'] }}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Distribusi jawaban:</p>
                                    <div class="flex space-x-4 text-xs">
                                        @foreach($stat['distribution'] as $scale => $count)
                                        <div class="text-center">
                                            <div class="font-semibold">{{ $scale }}</div>
                                            <div class="text-gray-500 dark:text-gray-400">{{ $count }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($importanceDataPaginator->hasPages())
                        <div class="mt-6 flex items-center justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Menampilkan {{ $importanceDataPaginator->firstItem() ?? 0 }} - {{ $importanceDataPaginator->lastItem() ?? 0 }} dari {{ $importanceDataPaginator->total() }} data
                            </div>
                            <div class="flex space-x-2">
                                @if($importanceDataPaginator->onFirstPage())
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Sebelumnya</span>
                                @else
                                    <a href="{{ $importanceDataPaginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">Sebelumnya</a>
                                @endif

                                @foreach($importanceDataPaginator->getUrlRange(1, $importanceDataPaginator->lastPage()) as $page => $url)
                                    @if($page == $importanceDataPaginator->currentPage())
                                        <span class="px-3 py-2 text-sm text-white bg-blue-600 rounded-md">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($importanceDataPaginator->hasMorePages())
                                    <a href="{{ $importanceDataPaginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">Selanjutnya</a>
                                @else
                                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">Selanjutnya</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif



                    <!-- Statistik per Kategori -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik per Kategori</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pertanyaan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jawaban</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Jawaban</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($categoryStats as $stat)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['question_count'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['response_count'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['average_responses'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Loading overlay logic
    const loading = document.getElementById('dashboard-loading');
    const form = document.querySelector('form[action="{{ route('dashboard') }}"]');
    if(form) {
        form.addEventListener('submit', function() {
            loading.classList.remove('hidden');
        });
    }

    // Chart untuk kategori
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData['categories']),
            datasets: [{
                label: 'Jumlah Jawaban',
                data: @json($chartData['response_counts']),
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart untuk Gap Analysis
    const gapCtx = document.getElementById('gapChart').getContext('2d');
    new Chart(gapCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData['questions']),
            datasets: [{
                label: 'Tingkat Kepuasan',
                data: @json($chartData['satisfaction_averages']),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }, {
                label: 'Tingkat Kepentingan',
                data: @json($chartData['importance_averages']),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        font: { size: 14 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        callback: function(value, index, values) {
                            let label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substr(0, 20) + '…' : label;
                        },
                        maxRotation: 45,
                        minRotation: 45,
                        font: { size: 10 }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 4,
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    // Chart untuk Tingkat Kepuasan
    const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
    new Chart(satisfactionCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['questions']),
            datasets: [{
                label: 'Rata-rata Tingkat Kepuasan',
                data: @json($chartData['satisfaction_averages']),
                borderColor: 'rgba(34, 197, 94, 1)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderWidth: 2,
                fill: true,
                pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        font: { size: 14 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        callback: function(value, index, values) {
                            let label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substr(0, 20) + '…' : label;
                        },
                        maxRotation: 45,
                        minRotation: 45,
                        font: { size: 10 }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 4,
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    // Chart untuk Tingkat Kepentingan
    const importanceCtx = document.getElementById('importanceChart').getContext('2d');
    new Chart(importanceCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['questions']),
            datasets: [{
                label: 'Rata-rata Tingkat Kepentingan',
                data: @json($chartData['importance_averages']),
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        font: { size: 14 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        callback: function(value, index, values) {
                            let label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substr(0, 20) + '…' : label;
                        },
                        maxRotation: 45,
                        minRotation: 45,
                        font: { size: 10 }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 4,
                    ticks: {
                        stepSize: 1,
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    console.log('Script PDF export loaded');
    const pdfForm = document.getElementById('pdfExportForm');
    if (pdfForm) {
        pdfForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const barChart = document.getElementById('categoryChart');
            const gapChart = document.getElementById('gapChart');
            if (barChart && gapChart) {
                document.getElementById('bar_chart_img').value = barChart.toDataURL('image/png');
                document.getElementById('line_chart_img').value = gapChart.toDataURL('image/png');
                pdfForm.submit();
            }
        });
    }
});

function confirmReset() {
    if (confirm('⚠️ PERHATIAN! \n\nAnda akan menghapus SEMUA data jawaban survey. \n\nData yang akan dihapus:\n• Semua jawaban survey\n• Semua saran responden\n• Semua data demografis\n\nTindakan ini TIDAK DAPAT DIBATALKAN!\n\nApakah Anda yakin ingin melanjutkan?')) {
        window.location.href = '{{ route('dashboard.reset-survey-data') }}';
    }
}
</script>
@endsection
