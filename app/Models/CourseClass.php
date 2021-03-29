<?php

namespace App\Models;

use App\Models\CourseClassSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotANoviceException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Exceptions\CourseClassAlreadyHasLessonsException;

class CourseClass extends Model
{
    use HasFactory, CourseClassSchedule;

    protected $dates = [
        'begin',
        'end',
        'intro_begin',
        'intro_end',
        'vacation_begin',
        'vacation_end'
    ];

    public function getFirstDayAttribute()
    {
        return $this->first_theoretical_activity_day;
    }

    public function getSecondDayAttribute()
    {
        return $this->second_theoretical_activity_day;
    }

    public function getFirstDurationAttribute()
    {
        return $this->first_theoretical_activity_duration;
    }

    public function getSecondDurationAttribute()
    {
        return $this->second_theoretical_activity_duration;
    }

    public function createLessonsFromArray(array $lessons)
    {
        throw_if(
            $this->hasLessons(),
            CourseClassAlreadyHasLessonsException::class,
            'Trying to create Lessons for a CourseClass that already have Lessons.'
        );

        $lessons = collect($lessons)->map(function ($lesson) {
            return Lesson::fromArray($lesson);
        });

        $this->lessons()->attach($lessons->pluck('id')->toArray());

        return $lessons;
    }

    public function subscribe(User $novice)
    {
        throw_unless(
            $novice->isNovice(),
            NotANoviceException::class,
            'Trying to subscribe a user that is not a novice to a course class'
        );

        $this->novices()->save($novice);
    }

    public function isSubscribed(User $novice)
    {
        throw_unless(
            $novice->isNovice(),
            NotANoviceException::class,
            'Trying to check subscription for a user that is not a novice'
        );

        return $this->novices()->where('id', $novice->id)->count() > 0;
    }

    public function hasNovicesFor(User $employer)
    {
        $novices = $employer->novices->pluck('id')->toArray();

        return $this->novices->whereIn('id', $novices)->count() > 0;
    }

    public function hasLessons()
    {
        return $this->lessons->isNotEmpty();
    }

    public function offdays()
    {
        return $this->hasMany(Offday::class);
    }

    public function novices()
    {
        return $this->hasMany(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }
}
