<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Response;
use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Carbon\Carbon;

class ResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surveys = Survey::all();
        $questions = Question::all();
        $users = User::all();

        if ($surveys->isEmpty() || $questions->isEmpty()) {
            $this->command->info('No surveys or questions found. Please run SurveySeeder and QuestionSeeder first.');
            return;
        }

        // Data untuk user yang login (10 data)
        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                $survey = $surveys->random();
                $surveyQuestions = $questions->where('survey_id', $survey->id);
                $sessionId = (string) \Illuminate\Support\Str::uuid();

                foreach ($surveyQuestions as $question) {
                    Response::create([
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                        'question_id' => $question->id,
                        'survey_id' => $survey->id,
                        'jenis_kelamin' => ['Laki-laki', 'Perempuan'][rand(0, 1)],
                        'umur' => ['<30', '31-40', '41-50', '>50'][rand(0, 3)],
                        'pendidikan' => ['SMA', 'D-I', 'D-II', 'D-III', 'D-IV/S-1', 'S-2', 'S-3'][rand(0, 6)],
                        'unit_kerja' => ['Biro Perencanaan', 'Biro Hukum, Hubungan Masyarakat, dan Kerjasama', 'Biro Umum dan Keuangan', 'Direktorat Operasional Modifikasi Cuaca'][rand(0, 3)],
                        'jabatan_fungsional' => ['Fungsional MKG', 'Fungsional Lain'][rand(0, 1)],
                        'importance' => rand(2, 4), // Lebih realistis: 2-4
                        'satisfaction' => rand(1, 4), // Variasi: 1-4
                        'suggestion' => 'Saran dari user ' . $user->name . ' - ' . $this->getRandomSuggestion(),
                        'ip_address' => $this->getRandomIP(),
                        'user_agent' => $this->getRandomUserAgent(),
                        'created_at' => Carbon::now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }

        // Data untuk tamu (40 data) - dengan variasi yang lebih baik
        $guestSurveys = $surveys->take(2); // Ambil 2 survey untuk tamu
        $guestCount = 0;

        foreach ($guestSurveys as $survey) {
            $guestCount++;
            $surveyQuestions = $questions->where('survey_id', $survey->id);

            // Buat 20 tamu untuk setiap survey (total 40 data)
            for ($tamu = 1; $tamu <= 20; $tamu++) {
                $sessionId = (string) \Illuminate\Support\Str::uuid();

                foreach ($surveyQuestions as $question) {
                    // Buat variasi yang lebih realistis untuk analisis
                    $importance = $this->getRealisticImportance();
                    $satisfaction = $this->getRealisticSatisfaction($importance);

                    Response::create([
                        'user_id' => null, // Tamu
                        'session_id' => $sessionId,
                        'question_id' => $question->id,
                        'survey_id' => $survey->id,
                        'jenis_kelamin' => ['Laki-laki', 'Perempuan'][rand(0, 1)],
                        'umur' => ['<30', '31-40', '41-50', '>50'][rand(0, 3)],
                        'pendidikan' => ['SMA', 'D-I', 'D-II', 'D-III', 'D-IV/S-1', 'S-2', 'S-3'][rand(0, 6)],
                        'unit_kerja' => ['Biro Perencanaan', 'Biro Hukum, Hubungan Masyarakat, dan Kerjasama', 'Biro Umum dan Keuangan', 'Direktorat Operasional Modifikasi Cuaca'][rand(0, 3)],
                        'jabatan_fungsional' => ['Fungsional MKG', 'Fungsional Lain'][rand(0, 1)],
                        'importance' => $importance,
                        'satisfaction' => $satisfaction,
                        'suggestion' => 'Saran dari tamu ' . $guestCount . '-' . $tamu . ' - ' . $this->getRandomSuggestion(),
                        'ip_address' => $this->getRandomIP(),
                        'user_agent' => $this->getRandomUserAgent(),
                        'created_at' => Carbon::now()->subDays(rand(1, 30))->addHours(rand(1, 24)),
                    ]);
                }
            }
        }

        $this->command->info('ResponseSeeder completed successfully! Generated 50+ dummy responses for analysis.');
    }

    /**
     * Generate realistic importance scores
     */
    private function getRealisticImportance()
    {
        $weights = [
            1 => 5,   // 5% chance
            2 => 15,  // 15% chance
            3 => 50,  // 50% chance (most common)
            4 => 30   // 30% chance
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $score => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $score;
            }
        }

        return 3; // Default
    }

    /**
     * Generate realistic satisfaction scores based on importance
     */
    private function getRealisticSatisfaction($importance)
    {
        // Satisfaction tends to be lower than importance (creating gaps for analysis)
        $baseSatisfaction = $importance - rand(0, 2);
        return max(1, min(4, $baseSatisfaction));
    }

    /**
     * Generate random IP addresses
     */
    private function getRandomIP()
    {
        $ips = [
            '192.168.1.' . rand(1, 254),
            '10.0.0.' . rand(1, 254),
            '172.16.' . rand(1, 254) . '.' . rand(1, 254),
            '203.0.113.' . rand(1, 254),
            '198.51.100.' . rand(1, 254),
        ];

        return $ips[array_rand($ips)];
    }

    /**
     * Generate random user agents
     */
    private function getRandomUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];

        return $userAgents[array_rand($userAgents)];
    }

    /**
     * Generate random suggestions
     */
    private function getRandomSuggestion()
    {
        $suggestions = [
            'Pelayanan sudah sangat baik, pertahankan kualitasnya',
            'Perlu peningkatan dalam kecepatan respon',
            'Informasi yang diberikan sudah jelas dan lengkap',
            'Saran untuk menambah fasilitas pendukung',
            'Komunikasi dengan pengguna perlu ditingkatkan',
            'Proses administrasi sudah efisien',
            'Perlu pelatihan tambahan untuk staf',
            'Infrastruktur sudah memadai',
            'Sistem informasi perlu diupdate',
            'Koordinasi antar unit sudah baik',
            'Perlu evaluasi berkala untuk perbaikan',
            'Layanan customer service sudah responsif',
            'Dokumentasi perlu diperbaiki',
            'Teknologi yang digunakan sudah modern',
            'Prosedur kerja sudah standar',
            'Perlu sosialisasi yang lebih intensif',
            'Monitoring dan evaluasi sudah rutin',
            'Kualitas output sudah memuaskan',
            'Perlu peningkatan kapasitas SDM',
            'Sistem pengawasan sudah efektif',
        ];

        return $suggestions[array_rand($suggestions)];
    }
}
