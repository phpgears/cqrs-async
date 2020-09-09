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
use Gears\DTO\Exception\InvalidParameterException;

final class ParameterCommandDiscriminator implements CommandDiscriminator
{
    /**
     * Command parameter name.
     *
     * @var string
     */
    private $parameter;

    /**
     * Expected command parameter value.
     *
     * @var mixed|null
     */
    private $value;

    /**
     * ParameterCommandDiscriminator constructor.
     *
     * @param string     $parameter
     * @param mixed|null $value
     */
    public function __construct(string $parameter, $value = null)
    {
        $this->parameter = $parameter;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Command $command): bool
    {
        try {
            $parameterValue = $command->get($this->parameter);
        } catch (InvalidParameterException $exception) {
            return false;
        }

        return $this->value === null || $parameterValue === $this->value;
    }
}
