<?php

namespace App\Policies;

use App\Models\CourseClass;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use HandlesAuthorization;

    private $message;

    private $draft;

    public function __construct()
    {
        $this->draft = false;
    }

    public function viewAny(User $user, CourseClass $courseClass)
    {
        if ($user->isInstructor()) {
            return $courseClass->lessons()
                               ->where('instructor_id', $user->id)
                               ->count() > 0;
        }

        if ($user->isNovice()) {
            return $courseClass->isSubscribed($user);
        }

        if ($user->isEmployer()) {
            return $courseClass->hasNovicesFor($user);
        }

        return $user->isCoordinator();
    }

    public function view(User $user, Lesson $lesson)
    {
        if ($user->hasNoRole()) {
            return false;
        }

        if ($user->isInstructor() && $lesson->isForInstructor($user)) {
            return true;
        }

        if ($user->isNovice() && $lesson->isEnrolled($user)) {
            return true;
        }

        if ($user->isEmployer() && $lesson->hasNovicesForEmployer($user)) {
            return true;
        }

        if ($user->isCoordinator()) {
            return true;
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->isCoordinator();
    }

    public function viewLessonNovice(User $user, User $novice)
    {
        if ($user->isEmployer()) {
            return $user->isEmployerOf($novice);
        }
        
        return $user->isInstructor() || $user->isCoordinator();
    }

    public function createRegister(User $user, Lesson $lesson)
    {
        if ($this->isNotAnInstructor($user)) {
            return response::deny($this->message, 401);
        } 

        if ($this->lessonIsNotForInstructor($user, $lesson)) {
            return response::deny($this->message, 401);
        } 

        if ($this->lessonIsRegistered($lesson)) {
            return response::deny($this->message, 404);
        } 

        if ($this->lessonNotAvailable($lesson)) {
            return response::deny($this->message, 404);
        }

        return true;
    }

    public function storeDraft(User $user, Lesson $lesson)
    {
        $this->draft = true;
        
        return $this->storeRegisterOrDraft($user, $lesson);
    }

    public function storeRegister(User $user, Lesson $lesson)
    {
        return $this->storeRegisterOrDraft($user, $lesson);
    }

    public function update(User $user, Lesson $lesson)
    {
        return $user->isCoordinator() && ! $lesson->isRegistered();
    }

    private function storeRegisterOrDraft(User $user, Lesson $lesson)
    {
        if ($this->isNotAnInstructor($user)) {
            return response::deny($this->message, 401);
        } 

        if ($this->lessonIsNotForInstructor($user, $lesson)) {
            return response::deny($this->message, 401);
        } 

        if ($this->lessonIsRegistered($lesson)) {
            return response::deny($this->message, 422);
        } 

        if ($this->lessonNotAvailable($lesson)) {
            return response::deny($this->message, 422);
        }

        return true;
    }

    private function isNotAnInstructor(User $user)
    {
        $this->message = 'Action not authorized for this user';

        return ! $user->isInstructor();
    }

    private function lessonIsNotForInstructor(User $user, Lesson $lesson)
    {
        $this->message = 'Action not authorized for this instructor';

        return ! $lesson->isForInstructor($user);
    }

    private function lessonIsRegistered(Lesson $lesson)
    {
        $this->message = 'Lesson already registered';

        return $lesson->isRegistered() && !$lesson->hasPendingRequest();
    }

    private function lessonNotAvailable(Lesson $lesson)
    {
        $this->message = $this->draft 
            ? 'Lesson is not available to draft at this date' 
            : 'Lesson is not available to register at this date';

        return !$lesson->isForToday() && !$lesson->hasPendingRequest();
    }
}
