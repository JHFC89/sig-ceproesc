<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonRequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, LessonRequest $request)
    {
        return ($user->isInstructor() && $request->isForInstructor($user)) 
            || $user->isCoordinator();
    }

    public function createForLesson(User $user, Lesson $lesson)
    {
        if ($lesson->isRegistered()) {
            return $this->checkUserConditions($user, $lesson)
                && $this->checkRequestConditions($lesson);
        }

        return $this->checkUserConditions($user, $lesson)
            && $this->checkRequestConditions($lesson)
            && $lesson->isExpired();
    }

    public function storeForLesson(User $user, Lesson $lesson)
    {
        if ($lesson->isRegistered()) {
            return $this->checkUserConditions($user, $lesson)
                && $this->checkRequestConditions($lesson)
                && $lesson->isRegistered();
        }

        return $this->checkUserConditions($user, $lesson)
            && $this->checkRequestConditions($lesson)
            && $lesson->isExpired();
    }

    public function update(User $user, LessonRequest $request)
    {
        return $user->isCoordinator() && !$request->isReleased();
    }

    private function checkUserConditions(User $user, Lesson $lesson)
    {
        return $user->isInstructor() 
            && $lesson->isForInstructor($user);
    }

    private function checkRequestConditions(Lesson $lesson)
    {
        return !$lesson->hasOpenRequest()
            && !$lesson->hasPendingRequest();
    }
}
