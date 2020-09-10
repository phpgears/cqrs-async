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

use Gears\CQRS\Async\Exception\ReceivedCommandException;
use Gears\CQRS\Async\ReceivedCommand;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

class ReceivedCommandTest extends TestCase
{
    public function testOriginalCommand(): void
    {
        $originalCommand = CommandStub::instance([]);

        $command = new ReceivedCommand($originalCommand);

        static::assertSame($originalCommand, $command->getWrappedCommand());
    }

    public function testTypeException(): void
    {
        $this->expectException(ReceivedCommandException::class);
        $this->expectExceptionMessage('Method Gears\CQRS\Async\ReceivedCommand::getCommandType should not be called');

        (new ReceivedCommand(CommandStub::instance([])))->getCommandType();
    }

    public function testGetException(): void
    {
        $this->expectException(ReceivedCommandException::class);
        $this->expectExceptionMessage('Method Gears\CQRS\Async\ReceivedCommand::get should not be called');

        (new ReceivedCommand(CommandStub::instance([])))->get('');
    }

    public function testGetPayloadException(): void
    {
        $this->expectException(ReceivedCommandException::class);
        $this->expectExceptionMessage('Method Gears\CQRS\Async\ReceivedCommand::getPayload should not be called');

        (new ReceivedCommand(CommandStub::instance([])))->getPayload();
    }

    public function testToArrayException(): void
    {
        $this->expectException(ReceivedCommandException::class);
        $this->expectExceptionMessage('Method Gears\CQRS\Async\ReceivedCommand::toArray should not be called');

        (new ReceivedCommand(CommandStub::instance([])))->toArray();
    }

    public function testReconstituteException(): void
    {
        $this->expectException(ReceivedCommandException::class);
        $this->expectExceptionMessage('Method Gears\CQRS\Async\ReceivedCommand::reconstitute should not be called');

        ReceivedCommand::reconstitute([]);
    }
}
