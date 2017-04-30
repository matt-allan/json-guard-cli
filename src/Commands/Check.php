<?php

namespace Yuloh\JsonGuardCli\Commands;

use League\JsonReference\Dereferencer;
use League\JsonGuard\Validator;
use Symfony\Component\Console\Output\OutputInterface;
use Yuloh\JsonGuardCli\Util;

class Check
{
    public function __invoke($schema, OutputInterface $output)
    {
        $schema = Util::loadJson($schema);

        $metaSchema = Dereferencer::draft4()
            ->dereference('file://' . Util::schemaPath('draft4.json'));
        $validator  = new Validator($schema, $metaSchema);

        if ($validator->passes()) {
            $output->writeln('<info>✓ Valid draft-04 JSON Schema</info>');
            return 0;
        } else {
            $output->writeln('<error>✗ Invalid draft-04 JSON Schema</error>');
            Util::renderErrorTable($output, $validator->errors());
            return 1;
        }
    }
}
