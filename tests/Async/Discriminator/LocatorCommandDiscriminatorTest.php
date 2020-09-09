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

namespace Gears\CQRS\Async\Tests\Discriminator;

use Gears\CQRS\Async\Discriminator\LocatorCommandDiscriminator;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * Array locator command discriminator test.
 */
class LocatorCommandDiscriminatorTest extends TestCase
{
    public function testDiscriminate(): void
    {
        $commandMock = $this->getMockBuilder(CommandStub::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandMock->expects(static::any())
            ->method('getCommandType')
            ->willReturn(\get_class($commandMock));
        /** @var \Gears\CQRS\Command $commandMock */
        $discriminator = new LocatorCommandDiscriminator([\get_class($commandMock)]);

        static::assertTrue($discriminator->shouldEnqueue($commandMock));
        static::assertFalse($discriminator->shouldEnqueue(CommandStub::instance()));
    }
}
