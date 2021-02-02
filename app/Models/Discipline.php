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

    public function attachInstructors(array $instructors)
    {
        $instructors = User::whereIn('id', $instructors)->get();
        $instructors->each(function ($instructor) {
            throw_unless(
                $instructor->isInstructor(),
                NotInstructorException::class,
                'Trying to attach a non instructor user to a discipline.'
            );
        });

        $this->instructors()->saveMany($instructors->all()); 
    }

    public function isBasic()
    {
        return $this->basic ? true : false;
    }

    public function instructors()
    {
        return $this->hasMany(User::class);
    }
}
