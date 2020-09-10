<?php

/*
 * cqrs-async (https://github.com/phpgears/cqrs-async).
 * Async decorator for CQRS command bus.
 *
 * @license MIT
 * @link https://github.com/phpgears/cqrs-async
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

declare(strict_types=1);

namespace Gears\CQRS\Async\Tests\Serializer;

use Gears\CQRS\Async\Serializer\Exception\CommandSerializationException;
use Gears\CQRS\Async\Serializer\NativePhpCommandSerializer;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * Native PHP command serializer test.
 */
class NativePhpCommandSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $command = CommandStub::instance(['identifier' => '1234']);

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:39:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub\":1:{'
                . 's:10:\"identifier\";s:4:\"1234\";'
                . '}'
            : 'C:39:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub\":35:{a:1:{'
                . 's:10:\"identifier\";s:4:\"1234\";'
                . '}}';

        static::assertEquals($serialized, (new NativePhpCommandSerializer())->serialize($command));
    }

    public function testDeserialize(): void
    {
        $command = CommandStub::instance(['identifier' => '1234']);

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:39:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub\":1:{'
                . 's:10:\"identifier\";s:4:\"1234\";'
                . '}'
            : 'C:39:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub\":35:{a:1:{'
                . 's:10:\"identifier\";s:4:\"1234\";'
                . '}}';

        static::assertEquals(
            $command->getPayload(),
            (new NativePhpCommandSerializer())->fromSerialized($serialized)->getPayload()
        );
    }

    public function testInvalidDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage('Command deserialization failed: could not deserialize "    "');

        (new NativePhpCommandSerializer())->fromSerialized('    ');
    }

    public function testMissingCommandDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessageRegExp('/^Command deserialization failed: command class ".+" cannot be found$/');

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:42:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\NotStub\\\\CommandStub\":0:{}'
            : 'C:42:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\NotStub\\\\CommandStub\":0:{}';

        (new NativePhpCommandSerializer())->fromSerialized($serialized);
    }

    public function testWrongTypeDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage(
            'Command deserialization failed: not an instance of "Gears\CQRS\Command", "stdClass" given'
        );

        (new NativePhpCommandSerializer())->fromSerialized('O:8:"stdClass":0:{}');
    }
}
