<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Exceptions\InvalidDisciplinesDurationException;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function addDisciplines($disciplines)
    {
        throw_if(
            Discipline::durationWhereIn($disciplines) != $this->duration,
            InvalidDisciplinesDurationException::class,
            'Disciplines duration does not match the course duration.'
        );

        $this->disciplines()->attach($disciplines);
    }

    public function basicDisciplines()
    {
        return $this->disciplines()->whereBasic()->get();
    }

    public function specificDisciplines()
    {
        return $this->disciplines()->whereSpecific()->get();
    }

    public function basicDisciplinesDuration()
    {
        return $this->basicDisciplines()->sum('duration');
    }

    public function specificDisciplinesDuration()
    {
        return $this->specificDisciplines()->sum('duration');
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class);
    }

    public function courseClasses()
    {
        return $this->hasMany(CourseClass::class);
    }
}
