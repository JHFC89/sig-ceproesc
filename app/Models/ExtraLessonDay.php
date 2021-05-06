<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraLessonDay extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    public function format(string $format)
    {
        return $this->date->format($format);
    }
}
