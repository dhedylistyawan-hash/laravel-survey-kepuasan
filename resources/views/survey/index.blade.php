@extends('layouts.app')

@section('content')
<div id="survey-loading" class="fixed inset-0 bg-white/80 dark:bg-gray-900/80 hidden items-center justify-center z-50">
    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500 border-solid"></div>
</div>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-2xl rounded-2xl p-8 border-2 border-blue-100">
            <h1 class="text-lg md:text-2xl lg:text-3xl font-semibold mb-8 text-center text-blue-800 flex items-center justify-center gap-2 whitespace-nowrap">
                <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Form Survei Kepuasan Pengguna Layanan
            </h1>
            <div class="mb-4 p-3 bg-orange-50 border border-orange-200 text-orange-700 rounded-lg text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span><strong>Pembatasan:</strong> Setiap device/komputer hanya dapat mengisi survey 1 kali per hari untuk survey yang sama.</span>
                </div>
            </div>
            <!-- Dropdown Pilih Survei -->
            <form method="GET" action="{{ route('survey.index') }}" class="mb-8 flex gap-4 items-end bg-blue-50 p-4 rounded-xl border border-blue-100">
                <div class="flex-1">
                    <label for="survey_id" class="block text-sm font-bold text-blue-700 mb-1">Pilih Survei</label>
                    <select name="survey_id" id="survey_id" class="form-select w-full border-2 border-blue-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition" onchange="this.form.submit()">
                        <option value="">Pilih Survei</option>
                        @foreach($surveys as $survey)
                            <option value="{{ $survey->id }}" {{ $selectedSurveyId == $survey->id ? 'selected' : '' }}>{{ $survey->title }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-semibold">Terjadi kesalahan:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($categories->count() > 0 && !empty($selectedSurveyId))
            <div id="survey-questions" style="display:none;">
                <!-- Progress Bar Survey -->
                <div class="mb-8">
                    <div class="w-full bg-blue-100 dark:bg-gray-800 rounded-full h-4 shadow-inner">
                        <div id="survey-progress-bar" class="bg-gradient-to-r from-blue-400 to-green-400 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-xs mt-1 text-blue-700 dark:text-gray-200">
                        <span id="progress-label">0%</span>
                        <span>Selesai</span>
                    </div>
                </div>
                <form action="{{ route('survey.store') }}" method="POST" id="surveyForm">
                    @csrf
                    <input type="hidden" name="survey_id" value="{{ $selectedSurveyId }}">
                    <div class="mb-8 bg-white rounded-2xl shadow-xl p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border border-blue-100">
                        <div class="question-group mb-4">
                            <label class="font-bold block mb-2 text-blue-700 text-base">Jenis Kelamin:</label>
                            <select name="jenis_kelamin" required class="form-select w-full border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 px-4 py-2 hover:shadow-lg hover:border-blue-400">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="question-group mb-4">
                            <label class="font-bold block mb-2 text-blue-700 text-base">Umur:</label>
                            <select name="umur" required class="form-select w-full border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 px-4 py-2 hover:shadow-lg hover:border-blue-400">
                                <option value="">Pilih Umur</option>
                                <option value="<30">&lt;30</option>
                                <option value="31-40">31-40</option>
                                <option value="41-50">41-50</option>
                                <option value=">50">&gt;50</option>
                            </select>
                        </div>
                        <div class="question-group mb-4">
                            <label class="font-bold block mb-2 text-blue-700 text-base">Pendidikan Terakhir:</label>
                            <select name="pendidikan" required class="form-select w-full border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 px-4 py-2 hover:shadow-lg hover:border-blue-400">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SMA">SMA</option>
                                <option value="D-I">D-I</option>
                                <option value="D-II">D-II</option>
                                <option value="D-III">D-III</option>
                                <option value="D-IV/S-1">D-IV/S-1</option>
                                <option value="S-2">S-2</option>
                                <option value="S-3">S-3</option>
                            </select>
                        </div>
                        <div class="question-group mb-4">
                            <label class="font-bold block mb-2 text-blue-700 text-base">Unit Kerja:</label>
                            <select name="unit_kerja" required class="form-select w-full border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 px-4 py-2 hover:shadow-lg hover:border-blue-400">
                                <option value="">Pilih Unit Kerja</option>
                                <option value="Biro Perencanaan">Biro Perencanaan</option>
                                <option value="Biro Hukum, Hubungan Masyarakat, dan Kerjasama">Biro Hukum, Hubungan Masyarakat, dan Kerjasama</option>
                                <option value="Biro Umum dan Keuangan">Biro Umum dan Keuangan</option>
                                <option value="Biro Sumber Daya Manusia dan Organisasi">Biro Sumber Daya Manusia dan Organisasi</option>
                                <option value="Inspektorat">Inspektorat</option>
                                <option value="Direktorat Meteorologi Penerbangan">Direktorat Meteorologi Penerbangan</option>
                                <option value="Direktorat Meteorologi Maritim">Direktorat Meteorologi Maritim</option>
                                <option value="Direktorat Meteorologi Publik">Direktorat Meteorologi Publik</option>
                                <option value="Direktorat Perubahan Iklim">Direktorat Perubahan Iklim</option>
                                <option value="Direktorat Layanan Iklim Terapan">Direktorat Layanan Iklim Terapan</option>
                                <option value="Direktorat Gempa Bumi dan Tsunami">Direktorat Gempa Bumi dan Tsunami</option>
                                <option value="Direktorat Seismologi Teknik, Geofisika Potensial, dan Tanda Waktu">Direktorat Seismologi Teknik, Geofisika Potensial, dan Tanda Waktu</option>
                                <option value="Direktorat Instrumentasi dan Kalibrasi">Direktorat Instrumentasi dan Kalibrasi</option>
                                <option value="Direktorat Data dan Komputasi">Direktorat Data dan Komputasi</option>
                                <option value="Direktorat Sistem dan Jaringan Komunikasi">Direktorat Sistem dan Jaringan Komunikasi</option>
                                <option value="Direktorat Tata Kelola Modifikasi Cuaca">Direktorat Tata Kelola Modifikasi Cuaca</option>
                                <option value="Direktorat Operasional Modifikasi Cuaca">Direktorat Operasional Modifikasi Cuaca</option>
                                <option value="Pusat Standardisasi Instrumen MKG">Pusat Standardisasi Instrumen MKG</option>
                                <option value="Pusat Pengembangan Sumber Daya Manusia MKG">Pusat Pengembangan Sumber Daya Manusia MKG</option>
                                <option value="Pusat Pembinaan Jabatan Fungsional MKG">Pusat Pembinaan Jabatan Fungsional MKG</option>
                                <option value="STMKG">STMKG</option>
                                <option value="Sekretariat Korpri Unit Nasional">Sekretariat Korpri Unit Nasional</option>
                                <option value="Balai Besar Meteorologi Klimatologi dan Geofisika Wil. I - Medan">Balai Besar Meteorologi Klimatologi dan Geofisika Wil. I - Medan</option>
                                <option value="Sta. Geof. Kelas I Padang Panjang">Sta. Geof. Kelas I Padang Panjang</option>
                                <option value="Sta. Klim. Kelas II Sumatera Barat">Sta. Klim. Kelas II Sumatera Barat</option>
                                <option value="Sta. Met. Kelas II Minangkabau - Padang Pariaman">Sta. Met. Kelas II Minangkabau - Padang Pariaman</option>
                                <option value="Sta. Met. Kelas IV Maritim Teluk Bayur - Padang">Sta. Met. Kelas IV Maritim Teluk Bayur - Padang</option>
                                <option value="Sta. Pemantau Atmosfer Global Bukit Koto Tabang - Agam">Sta. Pemantau Atmosfer Global Bukit Koto Tabang - Agam</option>
                                <option value="Sta. Geof. Kelas I Deli Serdang">Sta. Geof. Kelas I Deli Serdang</option>
                                <option value="Sta. Geof. Kelas III Gunung Sitoli">Sta. Geof. Kelas III Gunung Sitoli</option>
                                <option value="Sta. Klim. Kelas I Sumatera Utara">Sta. Klim. Kelas I Sumatera Utara</option>
                                <option value="Sta. Met. Kelas I Kualanamu - Deli Serdang">Sta. Met. Kelas I Kualanamu - Deli Serdang</option>
                                <option value="Sta. Met. Kelas II Maritim Belawan - Medan">Sta. Met. Kelas II Maritim Belawan - Medan</option>
                                <option value="Sta. Met. Kelas III Binaka - Gunung Sitoli">Sta. Met. Kelas III Binaka - Gunung Sitoli</option>
                                <option value="Sta. Met. Kelas III F.L. Tobing - Tapanuli Tengah">Sta. Met. Kelas III F.L. Tobing - Tapanuli Tengah</option>
                                <option value="Sta. Met. Kelas IV Aek Godang - Padang Sidempuan">Sta. Met. Kelas IV Aek Godang - Padang Sidempuan</option>
                                <option value="Sta. Met. Kelas II Silangit - Tapanuli Utara">Sta. Met. Kelas II Silangit - Tapanuli Utara</option>
                                <option value="Sta. Geof. Kelas III Aceh Besar">Sta. Geof. Kelas III Aceh Besar</option>
                                <option value="Sta. Geof. Kelas III Aceh Selatan">Sta. Geof. Kelas III Aceh Selatan</option>
                                <option value="Sta. Klim. Kelas IV Aceh">Sta. Klim. Kelas IV Aceh</option>
                                <option value="Sta. Met. Kelas I Sultan Iskandar Muda - Banda Aceh">Sta. Met. Kelas I Sultan Iskandar Muda - Banda Aceh</option>
                                <option value="Sta. Met. Kelas III Maimun Saleh - Sabang">Sta. Met. Kelas III Maimun Saleh - Sabang</option>
                                <option value="Sta. Met. Kelas III Malikussaleh - Aceh Utara">Sta. Met. Kelas III Malikussaleh - Aceh Utara</option>
                                <option value="Sta. Met. Kelas III Cut Nyak Dhien - Nagan Raya">Sta. Met. Kelas III Cut Nyak Dhien - Nagan Raya</option>
                                <option value="Sta. Met. Kelas I Hang Nadim - Batam">Sta. Met. Kelas I Hang Nadim - Batam</option>
                                <option value="Sta. Met. Kelas III Dabo - Lingga">Sta. Met. Kelas III Dabo - Lingga</option>
                                <option value="Sta. Met. Kelas III Raja Haji Fisabilillah - Tanjung Pinang">Sta. Met. Kelas III Raja Haji Fisabilillah - Tanjung Pinang</option>
                                <option value="Sta. Met. Kelas III Maritim Natuna">Sta. Met. Kelas III Maritim Natuna</option>
                                <option value="Sta. Met. Kelas III Tarempa - Kepulauan Anambas">Sta. Met. Kelas III Tarempa - Kepulauan Anambas</option>
                                <option value="Sta. Met. Kelas IV Raja Haji Abdullah - Karimun">Sta. Met. Kelas IV Raja Haji Abdullah - Karimun</option>
                                <option value="Sta. Met. Kelas I Sultan Syarif Kasim II - Pekanbaru">Sta. Met. Kelas I Sultan Syarif Kasim II - Pekanbaru</option>
                                <option value="Sta. Met. Kelas III Japura - Indragiri Hulu">Sta. Met. Kelas III Japura - Indragiri Hulu</option>
                                <option value="Sta. Klim. Kelas IV Riau">Sta. Klim. Kelas IV Riau</option>
                                <option value="Balai Besar Meteorologi Klimatologi dan Geofisika Wil. II - Tangerang Selatan">Balai Besar Meteorologi Klimatologi dan Geofisika Wil. II - Tangerang Selatan</option>
                                <option value="Sta. Klim. Kelas I Jawa Barat">Sta. Klim. Kelas I Jawa Barat</option>
                                <option value="Sta. Met. Kelas III Kertajati - Majalengka">Sta. Met. Kelas III Kertajati - Majalengka</option>
                                <option value="Sta. Met. Kelas III Citeko - Bogor">Sta. Met. Kelas III Citeko - Bogor</option>
                                <option value="Sta. Geof. Kelas I Bandung">Sta. Geof. Kelas I Bandung</option>
                                <option value="Sta. Geof. Kelas III Sukabumi">Sta. Geof. Kelas III Sukabumi</option>
                                <option value="Sta. Geof. Kelas I Tangerang">Sta. Geof. Kelas I Tangerang</option>
                                <option value="Sta. Klim. Kelas II Banten">Sta. Klim. Kelas II Banten</option>
                                <option value="Sta. Met. Kelas I Soekarno Hatta - Tangerang">Sta. Met. Kelas I Soekarno Hatta - Tangerang</option>
                                <option value="Sta. Met. Kelas III Budiarto - Tangerang">Sta. Met. Kelas III Budiarto - Tangerang</option>
                                <option value="Sta. Met. Kelas I Maritim Merak - Cilegon">Sta. Met. Kelas I Maritim Merak - Cilegon</option>
                                <option value="Sta. Met. Kelas II Sultan Mahmud Badaruddin II - Palembang">Sta. Met. Kelas II Sultan Mahmud Badaruddin II - Palembang</option>
                                <option value="Sta. Klim. Kelas I Sumatera Selatan">Sta. Klim. Kelas I Sumatera Selatan</option>
                                <option value="Sta. Geof. Kelas III Banjarnegara">Sta. Geof. Kelas III Banjarnegara</option>
                                <option value="Sta. Klim. Kelas I Jawa Tengah">Sta. Klim. Kelas I Jawa Tengah</option>
                                <option value="Sta. Met. Kelas II Ahmad Yani - Semarang">Sta. Met. Kelas II Ahmad Yani - Semarang</option>
                                <option value="Sta. Met. Kelas III Maritim Tegal">Sta. Met. Kelas III Maritim Tegal</option>
                                <option value="Sta. Met. Kelas III Tunggul Wulung - Cilacap">Sta. Met. Kelas III Tunggul Wulung - Cilacap</option>
                                <option value="Sta. Met. Kelas II Maritim Tanjung Emas - Semarang">Sta. Met. Kelas II Maritim Tanjung Emas - Semarang</option>
                                <option value="Sta. Geof. Kelas III Kepahiang">Sta. Geof. Kelas III Kepahiang</option>
                                <option value="Sta. Klim. Kelas I Bengkulu">Sta. Klim. Kelas I Bengkulu</option>
                                <option value="Sta. Met. Kelas III Fatmawati Soekarno - Bengkulu">Sta. Met. Kelas III Fatmawati Soekarno - Bengkulu</option>
                                <option value="Sta. Geof. Kelas III Lampung Utara">Sta. Geof. Kelas III Lampung Utara</option>
                                <option value="Sta. Klim. Kelas IV Lampung">Sta. Klim. Kelas IV Lampung</option>
                                <option value="Sta. Met. Kelas I Radin Inten II - Lampung Selatan">Sta. Met. Kelas I Radin Inten II - Lampung Selatan</option>
                                <option value="Sta. Met. Kelas IV Maritim Panjang - Bandar Lampung">Sta. Met. Kelas IV Maritim Panjang - Bandar Lampung</option>
                                <option value="Sta. Klim. Kelas IV Jambi">Sta. Klim. Kelas IV Jambi</option>
                                <option value="Sta. Met. Kelas III Depati Parbo - Kerinci">Sta. Met. Kelas III Depati Parbo - Kerinci</option>
                                <option value="Sta. Met. Kelas I Sultan Thaha - Jambi">Sta. Met. Kelas I Sultan Thaha - Jambi</option>
                                <option value="Sta. Met. Kelas I Maritim Tanjung Priok - Jakarta Utara">Sta. Met. Kelas I Maritim Tanjung Priok - Jakarta Utara</option>
                                <option value="Sta. Met. Kelas III Pangsuma - Kapuas Hulu">Sta. Met. Kelas III Pangsuma - Kapuas Hulu</option>
                                <option value="Sta. Met. Kelas I Supadio - Pontianak">Sta. Met. Kelas I Supadio - Pontianak</option>
                                <option value="Sta. Met. Kelas III Singkawang">Sta. Met. Kelas III Singkawang</option>
                                <option value="Sta. Met. Kelas III Nangapinoh - Melawi">Sta. Met. Kelas III Nangapinoh - Melawi</option>
                                <option value="Sta. Met. Kelas III Rahadi Oesman - Ketapang">Sta. Met. Kelas III Rahadi Oesman - Ketapang</option>
                                <option value="Sta. Met. Kelas III Tebelian Sintang">Sta. Met. Kelas III Tebelian Sintang</option>
                                <option value="Sta. Met. Kelas IV Maritim Pontianak">Sta. Met. Kelas IV Maritim Pontianak</option>
                                <option value="Sta. Klim. Kelas II Kalimantan Barat">Sta. Klim. Kelas II Kalimantan Barat</option>
                                <option value="Sta. Geof. Kelas I Sleman">Sta. Geof. Kelas I Sleman</option>
                                <option value="Sta. Klim. Kelas IV D.I Yogyakarta">Sta. Klim. Kelas IV D.I Yogyakarta</option>
                                <option value="Sta. Met. Kelas II Yogyakarta">Sta. Met. Kelas II Yogyakarta</option>
                                <option value="Sta. Met. Kelas III H.AS. Hanandjoeddin - Belitung">Sta. Met. Kelas III H.AS. Hanandjoeddin - Belitung</option>
                                <option value="Sta. Met. Kelas I Depati Amir - Pangkal Pinang">Sta. Met. Kelas I Depati Amir - Pangkal Pinang</option>
                                <option value="Sta. Klim. Kelas IV Bangka Belitung">Sta. Klim. Kelas IV Bangka Belitung</option>
                                <option value="Balai Besar Meteorologi Klimatologi dan Geofisika Wil. III - Badung">Balai Besar Meteorologi Klimatologi dan Geofisika Wil. III - Badung</option>
                                <option value="Sta. Geof. Kelas I Kupang">Sta. Geof. Kelas I Kupang</option>
                                <option value="Sta. Geof. Kelas III Sumba Timur">Sta. Geof. Kelas III Sumba Timur</option>
                                <option value="Sta. Klim. Kelas II Nusa Tenggara Timur">Sta. Klim. Kelas II Nusa Tenggara Timur</option>
                                <option value="Sta. Met. Kelas II El Tari - Kupang">Sta. Met. Kelas II El Tari - Kupang</option>
                                <option value="Sta. Met. Kelas III Gewanyantana - Flores Timur">Sta. Met. Kelas III Gewanyantana - Flores Timur</option>
                                <option value="Sta. Met. Kelas III David Constantijn Saudale - Rote Ndao">Sta. Met. Kelas III David Constantijn Saudale - Rote Ndao</option>
                                <option value="Sta. Met. Kelas III Mali - Alor">Sta. Met. Kelas III Mali - Alor</option>
                                <option value="Sta. Met. Kelas III Umbu Mehang Kunda - Sumba Timur">Sta. Met. Kelas III Umbu Mehang Kunda - Sumba Timur</option>
                                <option value="Sta. Met. Kelas III Frans Sales Lega - Manggarai">Sta. Met. Kelas III Frans Sales Lega - Manggarai</option>
                                <option value="Sta. Met. Kelas III Tardamu - Sabu Raijua">Sta. Met. Kelas III Tardamu - Sabu Raijua</option>
                                <option value="Sta. Met. Kelas III Fransiskus Xaverius Seda - Sikka">Sta. Met. Kelas III Fransiskus Xaverius Seda - Sikka</option>
                                <option value="Sta. Met. Kelas IV Komodo - Manggarai Barat">Sta. Met. Kelas IV Komodo - Manggarai Barat</option>
                                <option value="Sta. Geof. Kelas III Alor">Sta. Geof. Kelas III Alor</option>
                                <option value="Sta. Met. Kelas IV Maritim Tenau - Kupang">Sta. Met. Kelas IV Maritim Tenau - Kupang</option>
                                <option value="Sta. Geof. Kelas II Denpasar">Sta. Geof. Kelas II Denpasar</option>
                                <option value="Sta. Klim. Kelas II Bali">Sta. Klim. Kelas II Bali</option>
                                <option value="Sta. Met. Kelas I I Gusti Ngurah Rai - Badung">Sta. Met. Kelas I I Gusti Ngurah Rai - Badung</option>
                                <option value="Sta. Geof. Kelas II Pasuruan">Sta. Geof. Kelas II Pasuruan</option>
                                <option value="Sta. Geof. Kelas III Malang">Sta. Geof. Kelas III Malang</option>
                                <option value="Sta. Geof. Kelas III Nganjuk">Sta. Geof. Kelas III Nganjuk</option>
                                <option value="Sta. Klim. Kelas II Jawa Timur">Sta. Klim. Kelas II Jawa Timur</option>
                                <option value="Sta. Met. Kelas I Juanda - Sidoarjo">Sta. Met. Kelas I Juanda - Sidoarjo</option>
                                <option value="Sta. Met. Kelas II Maritim Tanjung Perak - Surabaya">Sta. Met. Kelas II Maritim Tanjung Perak - Surabaya</option>
                                <option value="Sta. Met. Kelas III Banyuwangi">Sta. Met. Kelas III Banyuwangi</option>
                                <option value="Sta. Met. Kelas III Trunojoyo">Sta. Met. Kelas III Trunojoyo</option>
                                <option value="Sta. Met. Kelas III Tuban">Sta. Met. Kelas III Tuban</option>
                                <option value="Sta. Met. Kelas III Sangkapura - Gresik">Sta. Met. Kelas III Sangkapura - Gresik</option>
                                <option value="Sta. Met. Kelas III Dhoho - Kediri">Sta. Met. Kelas III Dhoho - Kediri</option>
                                <option value="Sta. Geof. Kelas III Balikpapan">Sta. Geof. Kelas III Balikpapan</option>
                                <option value="Sta. Met. Kelas I Sultan Aji Muhammad Sulaiman Sepinggan - Balikpapan">Sta. Met. Kelas I Sultan Aji Muhammad Sulaiman Sepinggan - Balikpapan</option>
                                <option value="Sta. Met. Kelas III Kalimarau - Berau">Sta. Met. Kelas III Kalimarau - Berau</option>
                                <option value="Sta. Met. Kelas III Aji Pangeran Tumenggung Pranoto Samarinda">Sta. Met. Kelas III Aji Pangeran Tumenggung Pranoto Samarinda</option>
                                <option value="Sta. Klim. Kelas I Kalimantan Selatan">Sta. Klim. Kelas I Kalimantan Selatan</option>
                                <option value="Sta. Met. Kelas II Syamsudin Noor - Banjarmasin">Sta. Met. Kelas II Syamsudin Noor - Banjarmasin</option>
                                <option value="Sta. Met. Kelas III Gusti Syamsir Alam - Kotabaru">Sta. Met. Kelas III Gusti Syamsir Alam - Kotabaru</option>
                                <option value="Sta. Klim. Kelas I Nusa Tenggara Barat">Sta. Klim. Kelas I Nusa Tenggara Barat</option>
                                <option value="Sta. Met. Kelas II Zainuddin Abdul Madjid - Lombok">Sta. Met. Kelas II Zainuddin Abdul Madjid - Lombok</option>
                                <option value="Sta. Met. Kelas III Sultan Muhammad Salahuddin - Bima">Sta. Met. Kelas III Sultan Muhammad Salahuddin - Bima</option>
                                <option value="Sta. Met. Kelas III Sultan Muhammad Kaharuddin - Sumbawa">Sta. Met. Kelas III Sultan Muhammad Kaharuddin - Sumbawa</option>
                                <option value="Sta. Geof. Kelas III Mataram">Sta. Geof. Kelas III Mataram</option>
                                <option value="Sta. Met. Kelas III Beringin - Barito Utara">Sta. Met. Kelas III Beringin - Barito Utara</option>
                                <option value="Sta. Met. Kelas III Iskandar - Kotawaringin Barat">Sta. Met. Kelas III Iskandar - Kotawaringin Barat</option>
                                <option value="Sta. Met. Kelas I Tjilik Riwut - Palangka Raya">Sta. Met. Kelas I Tjilik Riwut - Palangka Raya</option>
                                <option value="Sta. Met. Kelas IV Sanggu - Barito Selatan">Sta. Met. Kelas IV Sanggu - Barito Selatan</option>
                                <option value="Sta. Met. Kelas IV H. Asan - Kotawaringin Timur">Sta. Met. Kelas IV H. Asan - Kotawaringin Timur</option>
                                <option value="Sta. Met. Kelas III Juwata - Tarakan">Sta. Met. Kelas III Juwata - Tarakan</option>
                                <option value="Sta. Met. Kelas III Tanjung Harapan - Bulungan">Sta. Met. Kelas III Tanjung Harapan - Bulungan</option>
                                <option value="Sta. Met. Kelas III Yuvai Semaring - Nunukan">Sta. Met. Kelas III Yuvai Semaring - Nunukan</option>
                                <option value="Sta. Met. Kelas IV Nunukan">Sta. Met. Kelas IV Nunukan</option>
                                <option value="Balai Besar Meteorologi Klimatologi dan Geofisika Wil. IV - Makassar">Balai Besar Meteorologi Klimatologi dan Geofisika Wil. IV - Makassar</option>
                                <option value="Sta. Geof. Kelas I Ambon">Sta. Geof. Kelas I Ambon</option>
                                <option value="Sta. Geof. Kelas III Maluku Tenggara Barat">Sta. Geof. Kelas III Maluku Tenggara Barat</option>
                                <option value="Sta. Klim. Kelas III Maluku">Sta. Klim. Kelas III Maluku</option>
                                <option value="Sta. Met. Kelas II Pattimura - Ambon">Sta. Met. Kelas II Pattimura - Ambon</option>
                                <option value="Sta. Met. Kelas III Amahai - Maluku Tengah">Sta. Met. Kelas III Amahai - Maluku Tengah</option>
                                <option value="Sta. Met. Kelas III Bandaneira - Maluku Tengah">Sta. Met. Kelas III Bandaneira - Maluku Tengah</option>
                                <option value="Sta. Met. Kelas III Karel Sadsuitubun - Maluku Tenggara">Sta. Met. Kelas III Karel Sadsuitubun - Maluku Tenggara</option>
                                <option value="Sta. Met. Kelas III Kuffar Seram Bagian Timur">Sta. Met. Kelas III Kuffar Seram Bagian Timur</option>
                                <option value="Sta. Met. Kelas III Namlea - Buru">Sta. Met. Kelas III Namlea - Buru</option>
                                <option value="Sta. Met. Kelas III Mathilda Batlayeri - Maluku Tenggara Barat">Sta. Met. Kelas III Mathilda Batlayeri - Maluku Tenggara Barat</option>
                                <option value="Sta. Met. Kelas IV Maritim Ambon">Sta. Met. Kelas IV Maritim Ambon</option>
                                <option value="Sta. Geof. Kelas I Manado">Sta. Geof. Kelas I Manado</option>
                                <option value="Sta. Klim. Kelas II Sulawesi Utara">Sta. Klim. Kelas II Sulawesi Utara</option>
                                <option value="Sta. Met. Kelas II Maritim Bitung">Sta. Met. Kelas II Maritim Bitung</option>
                                <option value="Sta. Met. Kelas II Sam Ratulangi - Manado">Sta. Met. Kelas II Sam Ratulangi - Manado</option>
                                <option value="Sta. Met. Kelas III Naha - Kepulauan Sangihe">Sta. Met. Kelas III Naha - Kepulauan Sangihe</option>
                                <option value="Sta. Geof. Kelas II Gowa">Sta. Geof. Kelas II Gowa</option>
                                <option value="Sta. Klim. Kelas I Sulawesi Selatan">Sta. Klim. Kelas I Sulawesi Selatan</option>
                                <option value="Sta. Met. Kelas I Sultan Hasanuddin - Makassar">Sta. Met. Kelas I Sultan Hasanuddin - Makassar</option>
                                <option value="Sta. Met. Kelas II Maritim Paotere - Makassar">Sta. Met. Kelas II Maritim Paotere - Makassar</option>
                                <option value="Sta. Met. Kelas III Andi Djemma - Luwu Utara">Sta. Met. Kelas III Andi Djemma - Luwu Utara</option>
                                <option value="Sta. Met. Kelas IV Toraja Tana Toraja">Sta. Met. Kelas IV Toraja Tana Toraja</option>
                                <option value="Sta. Geof. Kelas I Palu">Sta. Geof. Kelas I Palu</option>
                                <option value="Sta. Met. Kelas II Mutiara SIS Al-Jufrie - Palu">Sta. Met. Kelas II Mutiara SIS Al-Jufrie - Palu</option>
                                <option value="Sta. Met. Kelas III Syukuran Aminuddin Amir - Banggai">Sta. Met. Kelas III Syukuran Aminuddin Amir - Banggai</option>
                                <option value="Sta. Met. Kelas III Kasiguncu - Poso">Sta. Met. Kelas III Kasiguncu - Poso</option>
                                <option value="Sta. Met. Kelas III Sultan Bantilan - Tolitoli">Sta. Met. Kelas III Sultan Bantilan - Tolitoli</option>
                                <option value="Sta. Pemantau Atmosfer Global Lore Lindu Bariri - Poso">Sta. Pemantau Atmosfer Global Lore Lindu Bariri - Poso</option>
                                <option value="Sta. Geof. Kelas III Ternate">Sta. Geof. Kelas III Ternate</option>
                                <option value="Sta. Met. Kelas I Sultan Babullah - Ternate">Sta. Met. Kelas I Sultan Babullah - Ternate</option>
                                <option value="Sta. Met. Kelas III Gamarmalamo - Halmahera Utara">Sta. Met. Kelas III Gamarmalamo - Halmahera Utara</option>
                                <option value="Sta. Met. Kelas III Oesman Sadik - Halmahera Selatan">Sta. Met. Kelas III Oesman Sadik - Halmahera Selatan</option>
                                <option value="Sta. Met. Kelas III Emalamo - Kepulauan Sula">Sta. Met. Kelas III Emalamo - Kepulauan Sula</option>
                                <option value="Sta. Geof. Kelas IV Kendari">Sta. Geof. Kelas IV Kendari</option>
                                <option value="Sta. Met. Kelas III Betoambari - Baubau">Sta. Met. Kelas III Betoambari - Baubau</option>
                                <option value="Sta. Met. Kelas III Sangia Ni Bandera - Kolaka">Sta. Met. Kelas III Sangia Ni Bandera - Kolaka</option>
                                <option value="Sta. Met. Kelas II Maritim Kendari">Sta. Met. Kelas II Maritim Kendari</option>
                                <option value="Sta. Klim. Kelas IV Sulawesi Tenggara">Sta. Klim. Kelas IV Sulawesi Tenggara</option>
                                <option value="Sta. Met. Kelas I Djalaluddin - Gorontalo">Sta. Met. Kelas I Djalaluddin - Gorontalo</option>
                                <option value="Sta. Klim. Kelas IV Gorontalo">Sta. Klim. Kelas IV Gorontalo</option>
                                <option value="Sta. Geof. Kelas II Gorontalo">Sta. Geof. Kelas II Gorontalo</option>
                                <option value="Sta. Met. Kelas II Tampa Padang Mamuju">Sta. Met. Kelas II Tampa Padang Mamuju</option>
                                <option value="Balai Besar Meteorologi Klimatologi dan Geofisika Wil. V - Jayapura">Balai Besar Meteorologi Klimatologi dan Geofisika Wil. V - Jayapura</option>
                                <option value="Sta. Geof. Kelas I Jayapura">Sta. Geof. Kelas I Jayapura</option>
                                <option value="Sta. Geof. Kelas III Sorong">Sta. Geof. Kelas III Sorong</option>
                                <option value="Sta. Klim. Kelas III Papua">Sta. Klim. Kelas III Papua</option>
                                <option value="Sta. Met. Kelas I Frans Kaisiepo - Biak Numfor">Sta. Met. Kelas I Frans Kaisiepo - Biak Numfor</option>
                                <option value="Sta. Met. Kelas III Maritim Dok II - Jayapura">Sta. Met. Kelas III Maritim Dok II - Jayapura</option>
                                <option value="Sta. Met. Kelas III Enarotali - Paniai">Sta. Met. Kelas III Enarotali - Paniai</option>
                                <option value="Sta. Met. Kelas III Mararena - Sarmi">Sta. Met. Kelas III Mararena - Sarmi</option>
                                <option value="Sta. Met. Kelas III Mopah - Merauke">Sta. Met. Kelas III Mopah - Merauke</option>
                                <option value="Sta. Met. Kelas III Sudjarwo Tjondro Negoro - Kepulauan Yapen">Sta. Met. Kelas III Sudjarwo Tjondro Negoro - Kepulauan Yapen</option>
                                <option value="Sta. Met. Kelas III Tanah Merah - Boven Digul">Sta. Met. Kelas III Tanah Merah - Boven Digul</option>
                                <option value="Sta. Met. Kelas III Mozez Kilangin - Mimika">Sta. Met. Kelas III Mozez Kilangin - Mimika</option>
                                <option value="Sta. Met. Kelas III Wamena - Jayawijaya">Sta. Met. Kelas III Wamena - Jayawijaya</option>
                                <option value="Sta. Met. Kelas I Sentani - Jayapura">Sta. Met. Kelas I Sentani - Jayapura</option>
                                <option value="Sta. Met. Kelas III Nabire">Sta. Met. Kelas III Nabire</option>
                                <option value="Sta. Klim. Kelas IV Papua Selatan">Sta. Klim. Kelas IV Papua Selatan</option>
                                <option value="Sta. Geof. Kelas III Nabire">Sta. Geof. Kelas III Nabire</option>
                                <option value="Sta. Klim. Kelas III Papua Barat">Sta. Klim. Kelas III Papua Barat</option>
                                <option value="Sta. Met. Kelas I Domine Eduard Osok - Sorong">Sta. Met. Kelas I Domine Eduard Osok - Sorong</option>
                                <option value="Sta. Met. Kelas III Rendani - Manokwari">Sta. Met. Kelas III Rendani - Manokwari</option>
                                <option value="Sta. Met. Kelas III Torea - Fakfak">Sta. Met. Kelas III Torea - Fakfak</option>
                                <option value="Sta. Met. Kelas III Utarom - Kaimana">Sta. Met. Kelas III Utarom - Kaimana</option>
                                <option value="Sta. Pemantau Atmosfer Global Puncak Vihara Klademak - Sorong">Sta. Pemantau Atmosfer Global Puncak Vihara Klademak - Sorong</option>
                                <option value="Instansi Lain">Instansi Lain</option>
                            </select>
                        </div>
                        <div class="question-group mb-4">
                            <label class="font-bold block mb-2 text-blue-700 text-base">Jabatan Fungsional:</label>
                            <select name="jabatan_fungsional" required class="form-select w-full border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 px-4 py-2 hover:shadow-lg hover:border-blue-400">
                                <option value="">Pilih Jabatan</option>
                                <option value="Fungsional MKG">Fungsional MKG</option>
                                <option value="Fungsional Lain">Fungsional Lain</option>
                            </select>
                        </div>
                    </div>
                    <div class="rounded-2xl shadow-lg bg-white dark:bg-gray-800 mb-8 w-full max-w-none">
                        <table class="w-full max-w-none border-separate border-spacing-0 text-sm">
                            <thead class="bg-blue-100 dark:bg-gray-800 sticky top-0 z-20">
                                <tr>
                                    <th class="px-8 py-4 text-center bg-blue-50 font-semibold text-blue-900 w-1/3 border-r-4 border-white" rowspan="2" style="min-width:260px;">Pertanyaan</th>
                                    <th colspan="4" class="text-center font-bold text-base bg-green-100 border-r-4 border-white">Tingkat Kepuasan</th>
                                    <th colspan="4" class="text-center font-bold text-base bg-blue-100">Tingkat Kepentingan</th>
                                </tr>
                                <tr>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-green-50">Tidak Puas</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-green-100">Kurang Puas</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-green-200">Puas</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-green-300 border-r-4 border-white">Sangat Puas</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-blue-50">Tidak Penting</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-blue-100">Kurang Penting</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-blue-200">Penting</th>
                                    <th class="px-2 py-1 w-1/12 text-center font-medium text-gray-700 text-sm bg-blue-300">Sangat Penting</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($categories as $category)
                                    <tr>
                                        <td colspan="9" class="bg-gradient-to-r from-blue-100 via-blue-50 to-blue-100 dark:from-blue-900 dark:via-blue-800 dark:to-blue-900 font-semibold text-blue-700 dark:text-blue-200 sticky top-0 z-10 text-lg py-2 pl-6 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            {{ $category->name }}
                                        </td>
                                    </tr>
                                    @foreach($category->questions as $question)
                                    <tr class="question-group hover:bg-blue-50 dark:hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-3 align-top text-left w-1/3 text-blue-900 dark:text-blue-100 bg-blue-50 border-r-4 border-white" style="min-width:260px;">{{ $question->question_text }}</td>
                                        @for($i=1; $i<=4; $i++)
                                            <td class="px-1 py-1 w-1/12 text-center text-xs bg-green-50">
                                                <input type="radio" name="satisfaction[{{ $question->id }}]" value="{{ $i }}" required class="form-radio h-4 w-4 text-blue-500 focus:ring-blue-400 border-2 border-blue-300">
                                            </td>
                                        @endfor
                                        @for($i=1; $i<=4; $i++)
                                            <td class="px-1 py-1 w-1/12 text-center text-xs bg-blue-50">
                                                <input type="radio" name="importance[{{ $question->id }}]" value="{{ $i }}" required class="form-radio h-4 w-4 text-blue-500 focus:ring-blue-400 border-2 border-blue-300">
                                            </td>
                                        @endfor
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-8 mt-8">
                        <label for="suggestion" class="block font-bold mb-2">Saran tambahan :</label>
                        <textarea name="suggestion" id="suggestion" rows="3" class="form-input w-full border-2 border-blue-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" placeholder="Tulis saran Anda di sini..."></textarea>
                    </div>
                    <div class="flex justify-between items-center pt-8 border-t border-blue-100 mt-8">
                        <a href="/dashboard" class="bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 font-bold py-2 px-6 rounded-xl transition flex items-center shadow">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-bold py-2 px-8 rounded-xl transition flex items-center shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kirim Survei
                        </button>
                    </div>
                </form>
                <!-- Modal Konfirmasi Sebelum Submit -->
                <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden" style="display: none;">
                    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full border-t-4 border-blue-500">
                        <h2 class="text-xl font-bold mb-4 text-blue-700">Konfirmasi Pengiriman</h2>
                        <p class="mb-6 text-gray-700">Apakah Anda sudah yakin dengan semua jawaban yang diberikan?</p>
                        <div class="flex justify-end gap-4">
                            <button id="cancelSubmitBtn" type="button" class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold">Periksa Ulang</button>
                            <button id="confirmSubmitBtn" type="button" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold">Sudah, Kirim</button>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($selectedSurveyId)
                <div class="text-center text-gray-500 py-12">Belum ada pertanyaan untuk survei ini.</div>
            @endif
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('surveyForm');
    const questions = document.querySelectorAll('.question-group');
    
    // Debug: Log elements found
    console.log('Form found:', !!form);
    console.log('Questions found:', questions.length);
    console.log('Progress bar found:', !!document.getElementById('survey-progress-bar'));
    console.log('Progress label found:', !!document.getElementById('progress-label'));

    // Sembunyikan/tampilkan pertanyaan survey berdasarkan dropdown survei
    const surveySelect = document.getElementById('survey_id');
    const surveyQuestions = document.getElementById('survey-questions');
    
    function toggleSurveyQuestions() {
        if (surveySelect && surveyQuestions) {
            if (surveySelect.value) {
                surveyQuestions.style.display = 'block';
            } else {
                surveyQuestions.style.display = 'none';
            }
        }
    }
    
    if (surveySelect) {
        surveySelect.addEventListener('change', toggleSurveyQuestions);
        toggleSurveyQuestions();
    }

    // Validasi real-time
    questions.forEach(question => {
        const inputs = question.querySelectorAll('input');
        const selects = question.querySelectorAll('select');
        const errorDiv = question.querySelector('.error-message');

        inputs.forEach(input => {
            input.addEventListener('change', function() {
                validateQuestion(question);
            });

            input.addEventListener('blur', function() {
                validateQuestion(question);
            });
        });

        selects.forEach(select => {
            select.addEventListener('change', function() {
                validateQuestion(question);
            });

            select.addEventListener('blur', function() {
                validateQuestion(question);
            });
        });
    });

    // Validasi form sebelum submit
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;

            questions.forEach(question => {
                if (!validateQuestion(question)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (typeof showNotification === 'function') {
                    showNotification('Mohon lengkapi semua pertanyaan yang wajib diisi.', 'error');
                } else {
                    alert('Mohon lengkapi semua pertanyaan yang wajib diisi.');
                }
                return;
            }

            // Tampilkan modal konfirmasi sebelum submit
            e.preventDefault();
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.remove('hidden');
            }
        });
    }

    // Event pada tombol modal
    const cancelBtn = document.getElementById('cancelSubmitBtn');
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            }
        });
    }
    
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.add('hidden');
            }
            if (form) {
                form.submit();
            }
        });
    }

    function validateQuestion(question) {
        const inputs = question.querySelectorAll('input');
        const selects = question.querySelectorAll('select');
        const errorDiv = question.querySelector('.error-message');
        let hasValue = false;

        inputs.forEach(input => {
            if (input.type === 'radio' && input.checked) {
                hasValue = true;
            } else if (input.type === 'text' && input.value.trim() !== '') {
                hasValue = true;
            }
        });

        selects.forEach(select => {
            if (select.value && select.value !== '') {
                hasValue = true;
            }
        });

        if (!hasValue) {
            if (errorDiv) {
                errorDiv.textContent = 'Pertanyaan ini wajib diisi';
                errorDiv.classList.remove('hidden');
            }
            return false;
        } else {
            if (errorDiv) errorDiv.classList.add('hidden');
            return true;
        }
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Progress Bar Logic
    const progressBar = document.getElementById('survey-progress-bar');
    const progressLabel = document.getElementById('progress-label');
    
    function updateProgress() {
        if (!progressBar || !progressLabel || !questions) {
            return;
        }
        
        let total = questions.length;
        let answered = 0;
        questions.forEach(question => {
            if (validateQuestion(question)) answered++;
        });
        let percent = total === 0 ? 0 : Math.round((answered / total) * 100);
        progressBar.style.width = percent + '%';
        progressLabel.textContent = percent + '%';
    }
    // Initial update
    if (progressBar && progressLabel) {
        updateProgress();
    }
    
    // Update on input
    if (questions && questions.length > 0) {
        questions.forEach(question => {
            const inputs = question.querySelectorAll('input');
            const selects = question.querySelectorAll('select');
            inputs.forEach(input => {
                input.addEventListener('change', updateProgress);
                input.addEventListener('blur', updateProgress);
            });
            selects.forEach(select => {
                select.addEventListener('change', updateProgress);
                select.addEventListener('blur', updateProgress);
            });
        });
    }

    // Loading overlay logic
    const loading = document.getElementById('survey-loading');
    if(form && loading) {
        form.addEventListener('submit', function() {
            loading.classList.remove('hidden');
        });
    }

    // Update progress bar saat select berubah
    const allSelects = document.querySelectorAll('select');
    if (allSelects.length > 0) {
        allSelects.forEach(select => {
            select.addEventListener('change', updateProgress);
        });
    }
});
</script>
@endsection
