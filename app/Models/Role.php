<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    const ADMIN = 'admin';
    const COORDINATOR = 'coordinator';
    const INSTRUCTOR = 'instructor';
    const NOVICE = 'novice';
    const EMPLOYER = 'employer';

    protected $guarded = [];

    public static function whereRole(string $role)
    {
        return self::where('name', $role)->first();
    }

    public static function promoteToAdmin($registration, $user)
    {
        if (empty($user)) {
            $registration->role()
                         ->associate(Self::whereRole(Self::ADMIN))
                         ->save();
        } else {
            $user->roles()->attach(Self::whereRole(Self::ADMIN)->id);
        }
    }
}
