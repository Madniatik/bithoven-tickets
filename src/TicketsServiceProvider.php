<?php

namespace Bithoven\Tickets;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Bithoven\Tickets\Console\Commands\CloseStaleTickets;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Policies\TicketPolicy;

class TicketsServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/tickets.php', 
            'tickets'
        );
        
        // Register services
        $this->app->singleton(Services\TicketService::class);
        $this->app->singleton(Services\NotificationService::class);
        $this->app->singleton(Services\AssignmentService::class);
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/tickets.php' => config_path('tickets.php'),
        ], 'bithoven-tickets-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'bithoven-tickets-migrations');

        // Load migrations (if running from vendor)
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tickets');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/tickets'),
        ], 'bithoven-tickets-views');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tickets');

        // Publish translations
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/tickets'),
        ], 'bithoven-tickets-lang');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CloseStaleTickets::class,
            ]);
        }

        // Register policies
        $this->registerPolicies();
        
        // Register permissions
        $this->registerPermissions();
    }

    /**
     * Register policies
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
    }

    /**
     * Register permissions with Spatie Laravel Permission
     */
    protected function registerPermissions(): void
    {
        // Only register if Spatie Permission is installed
        if (!class_exists(\Spatie\Permission\Models\Permission::class)) {
            return;
        }

        $permissions = [
            'view-tickets',
            'create-tickets',
            'edit-tickets',
            'delete-tickets',
            'assign-tickets',
            'manage-ticket-categories',
        ];

        foreach ($permissions as $permission) {
            try {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            } catch (\Exception $e) {
                // Silently fail if permissions table doesn't exist yet
                logger()->warning("Could not create permission: {$permission}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
