<?php declare(strict_types = 1);

namespace Lettland\PackageRecipe;

use Composer\Composer;
use Composer\IO\IOInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class ServiceContainer
{
    private ContainerBuilder $container;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->initContainer($composer, $io);
    }

    public function get(string $id): ?object
    {
        return $this->container->get($id);
    }

    private function initContainer(Composer $composer, IOInterface $io): void
    {
        $container = new ContainerBuilder();
        $container->set(Composer::class, $composer);
        $container->set(IOInterface::class, $io);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.php');

        $container->compile();

        $this->container = $container;
    }
}
