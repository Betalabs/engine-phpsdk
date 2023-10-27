<?php

namespace Betalabs\Engine\Providers;

use Betalabs\Engine\Console\Commands\CrudMakeController;
use Illuminate\Support\ServiceProvider;

class CrudMakerProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CrudMakeController::class,
            ]);
        }
    }
}
