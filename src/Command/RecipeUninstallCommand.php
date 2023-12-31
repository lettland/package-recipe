<?php declare(strict_types = 1);

namespace Lettland\PackageRecipe\Command;

use Composer\Command\BaseCommand;
use Lettland\PackageRecipe\Executor;
use Lettland\PackageRecipe\ServiceContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class RecipeUninstallCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('lettland:pr:uninstall')
            ->addArgument('package', InputArgument::OPTIONAL, 'Name of the package to uninstall recipe from')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Uninstall all');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = $input->getArgument('package');
        $all = $input->getOption('all');

        if (!$package && !$all) {
            $this->getIO()
                ->writeError('You must either specify a package name or --all to remove all recipes.');

            return Command::FAILURE;
        }

        $packagesToRemove = $package ? [$package] : $this->getRequiredPackages();

        (new ServiceContainer($this->requireComposer(), $this->getIO()))
            ->get(Executor::class)
            ?->uninstallRecipes($packagesToRemove);

        return Command::SUCCESS;
    }

    private function getRequiredPackages(): array
    {
        $rootPackage = $this->requireComposer()
            ->getPackage();

        return array_keys($rootPackage->getRequires() + $rootPackage->getDevRequires());
    }
}
