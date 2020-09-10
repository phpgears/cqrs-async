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
use Gears\CQRS\Async\Serializer\JsonCommandSerializer;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * JSON command serializer test.
 */
class JsonCommandSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $command = CommandStub::instance(['identifier' => '1234']);

        $serialized = '{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub",'
            . '"payload":{"identifier":"1234"}}';

        static::assertEquals($serialized, (new JsonCommandSerializer())->serialize($command));
    }

    public function testDeserialize(): void
    {
        $command = CommandStub::instance(['identifier' => '1234']);

        $serialized = '{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub",'
            . '"payload":{"identifier":"1234"}}';

        static::assertEquals(
            $command->getPayload(),
            (new JsonCommandSerializer())->fromSerialized($serialized)->getPayload()
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
                . '"payload":"1234"}');
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
}
