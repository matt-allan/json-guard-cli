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
            throw new \InvalidArgumentException(
                'Schema can not be loaded from a loader path when performing meta validation.'
            );
        }

        $metaSchema = (new Dereferencer())
            ->dereference('http://json-schema.org/draft-04/schema#');
        $validator  = new Validator(Util::normalizeJsonArgument($schema), $metaSchema);

        if ($validator->passes()) {
            $output->writeln('<info>✓ Valid draft-04 JSON Schema</info>');
        } else {
            $output->writeln('<error>✗ Invalid draft-04 JSON Schema</error>');
            Util::renderErrorTable($output, $validator->errors());
        }
    }
}
