<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegisterLessonRequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, RegisterLessonRequest $request)
    {
        return ($user->isInstructor() && $request->isForInstructor($user)) 
            || $user->isCoordinator();
    }

    public function createForLesson(User $user, Lesson $lesson)
    {
        return $user->isInstructor() 
            && $lesson->isForInstructor($user)
            && $lesson->isExpired()
            && !$lesson->hasOpenRequest()
            && !$lesson->hasPendingRequest();
    }

    public function storeForLesson(User $user, Lesson $lesson)
    {
        return $user->isInstructor() 
            && $lesson->isForInstructor($user)
            && $lesson->isExpired()
            && !$lesson->hasOpenRequest()
            && !$lesson->hasPendingRequest();
    }

    public function update(User $user, RegisterLessonRequest $request)
    {
        return $user->isCoordinator() && !$request->isReleased();
    }
}

