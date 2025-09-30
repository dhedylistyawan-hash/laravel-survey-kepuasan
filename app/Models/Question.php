<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text', 'category_id', 'survey_id', 'type', 'options', 'scale_label_type',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function responses()
    {
        return $this->hasMany(\App\Models\Response::class);
    }
}
