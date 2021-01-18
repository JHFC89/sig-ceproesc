<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotANoviceException;
use App\Exceptions\NotInstructorException;
use App\Exceptions\NoviceNotEnrolledException;
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

    public function recordGradeForNovice(User $novice, string $grade)
    {
        throw_unless(
            $novice->isNovice(),
            NotANoviceException::class, 
            'Trying to record a grade for an user that is not a novice.'
        );

        throw_unless(
            $this->lesson->isEnrolled($novice),
            NoviceNotEnrolledException::class, 
            'Trying to record a grade for an user that is not a novice.'
        );

        $this->lesson->novices()->updateExistingPivot($novice->id, ['grade' => $grade]);
    }

    public function gradeForNovice(User $novice)
    {
        return $this->lesson->novices()->where('user_id', $novice->id)->first()->record->grade;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
