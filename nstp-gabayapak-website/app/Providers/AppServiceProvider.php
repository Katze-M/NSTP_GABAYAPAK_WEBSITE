<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Database\NeonPostgresConnector;
use App\Database\NeonPostgresConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\PostgresConnection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('db.connector.pgsql', NeonPostgresConnector::class);

        // Only the pooled 'pgsql' connection runs with emulated prepares (see
        // NeonPostgresConnector); 'pgsql_direct' uses the stock PostgresConnection.
        Connection::resolverFor('pgsql', function ($pdo, $database, $prefix, $config) {
            $class = ($config['name'] ?? null) === 'pgsql'
                ? NeonPostgresConnection::class
                : PostgresConnection::class;

            return new $class($pdo, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    // Force HTTPS routing when running on your live production server
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
}
}
