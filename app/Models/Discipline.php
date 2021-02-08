<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotInstructorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discipline extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getFormattedInstructorsAttribute()
    {
        if ($this->instructors->count() == 0) {
            return null;
        }

        return implode(' | ', $this->instructors->pluck('name')->toArray());
    }

    public function getTypeAttribute()
    {
        return $this->basic ? 'básico' : 'específico';
    }

    public function attachInstructors($instructors)
    {
        if (is_int($instructors)) {
            $instructor = User::find($instructors);
            $this->checkIsInstructor($instructor);

            $this->instructors()->sync($instructor->id); 

            return;
        }

        $instructors = User::whereIn('id', $instructors)->get();
        $instructors->each(function ($instructor) {
            $this->checkIsInstructor($instructor);
        });

        $this->instructors()->sync($instructors->pluck('id')->toArray()); 
    }

    private function checkIsInstructor(User $user)
    {
        throw_unless(
            $user->isInstructor(),
            NotInstructorException::class,
            'Trying to attach a non instructor user to a discipline.'
        );
    }

    public function isAttached(User $instructor)
    {
        return $this->instructors->contains($instructor);
    }

    public function isBasic()
    {
        return $this->basic ? true : false;
    }

    public function isSpecific()
    {
        return ! $this->isBasic();
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'discipline_instructor');
    }

    public function scopeWhereBasic($query)
    {
        return $query->where('basic', true);
    }

    public function scopeWhereSpecific($query)
    {
        return $query->where('basic', false);
    }
}
