<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discipline extends Model
{
    use HasFactory;

    public function getFormattedInstructorsAttribute()
    {
        if ($this->instructors->count() == 0) {
            return null;
        }

        return implode(' | ', $this->instructors->pluck('name')->toArray());
    }

    public function instructors()
    {
        return $this->hasMany(User::class);
    }
}
