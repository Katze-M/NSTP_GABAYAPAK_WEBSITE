<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Policies\ProjectPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register project policy mapping (in case AuthServiceProvider is not present)
        try {
            Gate::policy(Project::class, ProjectPolicy::class);
        } catch (\Throwable $e) {
            // Fail silently if Gate or classes are not available during certain CLI tasks
        }
    }
}
