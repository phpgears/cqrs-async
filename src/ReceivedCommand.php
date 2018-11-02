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
    private $originalCommand;

    /**
     * ReceivedCommand constructor.
     *
     * @param Command $originalCommand
     */
    public function __construct(Command $originalCommand)
    {
        $this->originalCommand = $originalCommand;
    }

    /**
     * Get original command.
     *
     * @return Command
     */
    public function getOriginalCommand(): Command
    {
        return $this->originalCommand;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ReceivedCommandException
     */
    public function has(string $parameter): bool
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
     */
    public static function reconstitute(array $parameters): void
    {
        throw new ReceivedCommandException(\sprintf('Method %s should not be called ', __METHOD__));
    }
}
