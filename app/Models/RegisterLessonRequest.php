<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterLessonRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getInstructorAttribute()
    {
        return $this->lesson->instructor;
    }

    static public function for(Lesson $lesson, string $justification)
    {
        return $lesson->requests()->create([
            'justification' => $justification,
        ]);
    }

    public function isForInstructor(User $instructor)
    {
        return $this->instructor->id === $instructor->id;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
