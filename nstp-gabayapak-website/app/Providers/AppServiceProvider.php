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
        // Ensure PHP's default timezone matches the application timezone so
        // timestamps created by native PHP functions and by Eloquent use the
        // same timezone (Asia/Manila as configured in `config/app.php`).
        try {
            date_default_timezone_set(config('app.timezone'));
        } catch (\Throwable $e) {
            // Ignore if config isn't available in certain CLI contexts
        }
        // Register project policy mapping (in case AuthServiceProvider is not present)
        try {
            Gate::policy(Project::class, ProjectPolicy::class);
        } catch (\Throwable $e) {
            // Fail silently if Gate or classes are not available during certain CLI tasks
        }
    }
}
