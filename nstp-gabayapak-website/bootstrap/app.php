<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckRoles;
use App\Http\Middleware\StudentAccess;
use App\Http\Middleware\StaffAccess;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'roles' => CheckRoles::class,
            'student' => StudentAccess::class,
            'staff' => StaffAccess::class,
        ]);
        
        // Ensure unauthenticated users are redirected to login
        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e) {
            return redirect()->route('login');
        });
    })->create();