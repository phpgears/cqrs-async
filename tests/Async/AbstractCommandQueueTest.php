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

use Gears\CQRS\Async\Serializer\CommandSerializer;
use Gears\CQRS\Async\Tests\Stub\AbstractCommandQueueStub;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

class AbstractCommandQueueTest extends TestCase
{
    public function testSerialization(): void
    {
        $serializer = $this->getMockBuilder(CommandSerializer::class)
            ->getMock();
        $serializer->expects($this->once())
            ->method('serialize');
        /* @var CommandSerializer $serializer */

        (new AbstractCommandQueueStub($serializer))->send(CommandStub::instance([]));
    }
}
