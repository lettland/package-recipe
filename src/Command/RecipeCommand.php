<?php declare(strict_types = 1);

namespace Lettland\PackageRecipe\Command;

use Composer\Command\BaseCommand;
use Lettland\PackageRecipe\Executor;
use Lettland\PackageRecipe\ServiceContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class RecipeCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this
            ->setName('lettland:pr:execute')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite existing files or values');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $overwrite = $input->getOption('overwrite');
        (new ServiceContainer($this->requireComposer(), $this->getIO()))
            ->get(Executor::class)
            ?->runRecipes(null, $overwrite);

        return Command::SUCCESS;
    }
}
