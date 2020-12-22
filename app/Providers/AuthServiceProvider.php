<?php

namespace App\Providers;

use App\Models\Lesson;
use App\Policies\LessonPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\RegisterLessonRequest;
use App\Policies\RegisterLessonRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        RegisterLessonRequest::class => RegisterLessonRequestPolicy::class,
        Lesson::class => LessonPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
