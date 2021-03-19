<?php

namespace App\Models;

use App\Models\CourseClassSchedule;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotANoviceException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function subscribe(User $novice)
    {
        throw_unless(
            $novice->isNovice(),
            NotANoviceException::class,
            'Trying to subscribe a user that is not a novice to a course class'
        );

        $this->novices()->save($novice);
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
}
