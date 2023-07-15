<?php declare(strict_types = 1);

use Composer\Composer;
use Composer\IO\IOInterface;
use Lettland\PackageRecipe\Executor;
use Lettland\PackageRecipe\State;
use Lettland\PackageRecipe\StateInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load(namespace: 'Lettland\\PackageRecipe\\', resource: __DIR__ . '/../src/*')
        ->exclude(excludes: [__DIR__ . '/../src/{Command,PackageRecipe.php}']);

    $services->set(Executor::class)->public();
    $services->set(StateInterface::class)->class(State::class);
    $services->set(Composer::class)->synthetic();
    $services->set(IOInterface::class)->synthetic();
    $services->set(Filesystem::class);
};
