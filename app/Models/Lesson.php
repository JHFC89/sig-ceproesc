<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\LessonRegisteredException;
use App\Exceptions\NoviceNotEnrolledException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    protected $noviceToRegisterPresence;

    protected $noviceToRegister;

    protected $presenceToRegister;

    protected $observationToRegister;

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    public function getFormattedCourseClassesAttribute()
    {
        $relatedCourseClasses = $this->relatedCourseClasses();

        if ($relatedCourseClasses === null) {
            return null;
        }

        return implode(' | ', $relatedCourseClasses);
    }

    public function isRegistered()
    {
        return !empty($this->registered_at);
    }

    public function isExpired()
    {
        if ($this->date->greaterThan(now())) {
            return false;
        }

        return ($this->date->diffInSeconds(now()) > ((60 * 60) * 24) && ! $this->isRegistered());
    }

    public function isForToday()
    {
        return ($this->date->format('d/m/Y') == now()->format('d/m/Y')) ? true : false;
    }

    public function isForInstructor(User $instructor)
    {
        return $this->instructor->id === $instructor->id;
    }

    public function enroll(User $novice)
    {
        throw_if(
            $this->isRegistered(),
            LessonRegisteredException::class,
            'Trying to enroll a novice to a lesson already registered.'
        );

        $this->novices()->attach($novice->id);
    }

    public function isEnrolled(User $novice)
    {
        return $this->enrolled($novice->id)->count() > 0 ? true : false;
    }

    public function registerPresence(User $novice)
    {
        throw_unless(
            $this->isEnrolled($novice),
            NoviceNotEnrolledException::class,
            'Trying to register presence to a novice that is not enrolled to this lesson.'
        );

        $this->noviceToRegisterPresence = $novice;

        return $this;
    }

    public function registerFor(User $novice)
    {
        throw_unless(
            $this->isEnrolled($novice),
            NoviceNotEnrolledException::class,
            'Trying to register presence to a novice that is not enrolled to this lesson.'
        );

        throw_if(
            ($this->isRegistered() && !$this->hasPendingRequest()),
            LessonRegisteredException::class,
            'Trying to register a lesson that is already registered.'
        );

        $this->noviceToRegister= $novice;

        return $this;
    }

    public function present()
    {
        $this->presenceToRegister = true;

        return $this;
    }

    public function absent()
    {
        $this->presenceToRegister = false;
        return $this;
    }

    public function isPresent($novice)
    {
        $present = $this->novices()->where('user_id', $novice->id)->first()->record->present;

        return $present ? true : false;
    }

    public function isAbsent($novice)
    {
        $present = $this->novices()->where('user_id', $novice->id)->first()->record->present;

        return $present ? false : true;
    }

    public function novicesPresenceToJson()
    {
        $novices = $this->novices->reduce(function ($novices, $novice) {
            $record = $novice->lessons->find($this)->record;
            if($record->present === null) {
                $novices[$novice->id] = [
                    'presence'      => 1,
                    'observation'   => $record->observation,
                ];
            } else {
                $novices[$novice->id] = [
                    'presence'      => $record->present,
                    'observation'   => $record->observation,
                ];
            }
            return $novices;
        }, []);

        return json_encode($novices);
    }

    public function observation(string $observation)
    {
        $this->observationToRegister = $observation;

        return $this;
    }

    public function complete()
    {
        $this->novices()
                ->updateExistingPivot($this->noviceToRegister->id, [
                    'present'       => $this->presenceToRegister,
                    'observation'   => $this->observationToRegister,
                ]);

        $this->noviceToRegister = null;
        $this->presenceToRegister = null;
        $this->observationToRegister = null;

        return;
    }

    public function observationFor(User $novice)
    {
        return $this->novices()->where('user_id', $novice->id)->first()->record->observation;
    }

    public function register()
    {
        if ($this->hasPendingRequest()) {
            $this->registered_at = $this->registered_at ?: now();
            $this->save();
            $this->pendingRequest()->solve($this);
        } else {
            $this->registered_at = now();
            $this->save();
        }
    }

    public function hasNovicesForEmployer(user $employer)
    {
        return $this->novices()->whereIn('user_id', $employer->novices->pluck('id')->toArray())->count() ? true : false;
    }

    public function relatedCourseClasses()
    {
        $relatedCourseClasses = $this->novices->pluck('class')->unique();

        return $relatedCourseClasses->contains(null) ? null : $relatedCourseClasses->values()->all();
    }

    public function hasOpenRequest()
    {
        return $this->requests()->whereNull('released_at')->count() > 0;
    }

    public function openRequest()
    {
        return $this->requests()->whereNull('released_at')->first();
    }

    public function hasPendingRequest()
    {
        return $this->requests()->whereNotNull('released_at')->whereNull('solved_at')->count() > 0;
    }

    public function pendingRequest()
    {
        return $this->requests()->whereNotNull('released_at')->whereNull('solved_at')->first();
    }

    public function hasSolvedRequest()
    {
        return $this->requests()->whereNotNull('released_at')->whereNotNull('solved_at')->count() > 0;
    }

    public function hasEvaluation()
    {
        return $this->evaluation()->count() > 0;
    }

    public function novices()
    {
        return $this->belongsToMany(User::class)
                    ->as('record')
                    ->withPivot('present', 'observation', 'grade');
    }

    public function requests()
    {
        return $this->hasMany(LessonRequest::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class);
    }

    public function scopeEnrolled($query, int $noviceId)
    {
        return $query->whereHas('novices', function (builder $query) use ($noviceId) {
            $query->where('user_id', $noviceId);
        });
    }

    public function scopeEnrolledNovices($query, array $novicesIds)
    {
        return $query->whereHas('novices', function (builder $query) use ($novicesIds) {
            $query->whereIn('user_id', $novicesIds);
        });
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeForInstructor($query, User $instructor)
    {
        return $query->where('instructor_id', $instructor->id);
    }

    public function scopeForEmployer($query, User $employer)
    {
        return $query->enrolledNovices($employer->novices->pluck('id')->toArray());
    }

    public function scopeForNovice($query, User $novice)
    {
        return $query->enrolled($novice->id);
    }

    public function setTestData()
    {
        $courseClass = CourseClass::factory()->create();
        $this->novices->each(function ($novice) use ($courseClass) {
            $novice->turnIntoNovice();
            $courseClass->subscribe($novice);
        });
    }
}
