<?php

namespace App\Providers;

use App\View\Components\Buttons\Excel;
use App\View\Components\Buttons\Pdf;
use App\View\Components\Cards\Statistics;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        Blade::component( Statistics::class, 'card-statistics');
        Blade::component( Pdf::class,'button-pdf');
        Blade::component( Excel::class, 'button-excel');
        Relation::morphMap([
            'GPB' => \App\Models\Purchase::class,
            'SPV' => \App\Models\SupplierPaymentVoucher::class,
        ]);
    }
}
