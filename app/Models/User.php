<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UserTestData;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active'            => 'boolean',
    ];

    /**
    * The relationships that should always be loaded.
    *
    * @var array
    */
    protected $with = ['registration', 'roles'];

    public function getNameAttribute()
    {
        return $this->registration->name;
    }

    public function getCompanyAttribute()
    {
        return $this->registration->company;
    }

    public function getEmployerAttribute()
    {
        return $this->registration->employer;
    }

    public function getCodeAttribute()
    {
        return '2021' . $this->id;
    }

    public function getClassAttribute()
    {
        return $this->courseClass ? $this->courseClass->name : null;
    }

    public function getNovicesAttribute()
    {
        return $this->novices();
    }

    public function activate()
    {
        if ($this->active) {
            return false;
        }

        $this->active = true;

        $this->save();

        return true;
    }

    public function deactivate()
    {
        if (! $this->active) {
            return false;
        }

        $this->active = false;

        $this->save();

        return true;
    }

    public function isSubscribed()
    {
        return ! empty($this->courseClass);
    }

    public function subscribeToClass(CourseClass $courseClass)
    {
        if ($this->isSubscribed() || ! $this->isNovice()) {
            return;
        }

        $courseClass->subscribe($this);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class)
                    ->as('record')
                    ->withPivot('present', 'observation', 'grade');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function novices()
    {
        return $this->company->allNovices();
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function registration()
    {
        return $this->hasOne(Registration::class);
    }

    public function presentForLesson($lesson)
    {
        $present = $this->lessons()->where('lesson_id', $lesson->id)->first()->record->present;

        if ($present === null) {
            return null;
        }

        return $present ? true : false;
    }

    public function observationForLesson(Lesson $lesson)
    {
        return $this->lessons()->where('lesson_id', $lesson->id)->first()->record->observation;
    }
    
    public function isNovice()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == 'novice';
        });
    }

    public function isInstructor()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == 'instructor';
        });
    }

    public function isEmployer()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == 'employer';
        });
    }

    public function isCoordinator()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == 'coordinator';
        });
    }

    public function isAdmin()
    {
        return $this->roles->contains(function ($role) {
            return $role->name == 'admin';
        });
    }

    public function hasNoRole()
    {
        return $this->roles->isEmpty();
    }

    public function isEmployerOf(User $novice)
    {
        return $this->novices->pluck('id')->contains($novice->id);
    }

    public function turnIntoInstructor()
    {
        $this->roles()->attach(Role::firstOrCreate(['name' => 'instructor']));
    }

    public function turnIntoNovice()
    {
        $this->roles()->attach(Role::firstOrCreate(['name' => 'novice']));

        return $this;
    }

    public function scopeWhereInstructor($query)
    {
        return $query->whereHas('roles', function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->where('name', Role::INSTRUCTOR);
        });
    }

    public function scopeWhereNovice($query)
    {
        return $query->whereHas('roles', function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->where('name', Role::NOVICE);
        });
    }

    public function scopeWhereAvailableToSubscribe($query)
    {
        return $query->doesntHave('courseClass');
    }
}
