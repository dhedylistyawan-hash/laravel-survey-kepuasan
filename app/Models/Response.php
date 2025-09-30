<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'question_id',
        'survey_id',
        'answer',
        'importance',
        'satisfaction',
        'suggestion',
        'jenis_kelamin',
        'umur',
        'pendidikan',
        'unit_kerja',
        'jabatan_fungsional',
        'ip_address',
        'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
