<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function allNovices()
    {
        return $this->novices->map->user;
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function employers()
    {
        return $this->hasMany(Registration::class, 'company_id');
    }

    public function novices()
    {
        return $this->hasMany(Registration::class, 'employer_id');
    }
}
