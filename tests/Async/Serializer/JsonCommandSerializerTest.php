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
use Gears\CQRS\Async\Serializer\JsonCommandSerializer;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * JSON command serializer test.
 */
class JsonCommandSerializerTest extends TestCase
{
    /**
     * @dataProvider serializationProvider
     *
     * @param CommandStub $command
     * @param string      $serialized
     */
    public function testSerialize(CommandStub $command, string $serialized): void
    {
        static::assertEquals($serialized, (new JsonCommandSerializer())->serialize($command));
    }

    /**
     * @dataProvider queuedSerializationProvider
     *
     * @param QueuedCommand $command
     * @param string        $serialized
     */
    public function testSerializeQueued(QueuedCommand $command, string $serialized): void
    {
        static::assertEquals($serialized, (new JsonCommandSerializer())->serialize($command));
    }

    /**
     * @dataProvider serializationProvider
     *
     * @param CommandStub $command
     * @param string      $serialized
     */
    public function testDeserialize(CommandStub $command, string $serialized): void
    {
        static::assertEquals(
            $command->getPayload(),
            (new JsonCommandSerializer())->fromSerialized($serialized)->getPayload()
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
            (new JsonCommandSerializer())->fromSerialized($serialized)->getWrappedCommand()->getPayload()
        );
    }

    public function testEmptyDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage('Malformed JSON serialized command: empty string');

        (new JsonCommandSerializer())->fromSerialized('    ');
    }

    public function testMissingPartsDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage('Malformed JSON serialized command');

        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub"}');
    }

    public function testWrongTypeDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage('Malformed JSON serialized command');

        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub",'
                . '"payload":"value"}');
    }

    public function testMissingClassDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage('Command class Gears\Unknown cannot be found');

        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\Unknown","payload":{}}');
    }

    public function testWrongClassTypeDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessageRegExp(
            '/^Command class must implement .+\\\Command, .+\\\JsonCommandSerializer given$/'
        );

        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\CQRS\\\\Async\\\\Serializer\\\\JsonCommandSerializer",'
                . '"payload":{}}');
    }

    /**
     * @return mixed[][]
     */
    public function serializationProvider(): array
    {
        $serialized = '{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub",'
            . '"payload":{"parameter":"value"}}';

        return [[CommandStub::instance(['parameter' => 'value']), $serialized]];
    }

    /**
     * @return mixed[][]
     */
    public function queuedSerializationProvider(): array
    {
        $serialized = '{"class":"Gears\\\\CQRS\\\\Async\\\\QueuedCommand","payload":{'
            . '"wrappedCommand":"{'
            . '\"class\":\"Gears\\\\\\\\CQRS\\\\\\\\Async\\\\\\\\Tests\\\\\\\\Stub\\\\\\\\CommandStub\",'
            . '\"payload\":{\"parameter\":\"value\"}'
            . '}"}}';

        return [[new QueuedCommand(CommandStub::instance(['parameter' => 'value'])), $serialized]];
    }
}
