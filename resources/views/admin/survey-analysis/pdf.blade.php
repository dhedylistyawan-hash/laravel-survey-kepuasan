<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Survey Kepuasan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .stats-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }
        .stat-box h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #666;
        }
        .stat-box .value {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .status-baik { color: #059669; }
        .status-cukup { color: #d97706; }
        .status-perlu { color: #ea580c; }
        .status-sangat { color: #dc2626; }
        .page-break {
            page-break-before: always;
        }
        .chart-placeholder {
            border: 1px dashed #e5e7eb;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
            background-color: #f9fafb;
        }
        .gap-positive { color: #dc2626; }
        .gap-negative { color: #059669; }
        .test-info {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>


    <div class="header">
        <h1>LAPORAN ANALISIS SURVEY KEPUASAN</h1>
        <p>Periode: {{ $data['period']['start']->format('d/m/Y') }} - {{ $data['period']['end']->format('d/m/Y') }}</p>
        <p>Dibuat pada: {{ now()->format('d/m/Y H:i') }}</p>

    </div>

    <!-- 1. Statistik Umum -->
    <div class="section">
        <h2>1. STATISTIK UMUM</h2>
        <div class="stats-grid">
            <div class="stat-box">
                <h3>Jumlah Responden</h3>
                <div class="value">{{ number_format($data['total_respondents']) }}</div>
            </div>
            <div class="stat-box">
                <h3>Rata-rata Kepuasan</h3>
                <div class="value">{{ $data['overall_averages']['overall_satisfaction'] }}</div>
            </div>
            <div class="stat-box">
                <h3>Rata-rata Kepentingan</h3>
                <div class="value">{{ $data['overall_averages']['overall_importance'] }}</div>
            </div>
            <div class="stat-box">
                <h3>Gap Keseluruhan</h3>
                <div class="value {{ $data['overall_averages']['overall_gap'] > 0 ? 'gap-positive' : 'gap-negative' }}">
                    {{ $data['overall_averages']['overall_gap'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Hasil Akhir -->
    <div class="section">
        <h2>2. TABEL HASIL AKHIR</h2>
        <table>
            <tr>
                <th style="text-align: center;">Nilai Persepsi</th>
                <th style="text-align: center;">Mutu Pelayanan (x)</th>
                <th style="text-align: center;">Kinerja Unit Pelayanan (y)</th>
            </tr>
            <!-- Baris untuk Tingkat Kepuasan -->
            <tr style="background-color: #f0fdf4;">
                <td style="text-align: center; font-weight: bold; color: #059669;">{{ $data['final_results_table']['satisfaction']['perception_value'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #059669;">{{ $data['final_results_table']['satisfaction']['service_quality'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #059669;">{{ $data['final_results_table']['satisfaction']['unit_performance'] }}</td>
            </tr>
            <!-- Baris untuk Tingkat Kepentingan -->
            <tr style="background-color: #eff6ff;">
                <td style="text-align: center; font-weight: bold; color: #2563eb;">{{ $data['final_results_table']['importance']['perception_value'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #2563eb;">{{ $data['final_results_table']['importance']['service_quality'] }}</td>
                <td style="text-align: center; font-weight: bold; color: #2563eb;">{{ $data['final_results_table']['importance']['unit_performance'] }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 15px; padding: 10px; background-color: #f9fafb; border-left: 4px solid #2563eb;">
            <h4 style="margin: 0 0 10px 0; color: #2563eb;">Keterangan:</h4>
            
            <!-- Hasil Analisis -->
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <p style="margin: 5px 0;"><strong>Tingkat Kepuasan:</strong> Rata-rata {{ number_format($data['overall_averages']['overall_satisfaction'], 2) }}/4.00</p>
                    <p style="margin: 5px 0;"><strong>Mutu Pelayanan:</strong> {{ $data['final_results_table']['satisfaction']['service_quality'] }} ({{ $data['final_results_table']['satisfaction']['unit_performance'] }})</p>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 5px 0;"><strong>Tingkat Kepentingan:</strong> Rata-rata {{ number_format($data['overall_averages']['overall_importance'], 2) }}/4.00</p>
                    <p style="margin: 5px 0;"><strong>Mutu Pelayanan:</strong> {{ $data['final_results_table']['importance']['service_quality'] }} ({{ $data['final_results_table']['importance']['unit_performance'] }})</p>
                </div>
            </div>
            
            <!-- Rumusan Penilaian -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 10px;">
                <h5 style="margin: 0 0 8px 0; color: #374151; font-size: 12px;">Rumusan Penilaian:</h5>
                <div style="display: flex; gap: 20px; font-size: 10px;">
                    <div style="flex: 1;">
                        <p style="margin: 3px 0; font-weight: bold;">Nilai Interval (N):</p>
                        <ul style="margin: 3px 0; padding-left: 15px;">
                            <li>1.00 - 1.75 → Nilai Persepsi 1</li>
                            <li>1.76 - 2.50 → Nilai Persepsi 2</li>
                            <li>2.51 - 3.25 → Nilai Persepsi 3</li>
                            <li>3.26 - 4.00 → Nilai Persepsi 4</li>
                        </ul>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 3px 0; font-weight: bold;">Nilai Interval Konversi (NIK):</p>
                        <ul style="margin: 3px 0; padding-left: 15px;">
                            <li>25.00 - 43.75 → Nilai Persepsi 1</li>
                            <li>43.76 - 62.50 → Nilai Persepsi 2</li>
                            <li>62.51 - 81.25 → Nilai Persepsi 3</li>
                            <li>81.26 - 100.00 → Nilai Persepsi 4</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Chart Analisis (Format Tabel) -->
    <div class="section">
        <h2>3. CHART ANALISIS</h2>

        <!-- Chart 1: Gap Analysis -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 5px;">Chart 1: Gap Analysis (Kepentingan vs Kepuasan)</h3>
            <table>
                <tr>
                    <th>No</th>
                    <th>Pertanyaan</th>
                    <th>Rata-rata Kepuasan</th>
                    <th>Rata-rata Kepentingan</th>
                    <th>Gap</th>
                    <th>Status</th>
                    <th>Hasil Akhir</th>
                </tr>
                @foreach($data['question_averages'] as $index => $question)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ Str::limit($question['question_text'], 60) }}</td>
                    <td style="color: #059669; font-weight: bold;">{{ $question['satisfaction_avg'] }}</td>
                    <td style="color: #2563eb; font-weight: bold;">{{ $question['importance_avg'] }}</td>
                    <td class="{{ $question['gap'] > 0 ? 'gap-positive' : 'gap-negative' }}">{{ $question['gap'] }}</td>
                    <td>
                        @php
                            if ($question['gap'] <= 0) {
                                $statusClass = 'status-baik';
                                $statusText = 'Baik';
                            } elseif ($question['gap'] <= 0.5) {
                                $statusClass = 'status-cukup';
                                $statusText = 'Cukup';
                            } elseif ($question['gap'] <= 1.0) {
                                $statusClass = 'status-perlu';
                                $statusText = 'Perlu Perbaikan';
                            } else {
                                $statusClass = 'status-sangat';
                                $statusText = 'Sangat Perlu Perbaikan';
                            }
                        @endphp
                        <span class="{{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        @if(isset($question['final_result']))
                            <span style="font-weight: bold; color: #2563eb;">{{ $question['final_result']['display_text'] }}</span>
                        @else
                            <span style="color: #666;">N/A</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Chart 2: Distribusi Jawaban -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 5px;">Chart 2: Distribusi Jawaban</h3>
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1;">
                    <h4 style="color: #059669;">Distribusi Tingkat Kepuasan</h4>
                    <table>
                        <tr>
                            <th>Level</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                        @php $totalSatisfaction = array_sum($data['service_quality']['distribution']); @endphp
                        @foreach($data['service_quality']['distribution'] as $level => $count)
                        <tr>
                            <td>
                                @switch($level)
                                    @case(1) Tidak Puas @break
                                    @case(2) Kurang Puas @break
                                    @case(3) Puas @break
                                    @case(4) Sangat Puas @break
                                @endswitch
                            </td>
                            <td>{{ $count }}</td>
                            <td>{{ $totalSatisfaction > 0 ? round(($count / $totalSatisfaction) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <div style="flex: 1;">
                    <h4 style="color: #2563eb;">Distribusi Tingkat Kepentingan</h4>
                    <table>
                        <tr>
                            <th>Level</th>
                            <th>Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                        @php $totalImportance = array_sum($data['expectations']['distribution']); @endphp
                        @foreach($data['expectations']['distribution'] as $level => $count)
                        <tr>
                            <td>
                                @switch($level)
                                    @case(1) Tidak Penting @break
                                    @case(2) Kurang Penting @break
                                    @case(3) Penting @break
                                    @case(4) Sangat Penting @break
                                @endswitch
                            </td>
                            <td>{{ $count }}</td>
                            <td>{{ $totalImportance > 0 ? round(($count / $totalImportance) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart 3: Top Improvement Areas -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #dc2626; border-bottom: 2px solid #dc2626; padding-bottom: 5px;">Chart 3: Top 10 Area yang Perlu Perbaikan</h3>
            <table>
                <tr>
                    <th>No</th>
                    <th>Pertanyaan</th>
                    <th>Kategori</th>
                    <th>Gap</th>
                    <th>Status</th>
                </tr>
                @foreach($data['improvement_areas'] as $index => $area)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ Str::limit($area['question_text'], 60) }}</td>
                    <td>{{ $area['category'] }}</td>
                    <td class="gap-positive">{{ $area['gap'] }}</td>
                    <td class="status-sangat">{{ $area['gap_status'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Chart 4: Status Distribution -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 5px;">Chart 4: Distribusi Status Perbaikan</h3>
            <table>
                <tr>
                    <th>Status</th>
                    <th>Jumlah Pertanyaan</th>
                    <th>Persentase</th>
                    <th>Deskripsi</th>
                </tr>
                @php
                    $totalQuestions = $data['question_averages']->count();
                    $baikCount = $data['question_averages']->filter(function($q) { return $q['gap'] <= 0; })->count();
                    $cukupCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 0 && $q['gap'] <= 0.5; })->count();
                    $perluCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 0.5 && $q['gap'] <= 1.0; })->count();
                    $sangatCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 1.0; })->count();
                @endphp
                <tr>
                    <td class="status-baik">Baik</td>
                    <td>{{ $baikCount }}</td>
                    <td>{{ $totalQuestions > 0 ? round(($baikCount / $totalQuestions) * 100, 1) : 0 }}%</td>
                    <td>Gap ≤ 0 (Kepuasan ≥ Kepentingan)</td>
                </tr>
                <tr>
                    <td class="status-cukup">Cukup</td>
                    <td>{{ $cukupCount }}</td>
                    <td>{{ $totalQuestions > 0 ? round(($cukupCount / $totalQuestions) * 100, 1) : 0 }}%</td>
                    <td>Gap 0.1 - 0.5</td>
                </tr>
                <tr>
                    <td class="status-perlu">Perlu Perbaikan</td>
                    <td>{{ $perluCount }}</td>
                    <td>{{ $totalQuestions > 0 ? round(($perluCount / $totalQuestions) * 100, 1) : 0 }}%</td>
                    <td>Gap 0.6 - 1.0</td>
                </tr>
                <tr>
                    <td class="status-sangat">Sangat Perlu Perbaikan</td>
                    <td>{{ $sangatCount }}</td>
                    <td>{{ $totalQuestions > 0 ? round(($sangatCount / $totalQuestions) * 100, 1) : 0 }}%</td>
                    <td>Gap > 1.0</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- 4. Analisis Per Pertanyaan -->
    <div class="section page-break">
        <h2>4. ANALISIS PER PERTANYAAN (DETAIL)</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Pertanyaan</th>
                <th>Kategori</th>
                <th>Rata-rata Kepuasan</th>
                <th>Rata-rata Kepentingan</th>
                <th>Gap</th>
                <th>Status</th>
                <th>Hasil Akhir</th>
            </tr>
            @foreach($data['question_averages'] as $index => $question)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $question['question_text'] }}</td>
                <td>{{ $question['category'] }}</td>
                <td>{{ $question['satisfaction_avg'] }}</td>
                <td>{{ $question['importance_avg'] }}</td>
                <td class="{{ $question['gap'] > 0 ? 'gap-positive' : 'gap-negative' }}">{{ $question['gap'] }}</td>
                <td>
                    @php
                        if ($question['gap'] <= 0) {
                            $statusClass = 'status-baik';
                            $statusText = 'Baik';
                        } elseif ($question['gap'] <= 0.5) {
                            $statusClass = 'status-cukup';
                            $statusText = 'Cukup';
                        } elseif ($question['gap'] <= 1.0) {
                            $statusClass = 'status-perlu';
                            $statusText = 'Perlu Perbaikan';
                        } else {
                            $statusClass = 'status-sangat';
                            $statusText = 'Sangat Perlu Perbaikan';
                        }
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td>
                    @if(isset($question['final_result']))
                        <span style="font-weight: bold; color: #2563eb;">{{ $question['final_result']['display_text'] }}</span>
                    @else
                        <span style="color: #666;">N/A</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        
        <!-- Keterangan Hasil Akhir -->
        <div style="margin-top: 20px; padding: 15px; background-color: #eff6ff; border-left: 4px solid #2563eb; border-radius: 5px;">
            <h4 style="margin: 0 0 10px 0; color: #2563eb; font-size: 14px;">Keterangan Hasil Akhir</h4>
            <p style="margin: 5px 0; font-size: 12px; color: #1e40af;">
                <strong>Hasil Akhir</strong> ditentukan berdasarkan <strong>Rata-rata Kepuasan</strong> dari setiap pertanyaan menggunakan skala yang sama dengan Tabel Hasil Akhir:
            </p>
            <div style="margin-top: 10px; display: flex; gap: 20px; font-size: 11px;">
                <div style="flex: 1;">
                    <div style="margin: 3px 0;"><strong>A (Sangat Baik):</strong> Rata-rata Kepuasan 3.26 - 4.00</div>
                    <div style="margin: 3px 0;"><strong>B (Baik):</strong> Rata-rata Kepuasan 2.51 - 3.25</div>
                </div>
                <div style="flex: 1;">
                    <div style="margin: 3px 0;"><strong>C (Kurang Baik):</strong> Rata-rata Kepuasan 1.76 - 2.50</div>
                    <div style="margin: 3px 0;"><strong>D (Tidak Baik):</strong> Rata-rata Kepuasan 1.00 - 1.75</div>
                </div>
            </div>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #1e40af; font-weight: bold;">
                <strong>Catatan:</strong> Hasil Akhir menunjukkan mutu pelayanan dan kinerja unit pelayanan untuk setiap pertanyaan berdasarkan tingkat kepuasan responden.
            </p>
        </div>
    </div>

    <!-- 5. Area yang Perlu Perbaikan -->
    <div class="section page-break">
        <h2>5. AREA YANG PERLU PERBAIKAN (DETAIL)</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Pertanyaan</th>
                <th>Kategori</th>
                <th>Gap</th>
                <th>Status</th>
            </tr>
            @foreach($data['improvement_areas'] as $index => $area)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $area['question_text'] }}</td>
                <td>{{ $area['category'] }}</td>
                <td class="gap-positive">{{ $area['gap'] }}</td>
                <td class="status-sangat">{{ $area['gap_status'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- 6. Rekomendasi -->
    <div class="section page-break">
        <h2>6. REKOMENDASI PERBAIKAN</h2>

        @php
            $highPriorityCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 1.0; })->count();
            $mediumPriorityCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 0.5 && $q['gap'] <= 1.0; })->count();
            $lowPriorityCount = $data['question_averages']->filter(function($q) { return $q['gap'] > 0 && $q['gap'] <= 0.5; })->count();
        @endphp

        <div style="margin-bottom: 20px;">
            <h3 style="color: #dc2626;">Prioritas Tinggi ({{ $highPriorityCount }} item)</h3>
            <p>Area yang memerlukan perbaikan segera dengan gap > 1.0</p>
            <ul>
                @foreach($data['question_averages']->filter(function($q) { return $q['gap'] > 1.0; }) as $question)
                <li><strong>{{ $question['question_text'] }}</strong> (Gap: {{ $question['gap'] }})</li>
                @endforeach
            </ul>
        </div>

        <div style="margin-bottom: 20px;">
            <h3 style="color: #ea580c;">Prioritas Menengah ({{ $mediumPriorityCount }} item)</h3>
            <p>Area yang memerlukan perbaikan dengan gap 0.5 - 1.0</p>
            <ul>
                @foreach($data['question_averages']->filter(function($q) { return $q['gap'] > 0.5 && $q['gap'] <= 1.0; }) as $question)
                <li><strong>{{ $question['question_text'] }}</strong> (Gap: {{ $question['gap'] }})</li>
                @endforeach
            </ul>
        </div>

        <div style="margin-bottom: 20px;">
            <h3 style="color: #d97706;">Prioritas Rendah ({{ $lowPriorityCount }} item)</h3>
            <p>Area yang memerlukan monitoring dengan gap 0 - 0.5</p>
            <ul>
                @foreach($data['question_averages']->filter(function($q) { return $q['gap'] > 0 && $q['gap'] <= 0.5; }) as $question)
                <li><strong>{{ $question['question_text'] }}</strong> (Gap: {{ $question['gap'] }})</li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- 7. Kesimpulan -->
    <div class="section page-break">
        <h2>7. KESIMPULAN</h2>

        @php
            $overallGap = $data['overall_averages']['overall_gap'];
            $totalQuestions = $data['question_averages']->count();
            $goodQuestions = $data['question_averages']->filter(function($q) { return $q['gap'] <= 0; })->count();
            $improvementQuestions = $data['question_averages']->filter(function($q) { return $q['gap'] > 0; })->count();
            $goodPercentage = $totalQuestions > 0 ? round(($goodQuestions / $totalQuestions) * 100, 1) : 0;
            $improvementPercentage = $totalQuestions > 0 ? round(($improvementQuestions / $totalQuestions) * 100, 1) : 0;
        @endphp

        <div style="margin-bottom: 20px;">
            <h3>Ringkasan Performa</h3>
            <ul>
                <li><strong>Gap Keseluruhan:</strong> {{ $overallGap }}
                    @if($overallGap > 0)
                        <span style="color: #dc2626;">(Perlu Perbaikan)</span>
                    @else
                        <span style="color: #059669;">(Baik)</span>
                    @endif
                </li>
                <li><strong>Pertanyaan dengan Performa Baik:</strong> {{ $goodQuestions }} dari {{ $totalQuestions }} ({{ $goodPercentage }}%)</li>
                <li><strong>Pertanyaan yang Perlu Perbaikan:</strong> {{ $improvementQuestions }} dari {{ $totalQuestions }} ({{ $improvementPercentage }}%)</li>
            </ul>
        </div>

        <div style="margin-bottom: 20px;">
            <h3>Rekomendasi Umum</h3>
            @if($overallGap > 0.5)
                <p style="color: #dc2626;"><strong>⚠️ PERHATIAN:</strong> Gap keseluruhan menunjukkan bahwa layanan belum memenuhi harapan pengguna. Perlu dilakukan perbaikan menyeluruh.</p>
            @elseif($overallGap > 0)
                <p style="color: #ea580c;"><strong>⚠️ MONITORING:</strong> Gap keseluruhan menunjukkan beberapa area perlu perbaikan. Fokus pada area dengan gap tertinggi.</p>
            @else
                <p style="color: #059669;"><strong>✅ BAIK:</strong> Gap keseluruhan menunjukkan layanan sudah memenuhi harapan pengguna. Pertahankan kualitas layanan.</p>
            @endif
        </div>

        <div>
            <h3>Langkah Selanjutnya</h3>
            <ol>
                <li>Fokus pada area dengan gap tertinggi untuk perbaikan prioritas</li>
                <li>Lakukan analisis mendalam terhadap area yang memerlukan perbaikan</li>
                <li>Buat rencana aksi perbaikan dengan timeline yang jelas</li>
                <li>Monitor progress perbaikan secara berkala</li>
                <li>Lakukan survey follow-up untuk mengukur efektivitas perbaikan</li>
            </ol>
        </div>
    </div>

    <!-- 7. Profil Demografis Responden -->
    <div class="section page-break">
        <h2>7. PROFIL DEMOGRAFIS RESPONDEN</h2>

        @if(isset($data['demographic_data']) && $data['demographic_data']['total_demographic_responses'] > 0)
        <div style="margin-bottom: 25px;">
            <h3>Ringkasan Demografis</h3>
            <p><strong>Total Responden dengan Data Demografis:</strong> {{ $data['demographic_data']['total_demographic_responses'] }}</p>
        </div>

        <!-- Tabel Detail Demografis -->
        <div style="margin-bottom: 25px;">
            <h3>Detail Data Demografis (10 Data Teratas)</h3>
            <p style="font-size: 11px; color: #666; margin-bottom: 10px;">
                <em>Menampilkan 10 data demografis teratas berdasarkan jumlah responden terbanyak</em>
            </p>
            <table class="data-table">
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Jenis Kelamin</th>
                    <th>Umur</th>
                    <th>Pendidikan</th>
                    <th>Unit Kerja</th>
                    <th>Jabatan Fungsional</th>
                    <th>Jumlah</th>
                </tr>
                @foreach($data['demographic_table_data'] as $index => $demo)
                <tr>
                    <td class="text-center" style="font-weight: bold;">{{ $index + 1 }}</td>
                    <td>{{ $demo['jenis_kelamin'] ?? '-' }}</td>
                    <td>{{ $demo['umur'] ?? '-' }}</td>
                    <td>{{ $demo['pendidikan'] ?? '-' }}</td>
                    <td>{{ $demo['unit_kerja'] ?? '-' }}</td>
                    <td>{{ $demo['jabatan_fungsional'] ?? '-' }}</td>
                    <td class="text-center" style="font-weight: bold; color: #2563eb;">{{ $demo['count'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- Chart Demografis -->
        <div style="margin-bottom: 25px;">
            <h3>Distribusi Demografis</h3>
            
            <!-- Jenis Kelamin -->
            <div style="margin-bottom: 20px;">
                <h4>Jenis Kelamin</h4>
                @if(isset($demographicChartImages['gender']))
                    <img src="data:image/png;base64,{{ $demographicChartImages['gender'] }}" style="width: 100%; max-width: 400px; height: auto;">
                @else
                    <table class="data-table">
                        <tr><th>Jenis Kelamin</th><th>Jumlah</th><th>Persentase</th></tr>
                        @foreach($data['demographic_data']['gender'] as $gender => $count)
                        @php
                            $percentage = $data['demographic_data']['total_demographic_responses'] > 0 ? 
                                round(($count / $data['demographic_data']['total_demographic_responses']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $gender }}</td>
                            <td class="text-center">{{ $count }}</td>
                            <td class="text-center">{{ $percentage }}%</td>
                        </tr>
                        @endforeach
                    </table>
                @endif
            </div>

            <!-- Kelompok Umur -->
            <div style="margin-bottom: 20px;">
                <h4>Kelompok Umur</h4>
                @if(isset($demographicChartImages['age']))
                    <img src="data:image/png;base64,{{ $demographicChartImages['age'] }}" style="width: 100%; max-width: 400px; height: auto;">
                @else
                    <table class="data-table">
                        <tr><th>Kelompok Umur</th><th>Jumlah</th><th>Persentase</th></tr>
                        @foreach($data['demographic_data']['age'] as $age => $count)
                        @php
                            $percentage = $data['demographic_data']['total_demographic_responses'] > 0 ? 
                                round(($count / $data['demographic_data']['total_demographic_responses']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $age }}</td>
                            <td class="text-center">{{ $count }}</td>
                            <td class="text-center">{{ $percentage }}%</td>
                        </tr>
                        @endforeach
                    </table>
                @endif
            </div>

            <!-- Pendidikan -->
            <div style="margin-bottom: 20px;">
                <h4>Pendidikan Terakhir</h4>
                @if(isset($demographicChartImages['education']))
                    <img src="data:image/png;base64,{{ $demographicChartImages['education'] }}" style="width: 100%; max-width: 400px; height: auto;">
                @else
                    <table class="data-table">
                        <tr><th>Pendidikan</th><th>Jumlah</th><th>Persentase</th></tr>
                        @foreach($data['demographic_data']['education'] as $education => $count)
                        @php
                            $percentage = $data['demographic_data']['total_demographic_responses'] > 0 ? 
                                round(($count / $data['demographic_data']['total_demographic_responses']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $education }}</td>
                            <td class="text-center">{{ $count }}</td>
                            <td class="text-center">{{ $percentage }}%</td>
                        </tr>
                        @endforeach
                    </table>
                @endif
            </div>

            <!-- Jabatan Fungsional -->
            <div style="margin-bottom: 20px;">
                <h4>Jabatan Fungsional</h4>
                @if(isset($demographicChartImages['position']))
                    <img src="data:image/png;base64,{{ $demographicChartImages['position'] }}" style="width: 100%; max-width: 400px; height: auto;">
                @else
                    <table class="data-table">
                        <tr><th>Jabatan Fungsional</th><th>Jumlah</th><th>Persentase</th></tr>
                        @foreach($data['demographic_data']['position'] as $position => $count)
                        @php
                            $percentage = $data['demographic_data']['total_demographic_responses'] > 0 ? 
                                round(($count / $data['demographic_data']['total_demographic_responses']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $position }}</td>
                            <td class="text-center">{{ $count }}</td>
                            <td class="text-center">{{ $percentage }}%</td>
                        </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
        @else
        <div style="margin-bottom: 25px;">
            <p><em>Tidak ada data demografis yang tersedia untuk periode ini.</em></p>
        </div>
        @endif
    </div>

    <!-- 8. Informasi Sistem -->
    <div class="section page-break">
        <h2>8. INFORMASI SISTEM SURVEY</h2>

        <div style="margin-bottom: 20px;">
            <h3>Sistem Pembatasan Pengisian</h3>
            <ul>
                <li><strong>Metode:</strong> IP Address + User Agent + Survey ID + Tanggal</li>
                <li><strong>Pembatasan:</strong> 1 kali per hari per device untuk survey yang sama</li>
                <li><strong>Tujuan:</strong> Mencegah duplikasi pengisian survey</li>
                <li><strong>Tracking:</strong> Semua pengisian dicatat dengan IP dan User Agent</li>
            </ul>
        </div>

        <div style="margin-bottom: 20px;">
            <h3>Data yang Dikumpulkan</h3>
            <ul>
                <li><strong>Data Demografis:</strong> Jenis kelamin, umur, pendidikan, unit kerja, jabatan</li>
                <li><strong>Data Survey:</strong> Tingkat kepuasan, tingkat kepentingan, saran</li>
                <li><strong>Data Teknis:</strong> IP Address, User Agent, timestamp</li>
                <li><strong>Data Analisis:</strong> Gap analysis, statistik per pertanyaan</li>
            </ul>
        </div>

        <div>
            <h3>Keamanan dan Privasi</h3>
            <ul>
                <li>Data responden dienkripsi dan disimpan dengan aman</li>
                <li>IP Address dan User Agent digunakan hanya untuk pembatasan pengisian</li>
                <li>Data pribadi responden tidak dibagikan ke pihak ketiga</li>
                <li>Sistem mematuhi kebijakan privasi yang berlaku</li>
            </ul>
        </div>
    </div>
</body>
</html>
