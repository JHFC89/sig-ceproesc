<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

trait CourseClassSchedule
{
    public function allTheoreticalDays($withoutExtraLessons = true)
    {
        return $this->formattedDates($this->allTheoreticalPeriod($withoutExtraLessons));
    }

    private function allTheoreticalPeriod($withoutExtraLessons = true)
    {
        $days = $this->allDurationDays();

        $days->filter($this->theoreticalDaysFilter(), 'theoretical_days');

        $days->filter($this->offdaysFilter(), 'offdays');

        $days->filter($this->vacationFilter(), 'vacation');

        $days->filter($this->holidaysFilter(), 'holidays');

        if ($withoutExtraLessons) {
            $days->filter($this->extraLessonDaysFilter(), 'extra');
        }

        return $days;
    }

    private function theoreticalDaysFilter()
    {
        return function ($date) {
            return $date->is($this->first_day)
                || $date->is($this->second_day)
                || (
                    $date->between($this->intro_begin, $this->intro_end)
                    && ! $date->isSunday()
                );
        };
    }

    public function allPracticalDays($offdays = false)
    {
        return $this->formattedDates($this->allPracticalPeriod($offdays));
    }

    public function allPracticalPeriod($offdays = false)
    {
        $days = $this->allDurationDays();

        $days->filter($this->practicalDaysFilter(), 'practical_days');

        $days->filter($this->offdaysFilter($offdays), 'offdays');

        $days->filter($this->vacationFilter(), 'vacation');

        $days->filter($this->holidaysFilter(), 'holidays');

        return $days;
    }

    private function practicalDaysFilter()
    {
        return function ($date) {
            return ! $date->is($this->first_day)
                && ! $date->is($this->second_day)
                && ! $date->isSunday()
                && ! $date->between($this->intro_begin, $this->intro_end);
        };
    }

    private function allDurationDays()
    {
        return CarbonPeriod::since($this->begin)->days(1)->until($this->end);
    }

    private function offdaysFilter($offdays = false)
    {
        $offdays = $offdays ? $offdays : $this->offdays;

        return function ($date) use ($offdays) {
            return ! $offdays->contains(function ($offday) use ($date) {
                return $date->format('d-m-Y') 
                    == $offday->format('d-m-Y');
            });
        };
    }

    private function vacationFilter()
    {
        return function ($date) {
            return ! $date->between($this->vacation_begin, $this->vacation_end);
        };
    }

    private function holidaysFilter()
    {
        return function ($date) {
            return ! Holiday::allForCity($this->city)
                ->contains(function ($holiday) use ($date) {
                    return $date->format('d-m-Y') 
                        == $holiday->format('d-m-Y');
                });
        };
    }

    private function extraLessonDaysFilter()
    {
        $extraDays = $this->extraLessonDays;

        return function ($date) use ($extraDays) {
            return ! $extraDays->contains(function ($extraDay) use ($date) {
                return $date->format('d-m-Y') 
                    == $extraDay->format('d-m-Y');
            });
        };
    }

    public function allOffdays()
    {
        return $this->formattedDates($this->offdays);
    }

    public function allExtraLessonDays()
    {
        return $this->formattedDates($this->extraLessonDays);
    }

    public function allVacationDays()
    {
        $days = CarbonPeriod::since($this->vacation_begin)
            ->days(1)
            ->until($this->vacation_end);

        return $this->formattedDates($days);
    }

    public function totalTheoreticalDaysDuration()
    {
        $allDays = $this->allTheoreticalDays();

        $firstDays = $allDays->filter->is($this->first_day);
        $secondDays = $allDays->filter->is($this->second_day);
        $introDays = $allDays->diffKeys($firstDays)->diffKeys($secondDays);

        $firstDaysDuration = $firstDays->count() * $this->first_duration;
        $secondDaysDuration = $secondDays->count() * $this->second_duration;
        $introDaysDuration = $introDays->count() * $this->first_duration;

        // return minutes
        return $firstDaysDuration + $secondDaysDuration + $introDaysDuration;
    }

    public function totalPracticalDaysDuration($offdays = false)
    {
        // return minutes
        return $this->allPracticalDays($offdays)->count() * $this->practical_duration;
    }

    public function allMonths()
    {
        $months = CarbonPeriod::since($this->begin)
            ->months(1)
            ->until($this->end)
            ->map(function($date) {
                return [
                    'id'    => $date->format('m-Y'),
                    'month' => $date->format('m'),
                    'year'  => $date->format('Y'),
                ];
            });

        return collect($months);
    }

    public function theoreticalDaysForMonth(int $month, int $year, $withoutExtraLessons = true)
    {
        $month = Carbon::createFromDate($year, $month, 1);

        $days = $this->allTheoreticalPeriod($withoutExtraLessons);

        $days->filter(function($date) use ($month) {
            return $date->isSameMonth($month);
        });

        return $this->formattedDates($days);
    }

    private function formattedDates($dates)
    {
        return collect($dates)->keyBy->format('d-m-Y');
    }
}
