<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    const COORDINADOR = 'coordinator';
    const INSTRUCTOR = 'instructor';
    const NOVICE = 'novice';
    const EMPLOYER = 'employer';

    protected $guarded = [];

    public static function whereRole(string $role)
    {
        return self::where('name', $role)->first();
    }
}
