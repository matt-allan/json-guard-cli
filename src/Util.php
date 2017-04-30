<?php

namespace Yuloh\JsonGuardCli;

use League\JsonGuard\ValidationError;
use League\JsonReference\Dereferencer;
use Seld\JsonLint\JsonParser;
use Symfony\Component\Console\Helper\Table;

class Util
{
    public static function printableErrors(array $errors)
    {
        return array_map(function (ValidationError $error) {
            $context = $error->getContext();
            return [
                ValidationError::MESSAGE     => $error->getMessage(),
                ValidationError::SCHEMA_PATH => $context[ValidationError::SCHEMA_PATH],
                ValidationError::DATA_PATH   => $context[ValidationError::DATA_PATH],
                ValidationError::CAUSE       => $context[ValidationError::CAUSE],
            ];
        }, $errors);
    }

    public static function loadJson($json)
    {
        // If it's a loader path just load it since we can't lint that.
        if (static::isLoaderPath($json)) {
            return static::loadPath($json);
        } elseif (file_exists($json)) {
            $json = file_get_contents($json);
        } elseif ($json === '-') {
            $json = static::getStdin();
        }

        if ($parseException = (new JsonParser())->lint($json)) {
            throw $parseException;
        }

        return json_decode($json, false, 512, JSON_BIGINT_AS_STRING);
    }

    public static function renderErrorTable($output, $errors)
    {
        (new Table($output))
            ->setHeaders([
                'Message',
                'Schema Path',
                'Data Path',
                'Cause',
            ])
            ->setRows(static::printableErrors($errors))
            ->render();
    }

    public static function isLoaderPath($path)
    {
        return preg_match('#^[^{\n\r]+\:\/\/[^}\n\r]*#', $path);
    }

    public static function schemaPath($file = '')
    {
        return realpath(__DIR__ . '/../schema/' . $file);
    }

    public static function loadPath($path)
    {
        list($prefix, $path) = explode('://', $path, 2);

        $loader = Dereferencer::draft4()
            ->getLoaderManager()
            ->getLoader($prefix);

        return $loader->load($path);
    }

    public static function getStdin()
    {
        $json = '';
        $fh   = fopen('php://stdin', 'r');
        while ($line = fgets($fh)) {
            $json .= $line;
        }
        return $json;
    }
}
