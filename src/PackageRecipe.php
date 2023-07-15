<?php declare(strict_types = 1);

namespace Lettland\PackageRecipe;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Lettland\PackageRecipe\Command\RecipeCommand;
use Lettland\PackageRecipe\Command\RecipeUninstallCommand;

final class PackageRecipe implements PluginInterface, Capable, EventSubscriberInterface, CommandProvider
{
    private array $newPackages = [];
    private ?ServiceContainer $serviceContainer = null;
    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public function getCapabilities(): array
    {
        return [
            CommandProvider::class => self::class,
        ];
    }

    public function getCommands(): array
    {
        return [new RecipeCommand(), new RecipeUninstallCommand()];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => ['addNewPackage'],
            PackageEvents::POST_PACKAGE_UPDATE => ['addNewPackage'],
            PackageEvents::PRE_PACKAGE_UNINSTALL => ['removePackage'],
            ScriptEvents::POST_INSTALL_CMD => ['postUpdate'],
            ScriptEvents::POST_UPDATE_CMD => ['postUpdate'],
        ];
    }

    public function addNewPackage(PackageEvent $event): void
    {
        $package = method_exists($event->getOperation(), 'getPackage')
            ? $event->getOperation()
                ->getPackage()
            : $event->getOperation()
                ->getInitialPackage();

        $this->newPackages[] = $package->getName();
    }

    public function removePackage(PackageEvent $event): void
    {
        $package = $event->getOperation()
            ->getPackage()
            ->getName();

        $this->uninstallRecipe($package);
    }

    public function postUpdate(): void
    {
        $this->executeRecipes();
        $this->displayPostInstallOutput();
    }

    private function getServiceContainer(): ServiceContainer
    {
        if (null === $this->serviceContainer) {
            $this->serviceContainer = new ServiceContainer($this->composer, $this->io);
        }

        return $this->serviceContainer;
    }

    private function executeRecipes(): void
    {
        $this->getServiceContainer()
            ->get(Executor::class)
            ?->runRecipes($this->newPackages);
    }

    private function uninstallRecipe(string $package): void
    {
        $this->getServiceContainer()
            ->get(Executor::class)
            ?->uninstallRecipes([$package]);
    }

    private function displayPostInstallOutput(): void
    {
        $this->getServiceContainer()
            ->get(Executor::class)
            ?->displayPostInstallOutput($this->newPackages);
    }
}
