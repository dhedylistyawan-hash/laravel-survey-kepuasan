<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Category;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::pluck('id', 'name');

        $questions = [
            [
                'category' => 'Pelayanan',
                'question_text' => 'Bagaimana pendapat Anda tentang kecepatan pelayanan?'
            ],
            [
                'category' => 'Fasilitas',
                'question_text' => 'Apakah fasilitas yang tersedia sudah memadai?'
            ],
            [
                'category' => 'Petugas',
                'question_text' => 'Apakah petugas melayani dengan ramah dan sopan?'
            ],
        ];

        foreach ($questions as $q) {
            if (isset($categories[$q['category']])) {
                Question::updateOrCreate([
                    'category_id' => $categories[$q['category']],
                    'question_text' => $q['question_text'],
                ]);
            }
        }
    }
}
