<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterLessonRequest extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    static public function for(Lesson $lesson, string $justification)
    {
        return $lesson->requests()->create([
            'justification' => $justification,
        ]);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
