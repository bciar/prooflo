<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ProofloServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Contracts\IProjectService',
            'App\Services\ProjectService'
        );

        $this->app->bind(
            'App\Contracts\IFileService',
            'App\Services\FileService'
        );

        $this->app->bind(
            'App\Contracts\IProofService',
            'App\Services\ProofService'
        );

        $this->app->bind(
            'App\Contracts\IRevisionService',
            'App\Services\RevisionService'
        );
    }
}
