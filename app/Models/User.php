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

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class)
                    ->as('presence')
                    ->withPivot('present', 'observation');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function novices()
    {
        return $this->hasMany(User::class, 'employer_id');
    }

    public function presentForLesson($lesson)
    {
        $presence = $this->lessons()->where('lesson_id', $lesson->id)->first()->presence->present;

        if ($presence === null) {
            return null;
        }

        return $presence ? true : false;
    }

    public function observationForLesson(Lesson $lesson)
    {
        return $this->lessons()->where('lesson_id', $lesson->id)->first()->presence->observation;
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

    public function hasNoRole()
    {
        return $this->roles->isEmpty();
    }

    public function isEmployerOf(user $novice)
    {
        return $this->novices->contains($novice);
    }
}
