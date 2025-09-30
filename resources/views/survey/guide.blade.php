@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-2xl rounded-2xl p-8 border-2 border-green-100">
            <h1 class="text-2xl font-extrabold mb-6 text-green-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Panduan Pengisian Form Survey
            </h1>
            <ol class="list-decimal ml-8 space-y-4 text-lg text-gray-800">
                <li>
                    <b class="text-green-700">Akses Halaman Survey:</b> Buka <span class="text-blue-600">/survey</span> tanpa perlu login.
                </li>
                <li>
                    <b class="text-green-700">Pilih Survei:</b> Pilih judul survei yang ingin diisi pada dropdown "Pilih Survei" (jika tersedia lebih dari satu).
                </li>
                <li>
                    <b class="text-green-700">Jawab Pertanyaan:</b><br>
                    <ul class="list-disc ml-6 mt-2 space-y-1 text-base">
                        <li>Pada setiap pertanyaan, pilih <b>Tingkat Kepuasan</b> dan <b>Tingkat Kepentingan</b> sesuai penilaian Anda (skala 1–4).</li>
                        <li>Skala 1 = Sangat Rendah, 2 = Rendah, 3 = Tinggi, 4 = Sangat Tinggi.</li>
                        <li>Jika tersedia, isi juga kolom <b>Saran/Masukan</b> di bagian akhir form.</li>
                        <li>Kolom bertanda <span class="text-red-600">*</span> wajib diisi.</li>
                    </ul>
                </li>
                <li>
                    <b class="text-green-700">Cek Kembali Jawaban:</b> Pastikan semua jawaban sudah benar sebelum mengirim.
                </li>
                <li>
                    <b class="text-orange-700">Pembatasan Pengisian:</b> Setiap device/komputer hanya dapat mengisi survey 1 kali per hari untuk survey yang sama.
                </li>
                <li>
                    <b class="text-green-700">Kirim Survey:</b> Klik tombol <b>Kirim</b> di bagian bawah form. Jika ada error, lengkapi lalu kirim ulang.
                </li>
                <li>
                    <b class="text-green-700">Notifikasi Sukses:</b> Setelah berhasil, akan muncul pesan “Terima kasih sudah mengisi survei!”.
                </li>
                <li>
                    <b class="text-green-700">Privasi:</b> Data Anda bersifat anonim, tidak perlu login atau memasukkan identitas pribadi kecuali diminta di form.
                </li>
            </ol>
            <div class="mt-8 text-base text-gray-600">
                Jika ada kendala, hubungi admin melalui kontak yang tersedia di halaman utama.
            </div>
        </div>
    </div>
</div>
@endsection
