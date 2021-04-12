<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function employers()
    {
        return $this->hasMany(User::class);
    }

    public function novices()
    {
        return $this->hasMany(User::class, 'employer_id');
    }
}
