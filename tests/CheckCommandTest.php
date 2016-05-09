<?php

namespace League\JsonGuardCli\Test;

use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\NullOutput;
use Yuloh\JsonGuardCli\Commands\Check;
use Seld\JsonLint\ParsingException;

class CheckCommandTest extends \PHPUnit_Framework_TestCase
{
    public function checkDataProvider()
    {
        return [
            [
                file_get_contents(__DIR__ . '/fixtures/valid-schema.json'),
                true,
                'valid string schema',
            ],
            [
                __DIR__ . '/fixtures/valid-schema.json',
                true,
                'valid path schema',
            ],
            [
                file_get_contents(__DIR__ . '/fixtures/invalid-schema.json'),
                false,
                'invalid string schema',
            ],
            [
                __DIR__ . '/fixtures/invalid-schema.json',
                false,
                'invalid path schema',
            ],
        ];
    }

    /**
     * @dataProvider checkDataProvider
     */
    public function testCheck($schema, $isValid, $msg)
    {
        $stream = fopen('php://memory', 'w');
        $output = new StreamOutput($stream);

        $command = new Check();
        $command->__invoke($schema, $output);

        rewind($stream);
        $regex = $isValid ? '/✓ Valid draft-04 JSON Schema/' : '/✗ Invalid draft-04 JSON Schema/';
        $this->assertRegexp($regex, stream_get_contents($stream), $msg);
    }

    public function testCheckWithLoaderPath()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $output = new NullOutput();
        $command = new Check();
        $command->__invoke(
            'file://' . __DIR__ . '/fixtures/valid-schema.json',
            $output
        );
    }

    public function testCheckWithInvalidJson()
    {
        $this->setExpectedException(ParsingException::class);
        $output = new NullOutput();
        $command = new Check();
        $command->__invoke(
            __DIR__ . '/fixtures/invalid-json.json',
            $output
        );
    }
}
