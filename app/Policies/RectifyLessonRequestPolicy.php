<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;
use App\Models\RectifyLessonRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class RectifyLessonRequestPolicy
{
    public function createForLesson(User $user, Lesson $lesson)
    {
        return $user->isInstructor() 
            && $lesson->isForInstructor($user)
            && !$lesson->hasOpenRequest()
            && !$lesson->hasPendingRequest();
    }

    public function storeForLesson(User $user, Lesson $lesson)
    {
        return $user->isInstructor()
            && $lesson->isForInstructor($user)
            && $lesson->isRegistered()
            && !$lesson->hasOpenRequest()
            && !$lesson->hasPendingRequest();
    }
}
