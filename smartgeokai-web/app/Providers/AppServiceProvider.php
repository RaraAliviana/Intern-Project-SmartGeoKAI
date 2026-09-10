<?php

namespace App\Providers;

use App\Models\StatusApproval;
use App\Observers\StatusApprovalObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Asset;
use App\Observers\AssetObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        StatusApproval::observe(StatusApprovalObserver::class);
        Paginator::useBootstrapFive();
        Asset::observe(AssetObserver::class);
    }
}