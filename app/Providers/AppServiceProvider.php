<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // share airline balances with every view (if user logged in)
        \Illuminate\Support\Facades\View::composer('*', function($view) {
            if (auth()->check() && in_array(auth()->user()->role, ['Admin','Staff','Owner'])) {
                $balances = \App\Models\Airlines::orderBy('airlines_name')->get(['id','airlines_code','balance']);
                $view->with('airlineBalances', $balances);
            }
        });
    }
}
