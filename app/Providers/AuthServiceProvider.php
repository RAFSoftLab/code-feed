<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Auth\RequestGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        $this->registerPolicies();

        // define a admin user role */
        Gate::define('isAdmin', fn($user) => $user->isAdmin());

        Auth::viaRequest('admin', function (Request $request) {
            $user = Auth::user();
            return $user->isAdmin() ? $user : null;
        });

        Gate::define(
            'access-repository',
            fn(User $user, string $organization, string $repository)
                => $user->repositories()->where('organization', $organization)->where('name', $repository)->exists()
        );
    }
}
