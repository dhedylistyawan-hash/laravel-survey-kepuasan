<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumentasi Admin & Teknis Aplikasi Survey BMKG JF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        h1, h2, h3 { color: #1a237e; }
        ul, ol { margin-bottom: 10px; }
        .section { margin-bottom: 24px; }
        .subtitle { color: #1565c0; font-weight: bold; }
        .code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>📄 Dokumentasi Admin & Teknis Aplikasi Survey BMKG JF</h1>

    <div class="section">
        <h2>1. Login Admin</h2>
        <ol>
            <li>Buka halaman login: <span class="code">/login</span></li>
            <li>Masukkan email & password admin.</li>
            <li>Jika belum punya akun admin, minta ke pengelola sistem.</li>
        </ol>
    </div>

    <div class="section">
        <h2>2. Dashboard</h2>
        <ul>
            <li>Setelah login, Anda akan diarahkan ke Dashboard.</li>
            <li>Lihat statistik survei: total responden, pertanyaan, kategori, jawaban.</li>
            <li>Gunakan filter survei, kategori, tanggal untuk statistik spesifik.</li>
        </ul>
    </div>

    <div class="section">
        <h2>3. Mengelola Kategori</h2>
        <ul>
            <li>Klik menu <b>Kategori</b> di navigasi atas.</li>
            <li>Bisa tambah, edit, hapus kategori.</li>
            <li>Urutan kategori bisa diubah di kolom "Urutan".</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. Mengelola Pertanyaan</h2>
        <ul>
            <li>Klik menu <b>Pertanyaan</b>.</li>
            <li>Bisa tambah, edit, hapus pertanyaan.</li>
            <li>Setiap pertanyaan bisa dihubungkan ke kategori & survey tertentu.</li>
            <li>Tipe pertanyaan: teks, skala, pilihan ganda.</li>
        </ul>
    </div>

    <div class="section">
        <h2>5. Mengelola Survey</h2>
        <ul>
            <li>Klik menu <b>Admin &gt; Survey</b>.</li>
            <li>Bisa tambah, edit, hapus survey.</li>
            <li>Survey bisa diaktifkan/nonaktifkan.</li>
        </ul>
    </div>

    <div class="section">
        <h2>6. Melihat & Mengekspor Hasil Survei</h2>
        <ul>
            <li>Di dashboard, klik <b>Export Data Lengkap</b> untuk unduh semua hasil (CSV).</li>
            <li>Gunakan <b>Export Summary</b> untuk rekap singkat.</li>
            <li>Gunakan <b>Cetak PDF</b> untuk rekap dashboard.</li>
            <li>Statistik bisa difilter berdasarkan survey, kategori, tanggal.</li>
        </ul>
    </div>

    <div class="section">
        <h2>7. Sistem Pembatasan Survey</h2>
        <ul>
            <li><b>Metode Pembatasan:</b> IP Address + User Agent + Survey ID + Tanggal</li>
            <li><b>Pembatasan:</b> Setiap device/komputer hanya dapat mengisi survey 1 kali per hari untuk survey yang sama</li>
            <li><b>Tujuan:</b> Mencegah duplikasi pengisian survey dari device yang sama</li>
            <li><b>Tracking:</b> Semua pengisian dicatat dengan IP Address dan User Agent untuk monitoring</li>
            <li><b>Pesan Error:</b> Jika user mencoba mengisi ulang, akan muncul pesan "Anda sudah mengisi survey ini hari ini"</li>
        </ul>
    </div>

    <div class="section">
        <h2>8. Data yang Dikumpulkan</h2>
        <ul>
            <li><b>Data Demografis:</b> Jenis kelamin, umur, pendidikan, unit kerja, jabatan fungsional</li>
            <li><b>Data Survey:</b> Tingkat kepuasan (1-4), tingkat kepentingan (1-4), saran tambahan</li>
            <li><b>Data Teknis:</b> IP Address, User Agent, timestamp pengisian</li>
            <li><b>Data Analisis:</b> Gap analysis, statistik per pertanyaan, distribusi jawaban</li>
        </ul>
    </div>

    <div class="section">
        <h2>9. Logout</h2>
        <ul>
            <li>Klik nama Anda di pojok kanan atas, pilih <b>Log Out</b>.</li>
        </ul>
    </div>

    <div class="section">
        <h2>⚙️ Dokumentasi Teknis (Pengembangan)</h2>
        <ul>
            <li><span class="subtitle">Struktur Utama:</span> <br>
                <b>routes/web.php</b>: route aplikasi<br>
                <b>app/Http/Controllers/</b>: logic controller<br>
                <b>app/Models/</b>: model Eloquent<br>
                <b>resources/views/</b>: file tampilan<br>
                <b>database/migrations/</b>: struktur tabel<br>
                <b>database/seeders/</b>: data awal/dummy
            </li>
            <li><span class="subtitle">Fitur Utama:</span> multi-survey, role-based access, export, statistik otomatis.</li>
            <li><span class="subtitle">Pengembangan Lanjutan:</span> notifikasi email, rate limit, captcha, API, kustomisasi tampilan.</li>
            <li><span class="subtitle">Deployment:</span> <br>
                <b>.env</b>: <span class="code">APP_ENV=production</span>, <span class="code">APP_DEBUG=false</span><br>
                Gunakan HTTPS di server production.<br>
                Backup database berkala.
            </li>
        </ul>
        <p><b>Catatan:</b> Untuk menambah admin baru, tambahkan user di database dan set <span class="code">role</span> ke <span class="code">admin</span>.<br>
        Jika ada error, cek log di <span class="code">storage/logs/laravel.log</span>.</p>
    </div>
</body>
</html>
