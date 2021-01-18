<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Evaluation;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Evaluation $evaluation)
    {
        if ($user->isInstructor()) {
            return $evaluation->isForInstructor($user);
        }

        if ($user->isCoordinator()) {
            return true;
        }
        
        return !$user->hasNoRole();
    }

    public function viewNoviceEvaluation(User $user, User $novice)
    {
        if ($user->isEmployer()) {
            return $user->isEmployerOf($novice);
        }

        return $user->isInstructor() || $user->isCoordinator();
    }

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

    public function storeGrade(User $user, Evaluation $evaluation)
    {
        if ($user->isInstructor()) {
            return $evaluation->isForInstructor($user);
        }
    }
}
