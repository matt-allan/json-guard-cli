<?php

namespace Yuloh\JsonGuardCli\Commands;

use League\JsonGuard\Dereferencer;
use League\JsonGuard\Validator;
use Symfony\Component\Console\Output\OutputInterface;
use Yuloh\JsonGuardCli\Util;

class Check
{
    public function __invoke($schema, OutputInterface $output)
    {
        if (Util::isLoaderPath($schema)) {
            $schema = Util::load($schema);
        } else {
            $schema = Util::normalizeJsonArgument($schema);
        }

        $metaSchema = (new Dereferencer())
            ->dereference('file://' . Util::schemaPath('draft4.json'));
        $validator  = new Validator($schema, $metaSchema);

        if ($validator->passes()) {
            $output->writeln('<info>✓ Valid draft-04 JSON Schema</info>');
        } else {
            $output->writeln('<error>✗ Invalid draft-04 JSON Schema</error>');
            Util::renderErrorTable($output, $validator->errors());
        }
    }
}
