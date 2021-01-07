<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\RequestAlreadyReleasedException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RectifyLessonRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['released_at'];

    static public function for(Lesson $lesson, string $justification)
    {
        if ($lesson->isRegistered()) {
            return $lesson->rectifications()->create([
                'justification' => $justification,
            ]);
        }
    }

    public function release()
    {
        throw_if($this->fresh()->isReleased(), RequestAlreadyReleasedException::class, 'Trying to release a request already released');

        $this->released_at = now();
        $this->save();
    }

    public function isReleased()
    {
        return $this->released_at ? true : false;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
