<?php

namespace Yuloh\JsonGuardCli;

use League\JsonGuard;
use Seld\JsonLint\JsonParser;
use Symfony\Component\Console\Helper\Table;
use League\JsonGuard\Loaders\CurlWebLoader;
use League\JsonGuard\Loaders\FileGetContentsWebLoader;
use League\JsonGuard\Loaders\FileLoader;

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
            if (is_array($error['value']) || is_object($error['value'])) {
                $error['value'] = json_encode($error['value']);
            } else {
                $error['value'] = JsonGuard\as_string($error['value']);
            }
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

    public static function schemaPath($file = '')
    {
        return realpath(__DIR__ . '/../schema/' . $file);
    }

    public static function load($path)
    {
        list($prefix, $path) = explode('://', $path, 2);

        switch ($prefix) {
            case 'http':
            case 'https':
                if (function_exists('curl_init')) {
                    $loader = new CurlWebLoader($prefix . '://');
                } else {
                    $loader = new FileGetContentsWebLoader($prefix . '://');
                }
                break;
            case 'file':
                $loader = new FileLoader();
                break;
            default:
                throw new \RuntimeException(sprintf('No loader registered for the prefix "%s"', $prefix));
        }

        return $loader->load($path);
    }
}
