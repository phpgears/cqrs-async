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

namespace Gears\CQRS\Async;

use Gears\CQRS\Async\Exception\QueuedCommandException;
use Gears\CQRS\Command;
use Gears\DTO\Exception\InvalidParameterException;

final class QueuedCommand implements Command, \Serializable
{
    /**
     * @var Command
     */
    private $wrappedCommand;

    /**
     * ReceivedCommand constructor.
     *
     * @param Command $wrappedCommand
     */
    public function __construct(Command $wrappedCommand)
    {
        $this->wrappedCommand = $wrappedCommand;
    }

    /**
     * {@inheritdoc}
     *
     * @throws QueuedCommandException
     */
    public function getCommandType(): string
    {
        throw new QueuedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws QueuedCommandException
     *
     * @return mixed
     */
    public function get(string $parameter)
    {
        if ($parameter !== 'wrappedCommand') {
            throw new InvalidParameterException(\sprintf(
                'Payload parameter "%s" on "%s" does not exist',
                $parameter,
                static::class
            ));
        }

        return $this->wrappedCommand;
    }

    /**
     * Get wrapped command.
     *
     * @return Command
     */
    public function getWrappedCommand(): Command
    {
        return $this->wrappedCommand;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload(): array
    {
        return ['wrappedCommand' => $this->wrappedCommand];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return ['wrappedCommand' => $this->wrappedCommand];
    }

    /**
     * {@inheritdoc}
     */
    public static function reconstitute(iterable $payload)
    {
        $payload = \is_array($payload) ? $payload : \iterator_to_array($payload);

        $commandClass = static::class;

        return new $commandClass($payload['wrappedCommand']);
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return ['wrappedCommand' => \addslashes(\serialize($this->wrappedCommand))];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __unserialize(array $data): void
    {
        $this->wrappedCommand = \unserialize(\stripslashes($data['wrappedCommand']));
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return \addslashes(\serialize(['wrappedCommand' => $this->wrappedCommand]));
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $serialized
     */
    public function unserialize($serialized): void
    {
        $data = \unserialize(\stripslashes($serialized));

        $this->wrappedCommand = $data['wrappedCommand'];
    }
}
