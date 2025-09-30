@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-blue-800 mb-2">Analisis Survey Kepuasan</h1>
                    <p class="text-gray-600">Pengolahan data survey sesuai periode dan analisis gap</p>
                </div>
                                <div class="flex gap-4">
                    <a href="{{ route('survey-analysis.export', request()->query()) }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('survey-analysis.export-pdf', request()->query()) }}"
                       class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Survey</label>
                    <select name="survey_id" class="w-full border-2 border-blue-200 rounded-lg px-3 py-2 focus:border-blue-500">
                        <option value="">Semua Survey</option>
                        @foreach(\App\Models\Survey::all() as $survey)
                            <option value="{{ $survey->id }}" {{ request('survey_id') == $survey->id ? 'selected' : '' }}>
                                {{ $survey->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Periode</label>
                    <select name="period" class="w-full border-2 border-blue-200 rounded-lg px-3 py-2 focus:border-blue-500">
                        <option value="current" {{ $period == 'current' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="jan-jun" {{ $period == 'jan-jun' ? 'selected' : '' }}>Januari - Juni</option>
                        <option value="jul-dec" {{ $period == 'jul-dec' ? 'selected' : '' }}>Juli - Desember</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                    <select name="year" class="w-full border-2 border-blue-200 rounded-lg px-3 py-2 focus:border-blue-500">
                        @for($y = date('Y'); $y >= date('Y')-5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Filter Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Chart 1: Overview Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-blue-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    1. Periode Penilaian
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="font-semibold text-gray-700">Periode:</span>
                        <span class="text-blue-800">{{ $data['period']['start']->format('d/m/Y') }} - {{ $data['period']['end']->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="font-semibold text-gray-700">Jumlah Responden:</span>
                        <span class="text-green-800 text-xl font-bold">{{ number_format($data['total_respondents']) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-blue-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    2. Rata-rata Keseluruhan
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="font-semibold text-gray-700">Tingkat Kepuasan:</span>
                        <span class="text-green-800 text-xl font-bold">{{ $data['overall_averages']['overall_satisfaction'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="font-semibold text-gray-700">Tingkat Kepentingan:</span>
                        <span class="text-blue-800 text-xl font-bold">{{ $data['overall_averages']['overall_importance'] }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 {{ $data['overall_averages']['overall_gap'] > 0 ? 'bg-red-50' : 'bg-green-50' }} rounded-lg">
                        <span class="font-semibold text-gray-700">Gap:</span>
                        <span class="{{ $data['overall_averages']['overall_gap'] > 0 ? 'text-red-800' : 'text-green-800' }} text-xl font-bold">
                            {{ $data['overall_averages']['overall_gap'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Gap Analysis Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Gap Analysis Chart
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart Area -->
                <div class="lg:col-span-2">
                    <div class="h-96">
                        <canvas id="gapAnalysisChart"></canvas>
                    </div>
                </div>
                
                <!-- Penjelasan Chart -->
                <div class="lg:col-span-1">
                    <div class="bg-blue-50 rounded-lg p-4 h-96 overflow-y-auto">
                        <h3 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Penjelasan Chart
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-green-500 rounded"></div>
                                <span><strong>Hijau:</strong> Tingkat Kepuasan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                <span><strong>Biru:</strong> Tingkat Kepentingan</span>
                            </div>
                            <div class="border-t pt-3">
                                <p class="font-semibold text-gray-700 mb-2">Interpretasi Gap:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• <strong>Gap Positif:</strong> Kepentingan > Kepuasan (Perlu Perbaikan)</li>
                                    <li>• <strong>Gap Nol:</strong> Kepentingan = Kepuasan (Baik)</li>
                                    <li>• <strong>Gap Negatif:</strong> Kepentingan < Kepuasan (Sangat Baik)</li>
                                </ul>
                            </div>
                            <div class="border-t pt-3">
                                <p class="font-semibold text-gray-700 mb-2">Skala Penilaian:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• <strong>1.0 - 1.5:</strong> Sangat Rendah</li>
                                    <li>• <strong>1.6 - 2.5:</strong> Rendah</li>
                                    <li>• <strong>2.6 - 3.5:</strong> Sedang</li>
                                    <li>• <strong>3.6 - 4.0:</strong> Tinggi</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Distribution Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-bold text-green-800 mb-4">Distribusi Tingkat Kepuasan</h3>
                <div class="h-64">
                    <canvas id="satisfactionChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-bold text-blue-800 mb-4">Distribusi Tingkat Kepentingan</h3>
                <div class="h-64">
                    <canvas id="importanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 4: Top Improvement Areas -->
        @if($data['improvement_areas'] instanceof \Illuminate\Support\Collection && $data['improvement_areas']->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-red-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Top 10 Area yang Perlu Perbaikan
            </h2>
            <div class="overflow-x-auto mb-6">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-red-50">
                            <th class="border border-gray-300 px-4 py-2 text-center">No</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Pertanyaan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Kategori</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Rata-rata Kepuasan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Rata-rata Kepentingan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Gap</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['improvement_areas'] as $i => $area)
                        <tr class="hover:bg-red-50">
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $i+1 }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $area['question_text'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $area['category'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $area['satisfaction_avg'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $area['importance_avg'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center font-bold text-red-600">{{ $area['gap'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    {{ $area['gap_status'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($data['improvement_areas']->count() > 1)
            <div class="h-96">
                <canvas id="improvementChart"></canvas>
            </div>
            @endif
        </div>
        @endif

        <!-- Chart 5: Question Analysis -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Analisis Per Pertanyaan
            </h2>
            <div class="h-96">
                <canvas id="questionAnalysisChart"></canvas>
            </div>
        </div>

        <!-- Chart 6: Status Distribution -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Distribusi Status Perbaikan
            </h2>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Chart 7: Demografis Responden -->
        @if($data['demographic_data']['total_demographic_responses'] > 0)
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Profil Demografis Responden
            </h2>
            
            <!-- Grid Chart Demografis -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Chart Jenis Kelamin -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 text-center">Jenis Kelamin</h3>
                    <div class="h-48">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>

                <!-- Chart Umur -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 text-center">Kelompok Umur</h3>
                    <div class="h-48">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>

                <!-- Chart Pendidikan -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 text-center">Pendidikan Terakhir</h3>
                    <div class="h-48">
                        <canvas id="educationChart"></canvas>
                    </div>
                </div>

                <!-- Chart Jabatan Fungsional -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 text-center">Jabatan Fungsional</h3>
                    <div class="h-48">
                        <canvas id="positionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Summary Demografis -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 text-center">Ringkasan Demografis</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($data['total_respondents']) }}</div>
                        <div class="text-gray-600">Total Responden</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($data['demographic_data']['total_demographic_responses']) }}</div>
                        <div class="text-gray-600">Dengan Data Demografis</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $data['total_respondents'] > 0 ? round(($data['demographic_data']['total_demographic_responses'] / $data['total_respondents']) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-gray-600">Persentase Lengkap</div>
                    </div>
                </div>
            </div>

            <!-- Tabel Detail Demografis -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Detail Data Demografis</h3>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Menampilkan 10 data teratas berdasarkan jumlah responden</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-blue-50">
                                <th class="border border-gray-300 px-3 py-2 text-left">No</th>
                                <th class="border border-gray-300 px-3 py-2 text-left">Jenis Kelamin</th>
                                <th class="border border-gray-300 px-3 py-2 text-left">Umur</th>
                                <th class="border border-gray-300 px-3 py-2 text-left">Pendidikan</th>
                                <th class="border border-gray-300 px-3 py-2 text-left">Unit Kerja</th>
                                <th class="border border-gray-300 px-3 py-2 text-left">Jabatan Fungsional</th>
                                <th class="border border-gray-300 px-3 py-2 text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['demographic_table_data'] as $index => $demo)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-gray-600">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $demo['jenis_kelamin'] ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $demo['umur'] ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $demo['pendidikan'] ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $demo['unit_kerja'] ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $demo['jabatan_fungsional'] ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center font-semibold text-blue-600">{{ $demo['count'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Informasi Pembatasan -->
                <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-semibold">Informasi Tampilan:</p>
                            <p>Tabel di atas menampilkan <strong>10 data demografis teratas</strong> berdasarkan jumlah responden terbanyak. Data diurutkan dari yang paling banyak hingga paling sedikit untuk memberikan gambaran yang lebih fokus.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Informasi Sistem Survey -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Informasi Sistem Survey
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-2">Sistem Pembatasan</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Metode: IP Address + User Agent + Survey ID + Tanggal</li>
                        <li>• Pembatasan: 1 kali per hari per device</li>
                        <li>• Tujuan: Mencegah duplikasi pengisian</li>
                        <li>• Tracking: Semua pengisian dicatat</li>
                    </ul>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-800 mb-2">Data yang Dikumpulkan</h3>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>• Data Demografis (jenis kelamin, umur, dll)</li>
                        <li>• Data Survey (kepuasan, kepentingan, saran)</li>
                        <li>• Data Teknis (IP, User Agent, timestamp)</li>
                        <li>• Data Analisis (gap analysis, statistik)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tabel Hasil Akhir -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Tabel Hasil Akhir
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="border border-gray-300 px-4 py-3 text-center font-bold text-blue-800">Nilai Persepsi</th>
                            <th class="border border-gray-300 px-4 py-3 text-center font-bold text-blue-800">Mutu Pelayanan (x)</th>
                            <th class="border border-gray-300 px-4 py-3 text-center font-bold text-blue-800">Kinerja Unit Pelayanan (y)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris untuk Tingkat Kepuasan -->
                        <tr class="hover:bg-green-50">
                            <td class="border border-gray-300 px-4 py-3 text-center font-semibold text-green-700">{{ $data['final_results_table']['satisfaction']['perception_value'] }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-center font-bold text-green-600">{{ $data['final_results_table']['satisfaction']['service_quality'] }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-center font-bold text-green-600">{{ $data['final_results_table']['satisfaction']['unit_performance'] }}</td>
                        </tr>
                        <!-- Baris untuk Tingkat Kepentingan -->
                        <tr class="hover:bg-blue-50">
                            <td class="border border-gray-300 px-4 py-3 text-center font-semibold text-blue-700">{{ $data['final_results_table']['importance']['perception_value'] }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-center font-bold text-blue-600">{{ $data['final_results_table']['importance']['service_quality'] }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-center font-bold text-blue-600">{{ $data['final_results_table']['importance']['unit_performance'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">Keterangan:</h3>
                
                <!-- Hasil Analisis -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p><strong>Tingkat Kepuasan:</strong> Rata-rata {{ number_format($data['overall_averages']['overall_satisfaction'], 2) }}/4.00</p>
                        <p><strong>Mutu Pelayanan:</strong> {{ $data['final_results_table']['satisfaction']['service_quality'] }} ({{ $data['final_results_table']['satisfaction']['unit_performance'] }})</p>
                    </div>
                    <div>
                        <p><strong>Tingkat Kepentingan:</strong> Rata-rata {{ number_format($data['overall_averages']['overall_importance'], 2) }}/4.00</p>
                        <p><strong>Mutu Pelayanan:</strong> {{ $data['final_results_table']['importance']['service_quality'] }} ({{ $data['final_results_table']['importance']['unit_performance'] }})</p>
                    </div>
                </div>
                
                <!-- Rumusan Penilaian -->
                <div class="border-t pt-3">
                    <h4 class="font-semibold text-gray-700 mb-2">Rumusan Penilaian:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <p><strong>Nilai Interval (N):</strong></p>
                            <ul class="ml-4 space-y-1">
                                <li>• 1.00 - 1.75 → Nilai Persepsi 1</li>
                                <li>• 1.76 - 2.50 → Nilai Persepsi 2</li>
                                <li>• 2.51 - 3.25 → Nilai Persepsi 3</li>
                                <li>• 3.26 - 4.00 → Nilai Persepsi 4</li>
                            </ul>
                        </div>
                        <div>
                            <p><strong>Nilai Interval Konversi (NIK):</strong></p>
                            <ul class="ml-4 space-y-1">
                                <li>• 25.00 - 43.75 → Nilai Persepsi 1</li>
                                <li>• 43.76 - 62.50 → Nilai Persepsi 2</li>
                                <li>• 62.51 - 81.25 → Nilai Persepsi 3</li>
                                <li>• 81.26 - 100.00 → Nilai Persepsi 4</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analisis Per Pertanyaan Table -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-800 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                3. Analisis Per Pertanyaan
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="border border-gray-300 px-4 py-2 text-left">Pertanyaan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Kategori</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Rata-rata Kepuasan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Rata-rata Kepentingan</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Gap</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                            <th class="border border-gray-300 px-4 py-2 text-center">Hasil Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['question_averages'] as $question)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">{{ $question['question_text'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $question['category'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center font-semibold text-green-600">{{ $question['satisfaction_avg'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center font-semibold text-blue-600">{{ $question['importance_avg'] }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center font-bold {{ $question['gap'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $question['gap'] }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    if ($question['gap'] <= 0) {
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Baik';
                                    } elseif ($question['gap'] <= 0.5) {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText = 'Cukup';
                                    } elseif ($question['gap'] <= 1.0) {
                                        $statusClass = 'bg-orange-100 text-orange-800';
                                        $statusText = 'Perlu Perbaikan';
                                    } else {
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText = 'Sangat Perlu Perbaikan';
                                    }
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                @if(isset($question['final_result']))
                                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $question['final_result']['color_class'] }}">
                                        {{ $question['final_result']['display_text'] }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-sm font-bold text-gray-600 bg-gray-100">
                                        N/A
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Keterangan Hasil Akhir -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <h3 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Keterangan Hasil Akhir
                </h3>
                <div class="text-sm text-blue-700 space-y-2">
                    <p><strong>Hasil Akhir</strong> ditentukan berdasarkan <strong>Rata-rata Kepuasan</strong> dari setiap pertanyaan menggunakan skala yang sama dengan Tabel Hasil Akhir:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded text-xs font-bold text-blue-600 bg-blue-100">A (Sangat Baik)</span>
                                <span>→ Rata-rata Kepuasan 3.26 - 4.00</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded text-xs font-bold text-green-600 bg-green-100">B (Baik)</span>
                                <span>→ Rata-rata Kepuasan 2.51 - 3.25</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded text-xs font-bold text-yellow-600 bg-yellow-100">C (Kurang Baik)</span>
                                <span>→ Rata-rata Kepuasan 1.76 - 2.50</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 rounded text-xs font-bold text-red-600 bg-red-100">D (Tidak Baik)</span>
                                <span>→ Rata-rata Kepuasan 1.00 - 1.75</span>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-blue-600 font-medium">
                        <strong>Catatan:</strong> Hasil Akhir menunjukkan mutu pelayanan dan kinerja unit pelayanan untuk setiap pertanyaan berdasarkan tingkat kepuasan responden.
                    </p>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk charts
        @php
        // Helper function untuk menyingkat pertanyaan
        function getShortQuestionText($questionText) {
            $shortMappings = [
                'Kecepatan dalam menyampaikan informasi terkait kegiatan dan layanan pembinaan jabatan fungsional' => 'Kecepatan Informasi',
                'Ketepatan waktu pelaksanaan kegiatan atau penyampaian layanan' => 'Ketepatan Waktu',
                'Kecepatan merespon pertanyaan, permintaan, atau pengaduan dari' => 'Kecepatan Respons',
                'Ketepatan dan keakuratan informasi yang disampaikan' => 'Keakuratan Informasi',
                'Profesionalisme dan etika pegawai dalam memberikan layanan' => 'Profesionalisme Pegawai',
                'Sikap dan tutur kata pegawai dalam melayani' => 'Sikap Pegawai',
                'Pemahaman pegawai terhadap subjek layanan' => 'Pemahaman Pegawai',
                'Kemudahan peserta atau pengguna dalam mengakses layanan' => 'Kemudahan Akses',
                'Komitmen dan kesungguhan Pusbin dalam memberikan layanan' => 'Komitmen Pusbin'
            ];

            foreach ($shortMappings as $full => $short) {
                if (strpos($questionText, $full) !== false) {
                    return $short;
                }
            }

            $words = explode(' ', $questionText);
            if (count($words) >= 2) {
                return $words[0] . ' ' . $words[1];
            }

            return \Illuminate\Support\Str::limit($questionText, 20);
        }

        $chartQuestions = $data['question_averages'] ? $data['question_averages']->take(10)->map(function($q) {
            return [
                'text' => getShortQuestionText($q['question_text']),
                'satisfaction' => $q['satisfaction_avg'],
                'importance' => $q['importance_avg'],
                'gap' => $q['gap']
            ];
        }) : collect();

        $chartImprovementAreas = $data['improvement_areas'] ? $data['improvement_areas']->take(10)->map(function($area) {
            return [
                'text' => \Illuminate\Support\Str::limit($area['question_text'], 30),
                'gap' => $area['gap']
            ];
        }) : collect();
    @endphp

    const chartData = {
        questions: @json($chartQuestions),
        improvement_areas: @json($chartImprovementAreas),
        satisfaction_distribution: @json($data['service_quality']['distribution'] ?? []),
        importance_distribution: @json($data['expectations']['distribution'] ?? []),
        overall_averages: @json($data['overall_averages'] ?? []),
        demographic_data: @json($data['demographic_data'] ?? [])
    };

    // Chart 1: Gap Analysis Chart
    if (chartData.questions && chartData.questions.length > 0) {
        const gapCtx = document.getElementById('gapAnalysisChart').getContext('2d');
        new Chart(gapCtx, {
            type: 'bar',
            data: {
                labels: chartData.questions.map(q => q.text),
                datasets: [{
                    label: 'Tingkat Kepuasan',
                    data: chartData.questions.map(q => q.satisfaction),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                }, {
                    label: 'Tingkat Kepentingan',
                    data: chartData.questions.map(q => q.importance),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4,
                        ticks: {
                            stepSize: 0.5
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Perbandingan Tingkat Kepuasan vs Kepentingan'
                    },
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }

    // Chart 2: Satisfaction Distribution
    if (chartData.satisfaction_distribution && Object.keys(chartData.satisfaction_distribution).length > 0) {
        const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
        new Chart(satisfactionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tidak Puas', 'Kurang Puas', 'Puas', 'Sangat Puas'],
                datasets: [{
                    data: Object.values(chartData.satisfaction_distribution),
                    backgroundColor: [
                        'rgba(220, 38, 127, 0.8)',   // Pink-600
                        'rgba(251, 146, 60, 0.8)',   // Orange-400
                        'rgba(59, 130, 246, 0.8)',   // Blue-500
                        'rgba(16, 185, 129, 0.8)'    // Emerald-500
                    ],
                    borderColor: [
                        'rgba(220, 38, 127, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Tingkat Kepuasan'
                    }
                }
            }
        });
    }

    // Chart 3: Importance Distribution
    if (chartData.importance_distribution && Object.keys(chartData.importance_distribution).length > 0) {
        const importanceCtx = document.getElementById('importanceChart').getContext('2d');
        new Chart(importanceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tidak Penting', 'Kurang Penting', 'Penting', 'Sangat Penting'],
                datasets: [{
                    data: Object.values(chartData.importance_distribution),
                    backgroundColor: [
                        'rgba(168, 85, 247, 0.8)',   // Purple-500
                        'rgba(236, 72, 153, 0.8)',   // Pink-500
                        'rgba(34, 197, 94, 0.8)',    // Green-500
                        'rgba(14, 165, 233, 0.8)'    // Sky-500
                    ],
                    borderColor: [
                        'rgba(168, 85, 247, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(14, 165, 233, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Tingkat Kepentingan'
                    }
                }
            }
        });
    }

    // Chart 4: Improvement Areas
    if (chartData.improvement_areas && chartData.improvement_areas.length > 0) {
        const improvementCtx = document.getElementById('improvementChart').getContext('2d');
        new Chart(improvementCtx, {
            type: 'bar',
            data: {
                labels: chartData.improvement_areas.map(area => area.text),
                datasets: [{
                    label: 'Gap Score',
                    data: chartData.improvement_areas.map(area => area.gap),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Top 10 Area yang Perlu Perbaikan'
                    }
                }
            }
        });
    }

    // Chart 5: Question Analysis
    if (chartData.questions && chartData.questions.length > 0) {
        const questionCtx = document.getElementById('questionAnalysisChart').getContext('2d');
        new Chart(questionCtx, {
            type: 'radar',
            data: {
                labels: chartData.questions.map(q => q.text),
                datasets: [{
                    label: 'Tingkat Kepuasan',
                    data: chartData.questions.map(q => q.satisfaction),
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(34, 197, 94, 1)'
                }, {
                    label: 'Tingkat Kepentingan',
                    data: chartData.questions.map(q => q.importance),
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 4
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Analisis Radar - Kepuasan vs Kepentingan'
                    }
                }
            }
        });
    }

        // Chart 6: Status Distribution
    if (chartData.questions && chartData.questions.length > 0) {
        const statusCtx = document.getElementById('statusChart').getContext('2d');

        // Hitung distribusi status
        const statusCounts = {
            'Baik': 0,
            'Cukup': 0,
            'Perlu Perbaikan': 0,
            'Sangat Perlu Perbaikan': 0
        };

        chartData.questions.forEach(question => {
            if (question.gap <= 0) {
                statusCounts['Baik']++;
            } else if (question.gap <= 0.5) {
                statusCounts['Cukup']++;
            } else if (question.gap <= 1.0) {
                statusCounts['Perlu Perbaikan']++;
            } else {
                statusCounts['Sangat Perlu Perbaikan']++;
            }
        });

        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusCounts),
                datasets: [{
                    data: Object.values(statusCounts),
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Status Perbaikan'
                    }
                }
            }
        });
    }

    // Chart 7: Demografis Charts
    if (chartData.demographic_data && chartData.demographic_data.total_demographic_responses > 0) {
        // Chart Jenis Kelamin
        if (chartData.demographic_data.gender && Object.keys(chartData.demographic_data.gender).length > 0) {
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(chartData.demographic_data.gender),
                    datasets: [{
                        data: Object.values(chartData.demographic_data.gender),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Chart Umur
        if (chartData.demographic_data.age && Object.keys(chartData.demographic_data.age).length > 0) {
            const ageCtx = document.getElementById('ageChart').getContext('2d');
            new Chart(ageCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(chartData.demographic_data.age),
                    datasets: [{
                        label: 'Jumlah Responden',
                        data: Object.values(chartData.demographic_data.age),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Chart Pendidikan
        if (chartData.demographic_data.education && Object.keys(chartData.demographic_data.education).length > 0) {
            const educationCtx = document.getElementById('educationChart').getContext('2d');
            new Chart(educationCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(chartData.demographic_data.education),
                    datasets: [{
                        data: Object.values(chartData.demographic_data.education),
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(147, 51, 234, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(16, 185, 129, 0.8)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        // Chart Jabatan Fungsional (Pie Chart dengan Persentase)
        if (chartData.demographic_data.position && Object.keys(chartData.demographic_data.position).length > 0) {
            const positionCtx = document.getElementById('positionChart').getContext('2d');
            
            // Hitung total untuk persentase
            const positionData = chartData.demographic_data.position;
            const totalPosition = Object.values(positionData).reduce((sum, count) => sum + count, 0);
            
            // Siapkan data dengan persentase
            const positionLabels = Object.keys(positionData).map(key => {
                const count = positionData[key];
                const percentage = totalPosition > 0 ? ((count / totalPosition) * 100).toFixed(1) : 0;
                return `${key} (${percentage}%)`;
            });
            
            new Chart(positionCtx, {
                type: 'pie',
                data: {
                    labels: positionLabels,
                    datasets: [{
                        data: Object.values(positionData),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(245, 158, 11, 0.8)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const percentage = totalPosition > 0 ? ((value / totalPosition) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endsection
