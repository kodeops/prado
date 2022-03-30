<?php
namespace kodeops\Prado;

use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;
use kodeops\Prado\Commands\Prefetch;

class PradoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('prado')
            ->hasCommand(Prefetch::class);
    }
}
