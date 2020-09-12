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

use Gears\CQRS\Async\Exception\QueuedCommandException;
use Gears\CQRS\Async\QueuedCommand;
use Gears\CQRS\Async\Tests\Stub\CommandStub;
use Gears\DTO\Exception\InvalidParameterException;
use PHPUnit\Framework\TestCase;

class QueuedCommandTest extends TestCase
{
    public function testTypeException(): void
    {
        $this->expectException(QueuedCommandException::class);
        $this->expectExceptionMessage('Method Gears\CQRS\Async\QueuedCommand::getCommandType should not be called');

        (new QueuedCommand(CommandStub::instance([])))->getCommandType();
    }

    public function testGetException(): void
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessageRegExp('/^Payload parameter "anotherValue" on ".+" does not exist$/');

        (new QueuedCommand(CommandStub::instance([])))->get('anotherValue');
    }

    public function testGet(): void
    {
        $command = CommandStub::instance([]);

        static::assertSame($command, (new QueuedCommand($command))->get('wrappedCommand'));
    }

    public function testWrappedCommand(): void
    {
        $originalCommand = CommandStub::instance([]);

        $command = new QueuedCommand($originalCommand);

        static::assertSame($originalCommand, $command->getWrappedCommand());
    }

    public function testGetPayloadException(): void
    {
        $command = CommandStub::instance([]);

        static::assertSame(['wrappedCommand' => $command], (new QueuedCommand($command))->getPayload());
    }

    public function testToArrayException(): void
    {
        $command = CommandStub::instance([]);

        static::assertSame(['wrappedCommand' => $command], (new QueuedCommand($command))->toArray());
    }

    public function testReconstitute(): void
    {
        $stub = QueuedCommand::reconstitute(
            new \ArrayIterator(['wrappedCommand' => CommandStub::reconstitute(['parameter' => 'value'])])
        );

        static::assertEquals(['parameter' => 'value'], $stub->getWrappedCommand()->getPayload());
    }

    public function testSerialization(): void
    {
        $stub = new QueuedCommand(CommandStub::instance(['parameter' => 'value']));

        $serialized = \version_compare(\PHP_VERSION, '7.4.0') >= 0
            ? 'O:30:"Gears\CQRS\Async\QueuedCommand":1:{'
                . 's:14:"wrappedCommand";s:79:"O:39:"Gears\\CQRS\\Async\\Tests\\Stub\\CommandStub":1:{'
                . 's:9:"parameter";s:5:"value";'
                . '}";}'
            : 'C:30:"Gears\CQRS\Async\QueuedCommand":114:{a:1:{'
                . 's:14:"wrappedCommand";C:39:"Gears\\CQRS\\Async\\Tests\\Stub\\CommandStub":34:{a:1:{'
                . 's:9:"parameter";s:5:"value";'
                . '}}}}';

        static::assertSame($serialized, \serialize($stub));

        /** @var QueuedCommand $unserializedStub */
        $unserializedStub = \unserialize($serialized);
        static::assertEquals(
            \get_class($stub->getWrappedCommand()),
            \get_class($unserializedStub->getWrappedCommand())
        );
        static::assertSame(
            $stub->getWrappedCommand()->getPayload(),
            $unserializedStub->getWrappedCommand()->getPayload()
        );
    }
}
