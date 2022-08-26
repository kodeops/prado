<?php
namespace kodeops\Prado;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PradoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package) : void
    {
        $package
            ->name('prado')
            ->hasMigration('create_prado_pins_table');
    }
}