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

use Gears\CQRS\Async\Serializer\Exception\CommandSerializationException;
use Gears\CQRS\Async\Serializer\NativeCommandSerializer;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * PHP native command serializer test.
 */
class NativeCommandSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $command = CommandStub::instance(['identifier' => '1234']);

        $serialized = (new NativeCommandSerializer())->serialize($command);

        static::assertContains('a:1:{s:10:"identifier";s:4:"1234";}', $serialized);
    }

    public function testDeserialize(): void
    {
        $command = CommandStub::instance(['identifier' => '1234']);

        $deserialized = (new NativeCommandSerializer())->fromSerialized(\serialize($command));

        static::assertEquals($command, $deserialized);
    }

    public function testInvalidDeserialization(): void
    {
        $this->expectException(CommandSerializationException::class);
        $this->expectExceptionMessage('Invalid unserialized command');

        (new NativeCommandSerializer())->fromSerialized(\serialize(new \stdClass()));
    }
}
