<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthRepositoryInterface;
use  App\Repositories\AuthRepository;
use App\Interfaces\PaymentUploadRepositoryInterface;
use  App\Repositories\PaymentUploadRepository;

class RepositoryServicesProvider extends ServiceProvider
{
    /**
     * Summary: Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class,AuthRepository::class);
        $this->app->bind(PaymentUploadRepositoryInterface::class,PaymentUploadRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
