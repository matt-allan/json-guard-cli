<?php

namespace Yuloh\JsonGuardCli\Commands;

use League\JsonGuard\Dereferencer;
use League\JsonGuard\Validator;
use Symfony\Component\Console\Output\OutputInterface;
use Yuloh\JsonGuardCli\Util;

class Validate
{
    public function __invoke($data, $schema, OutputInterface $output)
    {
        // If it's a loader path we don't normalize it.
        if (!Util::isLoaderPath($schema)) {
            $schema = Util::normalizeJsonArgument($schema);
        }

        if (Util::isLoaderPath($data)) {
            throw new \InvalidArgumentException(
                'Data can not be loaded from a loader path.'
            );
        }

        $schema    = (new Dereferencer())->dereference($schema);
        $validator = new Validator(Util::normalizeJsonArgument($data), $schema);

        if ($validator->passes()) {
            $output->writeln('<info>✓ Validation passed</info>');
        } else {
            $output->writeln('<error>✗ Validation failed</error>');
            Util::renderErrorTable($output, $validator->errors());
        }
    }
}
