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

use Gears\CQRS\Async\Discriminator\ParameterCommandDiscriminator;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use PHPUnit\Framework\TestCase;

/**
 * Parameter command discriminator test.
 */
class ParameterCommandDiscriminatorTest extends TestCase
{
    public function testDiscriminateParameter(): void
    {
        $discriminator = new ParameterCommandDiscriminator('identifier');

        static::assertTrue($discriminator->shouldEnqueue(CommandStub::instance(['identifier' => null])));
        static::assertFalse($discriminator->shouldEnqueue(CommandStub::instance([])));
    }

    public function testDiscriminateParameterValue(): void
    {
        $discriminator = new ParameterCommandDiscriminator('identifier', '1234');

        static::assertTrue($discriminator->shouldEnqueue(CommandStub::instance(['identifier' => '1234'])));
        static::assertFalse($discriminator->shouldEnqueue(CommandStub::instance(['identifier' => true])));
    }
}
