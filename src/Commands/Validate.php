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
        $schema = Util::loadJson($schema);
        $data   = Util::loadJson($data);

        $schema    = (new Dereferencer())->dereference($schema);
        $validator = new Validator($data, $schema);

        if ($validator->passes()) {
            $output->writeln('<info>✓ Validation passed</info>');
            return 0;
        } else {
            $output->writeln('<error>✗ Validation failed</error>');
            Util::renderErrorTable($output, $validator->errors());
            return 1;
        }
    }
}
