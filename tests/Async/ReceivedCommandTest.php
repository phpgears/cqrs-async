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
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

class ReceivedCommandTest extends TestCase
{
    public function testOriginalCommand(): void
    {
        $originalCommand = CommandStub::instance([]);

        $command = new ReceivedCommand($originalCommand);

        $this->assertSame($originalCommand, $command->getOriginalCommand());
    }

    /**
     * @expectedException \Gears\CQRS\Async\Exception\ReceivedCommandException
     * @expectedExceptionMessage Method Gears\CQRS\Async\ReceivedCommand::has should not be called
     */
    public function testHasException(): void
    {
        (new ReceivedCommand(CommandStub::instance([])))->has('');
    }

    /**
     * @expectedException \Gears\CQRS\Async\Exception\ReceivedCommandException
     * @expectedExceptionMessage Method Gears\CQRS\Async\ReceivedCommand::get should not be called
     */
    public function testGetException(): void
    {
        (new ReceivedCommand(CommandStub::instance([])))->get('');
    }

    /**
     * @expectedException \Gears\CQRS\Async\Exception\ReceivedCommandException
     * @expectedExceptionMessage Method Gears\CQRS\Async\ReceivedCommand::getPayload should not be called
     */
    public function testGetPayloadException(): void
    {
        (new ReceivedCommand(CommandStub::instance([])))->getPayload();
    }

    /**
     * @expectedException \Gears\CQRS\Async\Exception\ReceivedCommandException
     * @expectedExceptionMessage Method Gears\CQRS\Async\ReceivedCommand::reconstitute should not be called
     */
    public function testReconstituteException(): void
    {
        ReceivedCommand::reconstitute([]);
    }
}
