<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\NoviceNotEnrolledException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    protected $noviceToRegisterPresence;

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    public function isRegistered()
    {
        return empty($this->registered_at) ? false : true;
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

    public function present()
    {
        return $this->novices()->updateExistingPivot($this->noviceToRegisterPresence->id, ['present' => true]);
    }

    public function absent()
    {
        return $this->novices()->updateExistingPivot($this->noviceToRegisterPresence->id, ['present' => false]);
    }

    public function isPresent($novice)
    {
        $present = $this->novices()->where('user_id', $novice->id)->first()->presence->present;

        return $present ? true : false;
    }

    public function isAbsent($novice)
    {
        $present = $this->novices()->where('user_id', $novice->id)->first()->presence->present;

        return $present ? false : true;
    }

    public function novicesFrequencyToJsonObject()
    {
        $novices = $this->novices->reduce(function ($novices, $novice) {
            $present = $novice->lessons->find($this)->presence->present;
            if($present === null) {
                $novices[$novice->id] = 1;
            } else {
                $novices[$novice->id] = $present;
            }
            return $novices;
        }, []);

        return json_encode($novices);
    }

    public function novices()
    {
        return $this->belongsToMany(User::class)
                    ->as('presence')
                    ->withPivot('frequency', 'present');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEnrolled($query, int $noviceId)
    {
        return $query->whereHas('novices', function (builder $query) use ($noviceId) {
            $query->where('user_id', $noviceId);
        });
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeWeek($query)
    {
        Lesson::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->get()->count();
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
