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

use Gears\CQRS\Async\Discriminator\ClassCommandDiscriminator;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * Class command discriminator test.
 */
class ClassCommandDiscriminatorTest extends TestCase
{
    public function testDiscriminate(): void
    {
        $commandMock = $this->getMockBuilder(CommandStub::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \Gears\CQRS\Command $commandMock */
        $discriminator = new ClassCommandDiscriminator(\get_class($commandMock));

        $this->assertTrue($discriminator->shouldEnqueue($commandMock));
        $this->assertFalse($discriminator->shouldEnqueue(CommandStub::instance()));
    }
}
