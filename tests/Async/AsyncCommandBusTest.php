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
use Gears\CQRS\Async\Discriminator\ClassCommandDiscriminator;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use Gears\CQRS\CommandBus;
use PHPUnit\Framework\TestCase;

class AsyncCommandBusTest extends TestCase
{
    public function testShouldEnqueue(): void
    {
        $busMock = $this->getMockBuilder(CommandBus::class)
            ->getMock();
        /** @var CommandBus $busMock */
        $queueMock = $this->getMockBuilder(CommandQueue::class)
            ->getMock();
        $queueMock->expects($this->once())
            ->method('send');
        /** @var CommandQueue $queueMock */
        $discriminatorMock = $this->getMockBuilder(ClassCommandDiscriminator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $discriminatorMock->expects($this->once())
            ->method('shouldEnqueue')
            ->will($this->returnValue(true));
        /* @var \Gears\CQRS\Async\Discriminator\CommandDiscriminator $discriminatorMock */

        (new AsyncCommandBus($busMock, $queueMock, $discriminatorMock))->handle(CommandStub::instance([]));
    }

    public function testShouldNotEnqueue(): void
    {
        $busMock = $this->getMockBuilder(CommandBus::class)
            ->getMock();
        $busMock->expects($this->once())
            ->method('handle');
        /** @var CommandBus $busMock */
        $queueMock = $this->getMockBuilder(CommandQueue::class)
            ->getMock();
        $queueMock->expects($this->never())
            ->method('send');
        /** @var CommandQueue $queueMock */
        $discriminatorMock = $this->getMockBuilder(ClassCommandDiscriminator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $discriminatorMock->expects($this->once())
            ->method('shouldEnqueue')
            ->will($this->returnValue(false));
        /* @var \Gears\CQRS\Async\Discriminator\CommandDiscriminator $discriminatorMock */

        (new AsyncCommandBus($busMock, $queueMock, $discriminatorMock))->handle(CommandStub::instance([]));
    }
}
