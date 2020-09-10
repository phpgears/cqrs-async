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

use Gears\CQRS\Async\Exception\ReceivedCommandException;
use Gears\CQRS\Command;

final class ReceivedCommand implements Command
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
     *
     * @throws ReceivedCommandException
     */
    public function getCommandType(): string
    {
        throw new ReceivedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedCommandException
     *
     * @return mixed
     */
    public function get(string $parameter)
    {
        throw new ReceivedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedCommandException
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        throw new ReceivedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedCommandException
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        throw new ReceivedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedCommandException
     */
    public static function reconstitute(iterable $parameters): void
    {
        throw new ReceivedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }
}
