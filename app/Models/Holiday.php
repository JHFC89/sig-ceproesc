<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    public function getLocalAttribute($value)
    {
        if (is_null($value)) {
            return 'nacional';
        }
        
        return $value;
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    static public function formatDateToCreate(array $data)
    {
        return Carbon::createFromDate(
            $data['year'], 
            $data['month'], 
            $data['day']
        );
    }

    public function format(string $format)
    {
        return $this->date->format($format);
    }

    static public function allHolidays()
    {
        return Self::all()->keyBy->format('d-m-Y');
    }
}
