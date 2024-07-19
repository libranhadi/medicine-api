<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        $this->autoLoginUser();
    }

    private function autoLoginUser()
    {
        // for test make autologin
        if (Schema::hasTable('users')) {
            $user = User::find(1); 

            if ($user) {
                Auth::login($user);
            }
        }
    }
}
