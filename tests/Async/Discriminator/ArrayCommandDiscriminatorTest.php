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

use Gears\CQRS\Async\Discriminator\ArrayCommandDiscriminator;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * Array command discriminator test.
 */
class ArrayCommandDiscriminatorTest extends TestCase
{
    public function testDiscriminate(): void
    {
        $commandMock = $this->getMockBuilder(CommandStub::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->expects(static::any())
            ->method('getCommandType')
            ->will(static::returnValue(\get_class($commandMock)));
        /** @var \Gears\CQRS\Command $commandMock */
        $discriminator = new ArrayCommandDiscriminator([\get_class($commandMock)]);

        static::assertTrue($discriminator->shouldEnqueue($commandMock));
        static::assertFalse($discriminator->shouldEnqueue(CommandStub::instance()));
    }
}
