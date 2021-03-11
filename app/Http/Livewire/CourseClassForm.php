<?php

namespace App\Http\Livewire;

use App\Models\Course;
use App\Models\CourseClass;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CourseClassForm extends Component
{
    public $courseClass = null;

    public $class;

    public $courses;

    public $course = null;

    public $offdays;

    public $duration = null;

    public $basicDuration = null;

    public $specificDuration = null;

    public $practicalDuration = null;

    public $showSchedule = false;

    protected $listeners = ['toggledoffday' => 'toggleOffday'];

    protected $run = false;

    protected $rules = [
        'class.name'                => 'required',
        'course'                    => 'required',
        'class.first_day'           => 'required|different:class.second_day',
        'class.second_day'          => 'required|different:class.first_day',
        'class.first_day_duration'  => 'required|integer|between:1,6',
        'class.second_day_duration' => 'required|integer|between:1,6',
        'class.second_day_duration' => 'required|integer|between:1,6',
        'practicalDuration'         => 'required|integer|between:1,6',
    ];

    public function mount()
    {
        $this->courses = Course::all();

        $this->class = $this->setClassFields();

        $this->offdays = collect([]);
    }

    public function render()
    {
        return view('livewire.course-class-form');
    }

    public function updated($value, $name)
    {
        $this->generateCourseClass();
    }

    public function selectCourse($id)
    {
        $this->course = $this->courses->find($id);

        $this->duration = $this->course->duration;

        $this->basicDuration = $this->course->basicDisciplinesDuration();

        $this->specificDuration = $this->course->specificDisciplinesDuration();
    }

    public function generateSchedule()
    {
        $this->validateToGenerateSchedule();
        
        $this->generateCourseClass();

        $this->showSchedule();
    }

    private function showSchedule()
    {
        return $this->showSchedule = true;
    }

    private function generateCourseClass()
    {
        $courseClass = new CourseClass;
        $courseClass->name = $this->class['name'];
        $courseClass->begin = $this->date($this->class['begin']);
        $courseClass->end = $this->date($this->class['end']);
        $courseClass->first_theoretical_activity_day = $this->class['first_day'];
        $courseClass->second_theoretical_activity_day = $this->class['second_day'];
        $courseClass->vacation_begin = $this->date($this->class['vacation_begin']);
        $courseClass->vacation_end = $this->date($this->class['vacation_end']);
        $courseClass->first_theoretical_activity_duration = $this->class['first_day_duration'];
        $courseClass->second_theoretical_activity_duration = $this->class['second_day_duration'];

        $this->courseClass = $courseClass;
    }

    private function setClassFields()
    {
        $currentYear = now()->format('Y');

        return [
            'name'                  => null,
            'begin'                 => [
                'day'   => 1,
                'month' => 1,
                'year'  => $currentYear
            ],
            'end'                   => [
                'day'   => 1,
                'month' => 1,
                'year'  => $currentYear
            ],
            'first_day'             => 'monday',
            'first_day_duration'    => 4,
            'second_day'            => 'saturday',
            'second_day_duration'   => 5,
            'vacation_begin'        => [
                'day'   => 1,
                'month' => 1,
                'year'  => $currentYear
            ],
            'vacation_end'          => [
                'day'   => 1,
                'month' => 1,
                'year'  => $currentYear
            ],
        ];
    }

    private function date(array $date)
    {
        return Carbon::create($date['year'], $date['month'], $date['day']);
    }

    public function toggleOffday($date)
    {
        if (! $this->haveRun()) {
            return;
        }

        $this->generateSchedule();

        if ($this->offdays->contains($date)) {
            $this->removeOffday($date);
        } else {
            $this->addOffday($date);
        }

        $this->run = true;
    }

    public function offdayDates()
    {
        return $this->offdays->mapInto(Carbon::class);
    }

    private function haveRun()
    {
        return $this->run == false;
    }

    private function removeOffday($date)
    {
        $key = $this->offdays->search($date);
        $this->offdays->forget($key);
    }

    private function addOffday($date)
    {
        $formattedDate = Carbon::parse($date)->format('d-m-Y');

        if ($this->courseClass->allTheoreticalDays()->has($formattedDate)) {
            $this->offdays->push($date);
        }
    }

    public function totalTheoreticalDuration()
    {
        if (! $this->showSchedule) {
            return;
        }
        
        $this->generateCourseClass();

        $courseClass = $this->courseClass;

        $allDays = $courseClass->allTheoreticalDays();

        $allDays = $this->filterOffdays($allDays);

        $firstDays = $allDays->filter->is($courseClass->first_day);
        $secondDays = $allDays->filter->is($courseClass->second_day);

        $firstDaysDuration = $firstDays->count() * $courseClass->first_duration;
        $secondDaysDuration = $secondDays->count() * $courseClass->second_duration;

        return $firstDaysDuration + $secondDaysDuration;
    }

    public function calculateTotalPracticalDuration()
    {
        if (! isset($this->courseClass->begin)) {
            return;
        }
        
        if ($this->showSchedule) {
            return $this->courseClass->allPracticalDays()->count() * $this->practicalDuration;
        }
    }

    private function filterOffdays($days)
    {
        $this->offdays->each(function ($offday) use ($days) {
            $date = Carbon::parse($offday)->format('d-m-Y');
            $days->forget($date);
        });

        return $days;
    }

    public function theoreticalDurationDiff()
    {
        if ($this->showSchedule) {
            return $this->duration - $this->totalTheoreticalDuration();
        }
    }

    private function validateToGenerateSchedule()
    {
        $this->validate();

        $this->validateDates();
    }

    private function validateDates()
    {
        $begin = Carbon::create(
            $this->class['begin']['year'],
            $this->class['begin']['month'],
            $this->class['begin']['day']
        );

        $end = Carbon::create(
            $this->class['end']['year'],
            $this->class['end']['month'],
            $this->class['end']['day']
        );

        if ($begin->equalTo($end)) {
            throw ValidationException::withMessages([
                'duration' => 'begin is equal to end.'
            ]);
        }

        if ($begin->greaterThan($end)) {
            throw ValidationException::withMessages([
                'duration' => 'begin is greater than end.'
            ]);
        }

        $vacation_begin = Carbon::create(
            $this->class['vacation_begin']['year'],
            $this->class['vacation_begin']['month'],
            $this->class['vacation_begin']['day']
        );

        $vacation_end = Carbon::create(
            $this->class['vacation_end']['year'],
            $this->class['vacation_end']['month'],
            $this->class['vacation_end']['day']
        );

        if ($vacation_begin->equalTo($vacation_end)) {
            throw ValidationException::withMessages([
                'vacation_duration' => 'begin is equal to end.'
            ]);
        }

        if ($vacation_begin->greaterThan($vacation_end)) {
            throw ValidationException::withMessages([
                'vacation_duration' => 'begin is greater than end.'
            ]);
        }

        if ($vacation_begin->lessThan($begin)) {
            throw ValidationException::withMessages([
                'vacation_begin' => 'vacation must begin after class begin.'
            ]);
        }

        if ($vacation_end->greaterThan($end)) {
            throw ValidationException::withMessages([
                'vacation_duration' => 'vacation must end before class end.'
            ]);
        }
    }

    public function submit()
    {
        $this->validate();

        $this->validateTheoreticalDuration();

        $courseClass = $this->course->courseClasses()->save($this->courseClass);

        if ($this->offdays->count() > 0) {
            $offdays = $this->offdayDates()->map(function ($date) {
                return ['date' => $date];
            });

            $courseClass->offdays()->createMany($offdays);
        }

        return redirect()->route('classes.show', [
            'courseClass' => $courseClass,
        ]);
    }

    private function validateTheoreticalDuration()
    {
        if ($this->theoreticalDurationDiff() !== 0) {
            throw ValidationException::withMessages([
                'theoretical_duration' => 'value is not equal to course duration.'
            ]);
        }
    }
}
