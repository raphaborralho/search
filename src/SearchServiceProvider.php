<?php

namespace Raphaborralho\Search;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/search.php' => config_path('search.php'),
        ]);
    }
    public function register()
    {
        $this->app->singleton(Search::class, function () {
            return new Search();
        });
    }
}
