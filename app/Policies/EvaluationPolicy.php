<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    public function createForLesson(User $user, Lesson $lesson)
    {
        return $user->isInstructor() 
            && $lesson->isForInstructor($user)
            && !$lesson->isExpired()
            && !$lesson->isRegistered()
            && !$lesson->hasEvaluation();
    }

    public function storeForLesson(User $user, Lesson $lesson)
    {
        return $user->isInstructor() 
            && $lesson->isForInstructor($user)
            && !$lesson->isExpired()
            && !$lesson->isRegistered()
            && !$lesson->hasEvaluation();
    }
}
