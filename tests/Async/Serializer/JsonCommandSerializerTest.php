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

namespace Gears\CQRS\Async\Tests;

use Gears\CQRS\Async\ReceivedCommand;
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

        $serialized = (new JsonCommandSerializer())->serialize($command);

        $this->assertContains('"payload":{"identifier":"1234"}', $serialized);
    }

    public function testDeserialize(): void
    {
        $command = new ReceivedCommand(CommandStub::instance(['identifier' => '1234']));
        $serialized = '{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub",'
            . '"payload":{"identifier":"1234"}}';

        $deserialized = (new JsonCommandSerializer())->fromSerialized($serialized);

        $this->assertEquals($command, $deserialized);
    }

    /**
     * @expectedException \Gears\CQRS\Async\Serializer\Exception\CommandSerializationException
     * @expectedExceptionMessage Malformed JSON serialized command: empty string
     */
    public function testEmptyDeserialization(): void
    {
        (new JsonCommandSerializer())->fromSerialized('    ');
    }

    /**
     * @expectedException \Gears\CQRS\Async\Serializer\Exception\CommandSerializationException
     * @expectedExceptionMessage Malformed JSON serialized command
     */
    public function testMissingPartsDeserialization(): void
    {
        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub"}');
    }

    /**
     * @expectedException \Gears\CQRS\Async\Serializer\Exception\CommandSerializationException
     * @expectedExceptionMessage Malformed JSON serialized command
     */
    public function testWrongTypeDeserialization(): void
    {
        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\CQRS\\\\Async\\\\Tests\\\\Stub\\\\CommandStub",'
                . '"payload":"1234"}');
    }

    /**
     * @expectedException \Gears\CQRS\Async\Serializer\Exception\CommandSerializationException
     * @expectedExceptionMessage Command class Gears\Unknown cannot be found
     */
    public function testMissingClassDeserialization(): void
    {
        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\Unknown","payload":{"identifier":"1234"}}');
    }

    /**
     * @expectedException \Gears\CQRS\Async\Serializer\Exception\CommandSerializationException
     * @expectedExceptionMessageRegExp /^Command class must implement .+\\Command, .+\\JsonCommandSerializer given$/
     */
    public function testWrongClassTypeDeserialization(): void
    {
        (new JsonCommandSerializer())
            ->fromSerialized('{"class":"Gears\\\\CQRS\\\\Async\\\\Serializer\\\\JsonCommandSerializer",'
                . '"payload":{"identifier":"1234"}}');
    }
}
