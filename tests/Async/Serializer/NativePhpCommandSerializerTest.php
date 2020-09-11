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

use Gears\CQRS\Async\QueuedCommand;
use Gears\CQRS\Async\Serializer\Exception\CommandSerializationException;
use Gears\CQRS\Async\Serializer\NativePhpCommandSerializer;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * Native PHP command serializer test.
 */
class NativePhpCommandSerializerTest extends TestCase
{
    /**
     * @dataProvider serializationProvider
     *
     * @param CommandStub $stub
     * @param string      $serialized
     */
    public function testSerialize(CommandStub $stub, string $serialized): void
    {
        static::assertEquals($serialized, (new NativePhpCommandSerializer())->serialize($stub));
    }

    /**
     * @dataProvider queuedSerializationProvider
     *
     * @param QueuedCommand $command
     * @param string        $serialized
     */
    public function testSerializeQueued(QueuedCommand $command, string $serialized): void
    {
        static::assertEquals($serialized, (new NativePhpCommandSerializer())->serialize($command));
    }

    /**
     * @dataProvider serializationProvider
     *
     * @param CommandStub $stub
     * @param string      $serialized
     */
    public function testDeserialize(CommandStub $stub, string $serialized): void
    {
        static::assertEquals(
            $stub->getPayload(),
            (new NativePhpCommandSerializer())->fromSerialized($serialized)->getPayload()
        );
    }

    /**
     * @dataProvider queuedSerializationProvider
     *
     * @param QueuedCommand $command
     * @param string        $serialized
     */
    public function testDeserializeQueued(QueuedCommand $command, string $serialized): void
    {
        static::assertEquals(
            $command->getWrappedCommand()->getPayload(),
            (new NativePhpCommandSerializer())->fromSerialized($serialized)->getWrappedCommand()->getPayload()
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

    /**
     * @return mixed[][]
     */
    public function serializationProvider(): array
    {
        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:39:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub\":1:{'
                . 's:9:\"parameter\";s:5:\"value\";'
                . '}'
            : 'C:39:\"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub\":34:{a:1:{'
                . 's:9:\"parameter\";s:5:\"value\";'
                . '}}';

        return [[CommandStub::instance(['parameter' => 'value']), $serialized]];
    }

    /**
     * @return mixed[][]
     */
    public function queuedSerializationProvider(): array
    {
        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:30:\"Gears\\\\CQRS\\\\Async\\\\QueuedCommand\":1:{'
                . 's:14:\"wrappedCommand\";s:90:\"'
                . 'O:39:\\\\\\"Gears\\\\\\\\CQRS\\\\\\\\Async\\\\\\\\Tests\\\\\\\\Stub\\\\\\\\CommandStub\\\\\\":1:{'
                . 's:9:\\\\\\"parameter\\\\\\";s:5:\\\\\\"value\\\\\\";'
                . '}\";}'
            : 'C:30:\"Gears\\\\CQRS\\\\Async\\\\QueuedCommand\":127:{a:1:{'
                . 's:14:\\\\\\"wrappedCommand\\\\\\";'
                . 'C:39:\\\\\\"Gears\\\\\\\\CQRS\\\\\\\\Async\\\\\\\\Tests\\\\\\\\Stub\\\\\\\\CommandStub\\\\\\":34:{'
                . 'a:1:{s:9:\\\\\\"parameter\\\\\\";s:5:\\\\\\"value\\\\\\";}'
                . '}}}';

        return [[new QueuedCommand(CommandStub::instance(['parameter' => 'value'])), $serialized]];
    }
}
