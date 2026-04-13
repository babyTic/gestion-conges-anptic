<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Direction;
use App\Models\TypeConge;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Partage des variables avec toutes les vues
        View::composer('*', function ($view) {
            $view->with('directions', Direction::all());
            $view->with('types', TypeConge::all());
            $view->with('roles', User::select('role')->distinct()->pluck('role'));
        });
    }
}
