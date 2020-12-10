<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotANoviceException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClass extends Model
{
    use HasFactory;

    public function subscribe(User $novice)
    {
        throw_unless(
            $novice->isNovice(),
            NotANoviceException::class,
            'Trying to subscribe a user that is not a novice to a course class'
        );

        $this->novices()->save($novice);
    }

    public function novices()
    {
        return $this->hasMany(User::class);
    }
}
