<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotInstructorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function isForInstructor(User $instructor)
    {
        throw_unless(
            $instructor->isInstructor(),
            NotInstructorException::class, 
            'Trying to check the evaluation belongs to a user that is not an instructor.'
        );

        return $instructor->id === $this->lesson->instructor->id;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
