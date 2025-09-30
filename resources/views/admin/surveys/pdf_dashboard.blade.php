<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Survei</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #888; padding: 6px 8px; }
        th { background: #f0f0f0; }
        .stat-box { display: inline-block; margin-right: 20px; margin-bottom: 10px; }
        .section-title { margin-top: 24px; margin-bottom: 8px; font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Rekapitulasi Hasil Survei</h1>
    @if($filterSurvey)
        <h2>Survei: {{ $filterSurvey->title }}</h2>
    @endif
    <div style="margin-bottom: 16px;">
        <div class="stat-box">Total Responden: <b>{{ $totalRespondents }}</b></div>
        <div class="stat-box">Total Pertanyaan: <b>{{ $totalQuestions }}</b></div>
        <div class="stat-box">Total Kategori: <b>{{ $totalCategories }}</b></div>
        <div class="stat-box">Total Jawaban: <b>{{ $totalResponses }}</b></div>
    </div>

    @if(!empty($barChartImg) && !empty($lineChartImg))
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px;">
            <div style="flex:1; text-align:center;">
                <div style="font-weight:bold; margin-bottom:4px;">Bar Chart: Jawaban per Kategori</div>
                <img src="{{ $barChartImg }}" style="max-width:100%; height:auto; border:1px solid #ccc; background:#fff;" />
            </div>
            <div style="flex:1; text-align:center;">
                <div style="font-weight:bold; margin-bottom:4px;">Line Chart: Rata-rata Skala per Pertanyaan</div>
                <img src="{{ $lineChartImg }}" style="max-width:100%; height:auto; border:1px solid #ccc; background:#fff;" />
            </div>
        </div>
    @endif

    <div class="section-title">Statistik per Kategori</div>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Pertanyaan</th>
                <th>Total Jawaban</th>
                <th>Rata-rata Jawaban</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryStats as $stat)
            <tr>
                <td>{{ $stat['name'] }}</td>
                <td>{{ $stat['question_count'] }}</td>
                <td>{{ $stat['response_count'] }}</td>
                <td>{{ $stat['average_responses'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($satisfactionData->count() > 0)
    <div class="section-title">Statistik Tingkat Kepuasan</div>
    <table>
        <thead>
            <tr>
                <th>Pertanyaan</th>
                <th>Rata-rata</th>
                <th>Total Jawaban</th>
                <th>Distribusi (1-4)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($satisfactionData as $stat)
            <tr>
                <td>{{ Str::limit($stat['question'], 80) }}</td>
                <td>{{ $stat['average'] }}/4</td>
                <td>{{ $stat['total_responses'] }}</td>
                <td>
                    @foreach($stat['distribution'] as $scale => $count)
                        {{ $scale }}: {{ $count }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($importanceData->count() > 0)
    <div class="section-title">Statistik Tingkat Kepentingan</div>
    <table>
        <thead>
            <tr>
                <th>Pertanyaan</th>
                <th>Rata-rata</th>
                <th>Total Jawaban</th>
                <th>Distribusi (1-4)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($importanceData as $stat)
            <tr>
                <td>{{ Str::limit($stat['question'], 80) }}</td>
                <td>{{ $stat['average'] }}/4</td>
                <td>{{ $stat['total_responses'] }}</td>
                <td>
                    @foreach($stat['distribution'] as $scale => $count)
                        {{ $scale }}: {{ $count }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($choiceStats->count() > 0)
    <div class="section-title">Statistik Pertanyaan Pilihan Ganda</div>
    <table>
        <thead>
            <tr>
                <th>Pertanyaan</th>
                <th>Total Jawaban</th>
                <th>Distribusi Pilihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($choiceStats as $stat)
            <tr>
                <td>{{ $stat['question'] }}</td>
                <td>{{ $stat['total_responses'] }}</td>
                <td>
                    @foreach($stat['options'] as $option)
                        {{ $option }}: {{ $stat['distribution'][$option] ?? 0 }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div style="margin-top: 32px; font-size: 11px; color: #888;">Generated at: {{ now()->format('d-m-Y H:i') }}</div>
</body>
</html>
