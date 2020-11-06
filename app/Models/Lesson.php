<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    public function isRegistered()
    {
        return empty($this->registered_at) ? false : true;
    }

    public function isForToday()
    {
        return ($this->date->format('d/m/Y') == now()->format('d/m/Y')) ? true : false;
    }
}
