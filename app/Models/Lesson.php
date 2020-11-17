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

    public function registerPresence(User $novice, Int $frequency)
    {
        throw_unless(
            $this->isEnrolled($novice),
            NoviceNotEnrolledException::class,
            'Trying to register presence to a novice that is not enrolled to this lesson.'
        );

        if($this->novices->find($novice->id)->presence->frequency === $frequency) {
            return 1;
        }

        return $this->novices()->updateExistingPivot($novice->id, ['frequency' => $frequency]);
    }

    public function frequencyForNovice(User $novice)
    {
        return $this->novices()->where('user_id', $novice->id)->first()->presence->frequency;
    }

    public function novicesFrequencyToJsonObject()
    {
        $novices = $this->novices->reduce(function ($novices, $novice) {
            $frequency = $novice->lessons->find($this)->presence->frequency;
            if($frequency === null) {
                $novices[$novice->id] = 3;
            } else {
                $novices[$novice->id] = $frequency;
            }
            return $novices;
        }, []);

        return json_encode($novices);
    }

    public function novices()
    {
        return $this->belongsToMany(User::class)
                    ->as('presence')
                    ->withPivot('frequency');
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
}
