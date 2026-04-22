<?php

namespace App\Providers;

use App\Models\School;
use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\User;
use App\Policies\SchoolPolicy;
use App\Policies\ProgramPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        School::class => SchoolPolicy::class,
        Program::class => ProgramPolicy::class,
        ProgramCategory::class => CategoryPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}