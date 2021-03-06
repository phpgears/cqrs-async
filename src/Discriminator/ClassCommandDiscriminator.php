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

namespace Gears\CQRS\Async\Discriminator;

use Gears\CQRS\Command;

final class ClassCommandDiscriminator implements CommandDiscriminator
{
    /**
     * @var string
     */
    private $class;

    /**
     * ClassCommandDiscriminator constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Command $command): bool
    {
        return \is_a($command, $this->class);
    }
}
