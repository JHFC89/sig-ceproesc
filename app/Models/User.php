<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    ];

    public function getCodeAttribute()
    {
        return '2021' . $this->id;
    }

    public function getClassAttribute()
    {
        return $this->courseClass ? $this->courseClass->name : null;
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
        return $this->hasMany(User::class, 'employer_id');
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
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

    public function hasNoRole()
    {
        return $this->roles->isEmpty();
    }

    public function isEmployerOf(user $novice)
    {
        return $this->novices->contains($novice);
    }

    public function turnIntoNovice()
    {
        $this->roles()->attach(Role::firstOrCreate(['name' => 'novice']));
    }
}
