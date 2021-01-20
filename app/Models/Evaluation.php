<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotANoviceException;
use App\Exceptions\AbsentNoviceException;
use App\Exceptions\NotInstructorException;
use App\Exceptions\NoviceNotEnrolledException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['recorded_at'];

    public function isForInstructor(User $instructor)
    {
        throw_unless(
            $instructor->isInstructor(),
            NotInstructorException::class, 
            'Trying to check the evaluation belongs to a user that is not an instructor.'
        );

        return $instructor->id === $this->lesson->instructor->id;
    }

    public function record(array $gradesList)
    {
        $gradesList = $this->validateGradesList($gradesList);

        $gradesList->each(function ($item) {
            $this->recordGradeForNovice($item['novice'], $item['grade']);
        });

        $this->recorded_at = now();

        $this->save();
    }

    public function isRecorded()
    {
        return $this->recorded_at ? true : false;
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
            'Trying to record a grade for an novice that is not enrolled to the lesson'
        );

        throw_unless(
            $novice->presentForLesson($this->lesson),
            AbsentNoviceException::class, 
            'Trying to record a grade for a novice that is absent from the lesson'
        );

        $this->lesson->novices()->updateExistingPivot($novice->id, ['grade' => $grade]);
    }

    public function gradeForNovice(User $novice)
    {
        return $this->lesson->novices()->where('user_id', $novice->id)->first()->record->grade;
    }

    public function validateGradesList(array $gradesList)
    {
        $novices = User::whereIn('id', array_keys($gradesList))->get();

        $gradesList = $novices->map(function ($novice) use ($gradesList) {
            if ($this->lesson->isAbsent($novice)) {
                throw ValidationException::withMessages([
                    'grade' => 'Trying to record a grade for a novice that is absent from the lesson'
                ]);
            }
            
            return [
                'novice' => $novice,
                'grade' => $gradesList[$novice->id],
            ];
        });

        return $gradesList;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
