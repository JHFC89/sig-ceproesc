<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotANoviceException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClass extends Model
{
    use HasFactory;

    protected $dates = ['begin', 'end', 'vacation_begin', 'vacation_end'];

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

    public function allTheoreticalDays()
    {
        $days = CarbonPeriod::since($this->begin)->days(1)->until($this->end);

        // filter the two week days of theoretical activity
        $days->filter(function ($date) {
            return $date->is($this->first_day)
                || $date->is($this->second_day);
        }, 'theoretical_days');

        $days = $this->excludeOffdays($days);

        $days = $this->excludeVacation($days);

        $days = $this->excludeHolidays($days);

        return collect($days)->keyBy->format('d-m-Y');
    }

    public function allPracticalDays($offdays = false)
    {
        $days = CarbonPeriod::since($this->begin)->days(1)->until($this->end);

        // exclude the two week days of theoretical activity
        $days->filter(function ($date) {
            return ! $date->is($this->first_day)
                && ! $date->is($this->second_day)
                && ! $date->isSunday();
        }, 'theoretical_days');

        $days = $this->excludeOffdays($days, $offdays);

        $days = $this->excludeVacation($days);

        $days = $this->excludeHolidays($days);

        return collect($days)->keyBy->format('d-m-Y');
    }

    private function excludeOffdays($days, $offdays = false)
    {
        $offdays = $offdays ? $offays : $this->offdays;

        $days->filter(function ($date) use ($offdays) {
            return ! $offdays->contains(function ($offday) use ($date) {
                return $date->format('d-m-Y') 
                    == $offday->format('d-m-Y');
            });
        }, 'offdays');

        return $days;
    }

    private function excludeVacation($days)
    {
        $days->filter(function ($date) {
            return ! $date->between($this->vacation_begin, $this->vacation_end);
        }, 'vacation');

        return $days;
    }

    private function excludeHolidays($days)
    {
        $days->filter(function ($date) {
            return ! Holiday::allForCity($this->city)
                ->contains(function ($holiday) use ($date) {
                    return $date->format('d-m-Y') 
                        == $holiday->format('d-m-Y');
            });
        }, 'holidays');

        return $days;
    }

    public function allOffdays()
    {
        return $this->offdays->keyBy->format('d-m-Y');
    }

    public function allVacationDays()
    {
        $days = CarbonPeriod::since($this->vacation_begin)
            ->days(1)
            ->until($this->vacation_end);

        return collect($days)->keyBy->format('d-m-Y');
    }

    public function totalTheoreticalDaysDuration()
    {
        $allDays = $this->allTheoreticalDays();

        $firstDays = $allDays->filter->is($this->first_day);
        $secondDays = $allDays->filter->is($this->second_day);

        $firstDaysDuration = $firstDays->count() * $this->first_duration;
        $secondDaysDuration = $secondDays->count() * $this->second_duration;

        return $firstDaysDuration + $secondDaysDuration;
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
