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

use Gears\CQRS\Async\AsyncCommandBus;
use Gears\CQRS\Async\CommandQueue;
use Gears\CQRS\Async\Discriminator\CommandDiscriminator;
use Gears\CQRS\Async\ReceivedCommand;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use Gears\CQRS\Command;
use Gears\CQRS\CommandBus;
use PHPUnit\Framework\TestCase;

class AsyncCommandBusTest extends TestCase
{
    public function testShouldEnqueue(): void
    {
        $busMock = $this->getMockBuilder(CommandBus::class)
            ->getMock();
        $busMock->expects(static::never())
            ->method('handle');
        /** @var CommandBus $busMock */
        $queueMock = $this->getMockBuilder(CommandQueue::class)
            ->getMock();
        $queueMock->expects(static::once())
            ->method('send');
        /** @var CommandQueue $queueMock */
        $discriminatorMock = new class() implements CommandDiscriminator {
            public function shouldEnqueue(Command $command): bool
            {
                return true;
            }
        };

        (new AsyncCommandBus($busMock, $queueMock, $discriminatorMock))->handle(CommandStub::instance([]));
    }

    public function testShouldNotEnqueue(): void
    {
        $busMock = $this->getMockBuilder(CommandBus::class)
            ->getMock();
        $busMock->expects(static::once())
            ->method('handle');
        /** @var CommandBus $busMock */
        $queueMock = $this->getMockBuilder(CommandQueue::class)
            ->getMock();
        $queueMock->expects(static::never())
            ->method('send');
        /** @var CommandQueue $queueMock */
        $discriminatorMock = new class() implements CommandDiscriminator {
            public function shouldEnqueue(Command $command): bool
            {
                return false;
            }
        };

        (new AsyncCommandBus($busMock, $queueMock, $discriminatorMock))->handle(CommandStub::instance([]));
    }

    public function testReceivedCommand(): void
    {
        $busMock = $this->getMockBuilder(CommandBus::class)
            ->getMock();
        $busMock->expects(static::once())
            ->method('handle');
        /** @var CommandBus $busMock */
        $queueMock = $this->getMockBuilder(CommandQueue::class)
            ->getMock();
        $queueMock->expects(static::never())
            ->method('send');
        /** @var CommandQueue $queueMock */
        $discriminatorMock = new class() implements CommandDiscriminator {
            public function shouldEnqueue(Command $command): bool
            {
                return true;
            }
        };

        $command = new ReceivedCommand(CommandStub::instance([]));

        (new AsyncCommandBus($busMock, $queueMock, $discriminatorMock))->handle($command);
    }
}
