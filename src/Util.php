<?php

namespace Yuloh\JsonGuardCli;

use League\JsonGuard;
use Seld\JsonLint\JsonParser;
use Symfony\Component\Console\Helper\Table;

class Util
{
    public static function printableErrors(array $errors)
    {
        return array_map(function ($error) {
            $error = $error->toArray();
            $error['constraints'] = implode(
                ',',
                array_map(function ($k, $v) {
                    return $k . ':' . JsonGuard\as_string($v);
                }, array_keys($error['constraints']), $error['constraints'])
            );
            $error['value'] = JsonGuard\as_string($error['value']);
            return array_values($error);
        }, $errors);
    }

    public static function normalizeJsonArgument($json)
    {
        if (file_exists($json)) {
            $json = file_get_contents($json);
        }

        if ($parseException = (new JsonParser())->lint($json)) {
            throw $parseException;
        }

        return JsonGuard\json_decode($json, false, 512, JSON_BIGINT_AS_STRING);
    }

    public static function renderErrorTable($output, $errors)
    {
        (new Table($output))
            ->setHeaders(['Code', 'Message', 'Pointer', 'Value', 'Constraints'])
            ->setRows(static::printableErrors($errors))
            ->render();
    }

    public static function isLoaderPath($path)
    {
        return preg_match('#^[^{\n\r]+\:\/\/[^}\n\r]*#', $path);
    }
}
