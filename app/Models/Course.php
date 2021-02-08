<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

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
}
