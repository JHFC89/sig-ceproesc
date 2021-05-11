<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotExpectedLessonException;
use App\Exceptions\RequestNotReleasedException;
use App\Exceptions\LessonNotRegisteredException;
use App\Exceptions\RequestAlreadyReleasedException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['released_at'];

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getInstructorAttribute()
    {
        return $this->lesson->instructor;
    }

    static public function availableRequestTypeForLesson(Lesson $lesson)
    {
        return $lesson->isRegistered() ? 'rectification' : 'expiration';
    }

    static public function for(Lesson $lesson, string $justification)
    {
        if ($lesson->isExpired()) {
            return $lesson->requests()->create([
                'justification' => $justification,
                'rectification' => false,
            ]);
        }

        if ($lesson->isRegistered()) {
            return $lesson->requests()->create([
                'justification' => $justification,
            ]);
        }
    }

    public function isForInstructor(User $instructor)
    {
        return $this->instructor->id === $instructor->id;
    }

    public function release()
    {
        throw_if($this->fresh()->isReleased(), RequestAlreadyReleasedException::class, 'Trying to release a request already released');
        $this->released_at = now();
        $this->save();
    }

    public function solve(Lesson $lesson)
    {
        throw_unless($this->isReleased(), RequestNotReleasedException::class, 'Trying to solve a request that is not released');

        throw_unless(($lesson->id === $this->lesson->id), NotExpectedLessonException::class, 'Trying to solve a request for a lesson that is not registered yet');

        throw_unless($lesson->isRegistered(), LessonNotRegisteredException::class, 'Trying to solve a request for a lesson that is not registered yet');

        $this->solved_at = $lesson->registered_at;
        $this->save();
    }

    public function isRectification()
    {
        return boolval($this->rectification);
    }

    public function isReleased()
    {
        return $this->released_at ? true : false;
    }

    public function isSolved()
    {
        return $this->solved_at ? true : false;
    }

    public static function unsolvedRequestsForInstructor(User $instructor)
    {
        $lessons_ids = $instructor->lectures->pluck('id')->all();

        return self::query()->whereIn('lesson_id', $lessons_ids)
                            ->whereNull('solved_at')
                            ->get();
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function scopeWhereUnsolved($query)
    {
        return $query->whereNull('solved_at');
    }
}
