<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\RequestAlreadyReleasedException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisterLessonRequest extends Model
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

    public function release()
    {
        throw_if($this->fresh()->isReleased(), RequestAlreadyReleasedException::class, 'Trying to release a request already released');
        $this->released_at = now();
        $this->save();
    }

    public function isReleased()
    {
        return $this->released_at ? true : false;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
