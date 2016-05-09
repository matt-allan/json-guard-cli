<?php

namespace League\JsonGuardCli\Test;

use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\NullOutput;
use Yuloh\JsonGuardCli\Commands\Validate;
use Seld\JsonLint\ParsingException;

class ValidateCommandTest extends \PHPUnit_Framework_TestCase
{
    public function validateDataProvider()
    {
        return [
            [
                file_get_contents(__DIR__ . '/fixtures/valid-data.json'),
                file_get_contents(__DIR__ . '/fixtures/valid-schema.json'),
                true,
                'valid string data, valid string schema'
            ],
            [
                file_get_contents(__DIR__ . '/fixtures/invalid-data.json'),
                file_get_contents(__DIR__ . '/fixtures/valid-schema.json'),
                false,
                'invalid string data, valid string schema'
            ],
            [
                file_get_contents(__DIR__ . '/fixtures/valid-data.json'),
                __DIR__ . '/fixtures/valid-schema.json',
                true,
                'valid string data, valid path schema'
            ],
            [
                __DIR__ . '/fixtures/valid-data.json',
                __DIR__ . '/fixtures/valid-schema.json',
                true,
                'valid path data, valid path schema'
            ],
            [
                __DIR__ . '/fixtures/valid-data.json',
                'file://' . __DIR__ . '/fixtures/valid-schema.json',
                true,
                'valid path data, valid loader path schema'
            ],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testValidate($data, $schema, $isValid, $msg)
    {
        $stream = fopen('php://memory', 'w');
        $output = new StreamOutput($stream);

        $command = new Validate();
        $command->__invoke($data, $schema, $output);

        rewind($stream);
        $regex = $isValid ? '/✓ Validation passed/' : '/✗ Validation failed/';
        $this->assertRegexp($regex, stream_get_contents($stream), $msg);
    }

    public function testValidateWithLoaderPathForData()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $output = new NullOutput();
        $command = new Validate();
        $command->__invoke(
            'file://' . __DIR__ . '/fixtures/valid-data.json',
            __DIR__ . '/fixtures/valid-schema.json',
            $output
        );
    }

    public function testValidateWithInvalidJson()
    {
        $this->setExpectedException(ParsingException::class);
        $output = new NullOutput();
        $command = new Validate();
        $command->__invoke(
            __DIR__ . '/fixtures/invalid-json.json',
            __DIR__ . '/fixtures/valid-schema.json',
            $output
        );
    }
}
