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
        $this->refresh();

        throw_if(
            $this->hasLessons(),
            CourseClassAlreadyHasLessonsException::class,
            'Trying to create Lessons for a CourseClass that already have Lessons.'
        );

        $lessons = collect($lessons);

        $extistingLessons = $this->existingLessonsBetweenDuration($lessons);

        $lessons = $this->filterExistingLessons($lessons, $extistingLessons);

        $lessons = $lessons->map(function ($lesson) {
            return Lesson::fromArray($lesson);
        });

        $lessons = $lessons->concat($extistingLessons);

        $lessons = $lessons->reject(function ($lesson, $key) {
            return $this->refresh()->lessons->pluck('id')->contains($lesson->id);
        });

        $this->lessons()->attach($lessons->pluck('id')->toArray());

        return $lessons;
    }

    private function existingLessonsBetweenDuration($lessons)
    {
        $existingLessons = Lesson::with('courseClasses:city')
            ->whereBetween('date', [$this->begin, $this->end])
            ->where(function ($query) use ($lessons) {
                $lessons->each(function ($lesson) use ($query) {
                    $query->orWhere(function ($query) use ($lesson) {
                        $query->where('date', $lesson['date'])
                            ->where('type', $lesson['type'])
                            ->where('duration', $lesson['duration'])
                            ->where('instructor_id', $lesson['instructor_id'])
                            ->where('discipline_id', $lesson['discipline_id']);
                    });
                });
            })
            ->get();

        $existingLessons = $existingLessons->filter(function ($lesson) {
            return $lesson->courseClasses->first()->city === $this->city;
        });

        return $existingLessons;
    }

    private function filterExistingLessons($lessons, $existingLessons)
    {
        $duplicates = $existingLessons->map(function ($duplicate) {
            return $duplicate->date->format('Y-m-d') . '-' . $duplicate->type;
        });

        return $lessons->reject(function ($lesson) use ($duplicates) {
            return $duplicates->contains($lesson['date'] . '-' . $lesson['type']);
        });
    }

    public function subscribe(User $novice)
    {
        throw_unless(
            $novice->isNovice(),
            NotANoviceException::class,
            'Trying to subscribe a user that is not a novice to a course class'
        );

        $this->novices()->save($novice);

        $this->enrollNoviceToLessons($novice);
    }

    private function enrollNoviceToLessons(User $novice)
    {
        $lessons = $this->lessons;

        if ($lessons->count() === 0) {
            return;
        }

        $lessons->each->enroll($novice);
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
        $novices = $employer->company->allNovices()->pluck('id')->toArray();

        return $this->novices->whereIn('id', $novices)->count() > 0;
    }

    public function hasLessons()
    {
        return $this->lessons->isNotEmpty();
    }

    public function noviceFrequency(User $novice)
    {
        if (!$this->isSubscribed($novice)) {
            return;
        };

        $extraLessons = $this->extraLessonDays->map->format('Y-m-d')->all();

        $registeredLessonsDuration = (int) $this->lessons()
            ->registered()
            ->notExtra($extraLessons)
            ->totalDuration();

        if ($registeredLessonsDuration === 0) {
            return false;
        }

        $presenceDuration = (int) $novice->lessons()
            ->registered()
            ->wherePresent($novice->id)
            ->totalDuration();

        $frequency = ($presenceDuration * 100) / $registeredLessonsDuration;

        $frequency = sig_format_decimal_number($frequency);

        return number_format($frequency, 2, ',', '.');
    }

    public function instructors()
    {
        $instructors_ids = $this->lessons()->distinct()->pluck('instructor_id');

        return User::find($instructors_ids);
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

    public function extraLessonDays()
    {
        return $this->hasMany(ExtraLessonDay::class);
    }
}
