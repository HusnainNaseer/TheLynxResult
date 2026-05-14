<?php

namespace App\Providers;

use App\Models\Session;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrapFive();
        View::composer('*', function ($view) {
            $activeSession = null;

            if (Schema::hasTable('schoolsessions') && Schema::hasColumn('schoolsessions', 'is_active')) {
                $activeSession = Session::active()->first();
            }

            $view->with('activeSchoolSession', $activeSession);
        });
    }
}
