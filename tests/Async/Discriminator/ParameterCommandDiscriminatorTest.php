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
        $discriminator = new ParameterCommandDiscriminator('parameter');
        static::assertTrue($discriminator->shouldEnqueue(CommandStub::instance(['parameter' => null])));

        $discriminator = new ParameterCommandDiscriminator('unknown');
        static::assertFalse($discriminator->shouldEnqueue(CommandStub::instance([])));
    }

    public function testDiscriminateParameterValue(): void
    {
        $discriminator = new ParameterCommandDiscriminator('parameter', 'value');

        static::assertTrue($discriminator->shouldEnqueue(CommandStub::instance(['parameter' => 'value'])));
        static::assertFalse($discriminator->shouldEnqueue(CommandStub::instance(['parameter' => true])));
    }
}
