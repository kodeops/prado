<?php
namespace kodeops\Prado;

use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

class PradoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package) : void
    {
        $package
            ->name('prado')
            ->hasMigration('create_prado_pins_table');
    }
}