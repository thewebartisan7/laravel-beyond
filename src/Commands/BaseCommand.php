<?php

namespace Regnerisch\LaravelBeyond\Commands;

use Illuminate\Console\Command;
use Regnerisch\LaravelBeyond\Contracts\Composer as ComposerContract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    protected array $requiredPackages = [];

    public ?string $minimumVersion = null;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($code = $this->before()) {
            return $code;
        }

        return parent::execute($input, $output);
    }

    protected function before(): int
    {
        if ($code = $this->checkVersion()) {
            return $code;
        }

        if ($code = $this->checkDependencies()) {
            return $code;
        }

        return 0;
    }

    protected function checkDependencies(): int
    {
        if ([] === $missingPackages = $this->getMissingPackages()) {
            return 0;
        }

        $this->components->error(
            sprintf(
                'There are missing packages. Run composer require %s to install them.',
                implode(' ', $missingPackages)
            )
        );

        foreach ($missingPackages as $missingPackage) {
            $this->components->twoColumnDetail($missingPackage, 'MISSING');
        }

        return 1;
    }

    protected function checkVersion(): int
    {
        if (null === $this->minimumVersion) {
            return 0;
        }

        if (version_compare(PHP_VERSION, $this->minimumVersion, '<')) {
            $this->components->error(
                sprintf(
                    'Your version %s does not match the required version %s of this command.',
                    PHP_VERSION,
                    $this->minimumVersion
                )
            );

            return 1;
        }

        return 0;
    }

    protected function getMissingPackages(): array
    {
        $composer = $this->getLaravel()->get(ComposerContract::class);

        return array_diff($this->requiredPackages, $composer->getPackages()['require']);
    }
}
