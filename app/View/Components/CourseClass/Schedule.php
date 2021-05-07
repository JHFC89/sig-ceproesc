<?php

namespace App\View\Components\CourseClass;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\View\Component;

class Schedule extends Component
{
    public $courseClass;

    public $theoreticalDays;

    public $vacation;

    public $offdays;

    public $holidays;

    public $extra;

    private $clickable;

    private $begin;

    private $end;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($group, $clickable = false, $offdays = false, $extra = false)
    {
        $this->courseClass = $group;

        $this->theoreticalDays = $group->allTheoreticalDays();
        
        $this->vacation = $group->allVacationDays();

        $this->clickable = $clickable;

        $this->offdays = $offdays 
            ? $this->allOffdays($offdays) 
            : $group->allOffdays();

        $this->extra = $extra
            ? $this->allExtraLessonDays($extra)
            : $group->allExtraLessonDays();

        $this->holidays = Holiday::allForCity($group->city)
             ->keyBy
             ->format('d-m-Y');

        $this->begin = $this->courseClass->begin;

        $this->end = $this->courseClass->end;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.course-class.schedule');
    }

    public function months()
    {
        $months = $this->courseClass->begin->startOfMonth()->range(
            $this->courseClass->end->startOfMonth(), 
            1, 
            'month'
        );

        $months->locale('pt_BR');

        $months = collect($months)->reduce(function ($months, $date) {
            $months[$date->format('m-Y')] = [
                    'name' => $date->monthName . ' ' . $date->format('Y'),
                    'month' => $date->format('m'),
                    'year'  => $date->format('Y'),
                ];
            return $months;
        }, []);

        return $months;
    }

    public function weeks($month)
    {
        $firstDay = Carbon::create($month['year'], $month['month'], 1);
        $lastDay = $firstDay->copy()->lastOfMonth();
        $month = $firstDay->range($lastDay, 1, 'days');

        $weeks = collect($month)->reduce(function ($weeks, $date) {
            if ($date->format('d') == '01' && ! $date->isSunday()) {
                $weeks[$weeks['counter']] = array_pad([], ($date->dayOfWeek), null);
            }

            $weeks[$weeks['counter']][] = $date; 
            if (count($weeks[$weeks['counter']]) === 7) {
                $weeks['counter']++;
            }

            return $weeks;
        }, ['counter' => 0]);

        unset($weeks['counter']);

        return $weeks;
    }

    public function dateType($date)
    {
        if (! $date) {
            return [
                'date'  => null,
                'style' => null,
                'type'  => null,
            ];
        }

        $dayType = $this->dayType($date);

        switch ($dayType) {
            case 'theoretical':
                $style = 'bg-green-500 text-white';
                $style = $this->clickable ? $style . ' cursor-pointer' : $style;
                break;
            case 'vacation':
                $style = 'bg-orange-500 text-white';
                break;
            case 'offday':
                $style = 'bg-yellow-300 text-white';
                $style = $this->clickable ? $style . ' cursor-pointer' : $style;
                break;
            case 'holiday':
                $style = 'bg-red-500 text-white';
                break;
            case 'sunday':
                $style = 'bg-white';
                break;
            case 'out':
                $style = 'bg-white';
                break;
            case 'extra':
                $style = 'bg-teal-500 text-white';
                $style = $this->clickable ? $style . ' cursor-pointer' : $style;
                break;
            case 'practical':
                $style = 'bg-blue-500 text-white';
                $style = $this->clickable ? $style . ' cursor-pointer' : $style;
                break;
        }

        return [
            'date'  => $date,
            'style' => $style,
            'type'  => $dayType,
        ];
    }

    private function dayType($date)
    {
        if ($this->offdays->has($date->format('d-m-Y'))) {
            $this->offdays->forget($date->format('d-m-Y'));
            return 'offday';
        }

        if ($this->extra->has($date->format('d-m-Y'))) {
            $this->extra->forget($date->format('d-m-Y'));
            return 'extra';
        }

        if ($this->theoreticalDays->has($date->format('d-m-Y'))) {
            $this->theoreticalDays->forget($date->format('d-m-Y'));
            return 'theoretical';
        }

        if ($this->vacation->has($date->format('d-m-Y'))) {
            $this->vacation->forget($date->format('d-m-Y'));
            return 'vacation';
        }

        if ($this->holidays->has($date->format('d-m-Y'))) {
            $this->holidays->forget($date->format('d-m-Y'));
            return 'holiday';
        }

        if ($date->isSunday()) {
            return 'sunday';
        }

        if (! $date->between($this->begin, $this->end)) {
            return 'out';
        }

        return 'practical';
    }

    private function allOffdays($offdays)
    {
        return $offdays->keyBy->format('d-m-Y');
    }

    private function allExtraLessonDays($extra)
    {
        return $extra->keyBy->format('d-m-Y');
    }

    public function dateFormat($date, $format)
    {
        return $date ? $date->format($format) : '';
    }

    public function isClickable($type)
    {
        if (! $this->clickable) {
            return false;
        }

        if (in_array($type, ['offday', 'theoretical', 'extra', 'practical'])) {
            return true;
        }

        return false;
    }
}
