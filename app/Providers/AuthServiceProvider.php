<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\User; // Import User model
use App\Models\Car;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define 'admin' gate
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        // Define 'manage-own-car' gate
        Gate::define('manage-own-car', function (User $user, \App\Models\Car $car) { // Make sure to import Car model if using directly
            return $user->id === $car->user_id;
        });

        // You can also define policies for more complex authorization rules.
    }
}