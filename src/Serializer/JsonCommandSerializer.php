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

namespace Gears\CQRS\Async\Serializer;

use Gears\CQRS\Async\Serializer\Exception\CommandSerializationException;
use Gears\CQRS\Command;

final class JsonCommandSerializer implements CommandSerializer
{
    /**
     * JSON encoding options.
     * Preserve float values and encode &, ', ", < and > characters in the resulting JSON.
     */
    private const JSON_ENCODE_OPTIONS = \JSON_UNESCAPED_UNICODE
        | \JSON_PRESERVE_ZERO_FRACTION
        | \JSON_HEX_AMP
        | \JSON_HEX_APOS
        | \JSON_HEX_QUOT
        | \JSON_HEX_TAG;

    /**
     * JSON decoding options.
     * Decode large integers as string values.
     */
    private const JSON_DECODE_OPTIONS = \JSON_BIGINT_AS_STRING;

    /**
     * Get serialized from command.
     *
     * @param Command $command
     *
     * @return string
     */
    public function serialize(Command $command): string
    {
        $serialized = \json_encode(
            [
                'class' => \get_class($command),
                'payload' => $command->getPayload(),
            ],
            static::JSON_ENCODE_OPTIONS
        );

        // @codeCoverageIgnoreStart
        if ($serialized === false || \json_last_error() !== \JSON_ERROR_NONE) {
            throw new CommandSerializationException(\sprintf(
                'Error serializing command %s due to %s',
                \get_class($command),
                \lcfirst(\json_last_error_msg())
            ));
        }
        // @codeCoverageIgnoreEnd

        return $serialized;
    }

    /**
     * Get command from serialized.
     *
     * @param string $serialized
     *
     * @throws CommandSerializationException
     *
     * @return Command
     */
    public function fromSerialized(string $serialized): Command
    {
        ['class' => $commandClass, 'payload' => $payload] = $this->getCommandDefinition($serialized);

        if (!\class_exists($commandClass)) {
            throw new CommandSerializationException(\sprintf('Command class %s cannot be found', $commandClass));
        }

        if (!\in_array(Command::class, \class_implements($commandClass), true)) {
            throw new CommandSerializationException(\sprintf(
                'Command class must implement %s, %s given',
                Command::class,
                $commandClass
            ));
        }

        // @codeCoverageIgnoreStart
        try {
            /* @var Command $commandClass */
            return $commandClass::reconstitute($payload);
        } catch (\Exception $exception) {
            throw new CommandSerializationException('Error reconstituting command', 0, $exception);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get command definition from serialization.
     *
     * @param string $serialized
     *
     * @throws CommandSerializationException
     *
     * @return array<string, mixed>
     */
    private function getCommandDefinition(string $serialized): array
    {
        if (\trim($serialized) === '') {
            throw new CommandSerializationException('Malformed JSON serialized command: empty string');
        }

        $definition = \json_decode($serialized, true, 512, static::JSON_DECODE_OPTIONS);

        // @codeCoverageIgnoreStart
        if ($definition === null || \json_last_error() !== \JSON_ERROR_NONE) {
            throw new CommandSerializationException(\sprintf(
                'Command deserialization failed due to error %s: %s',
                \json_last_error(),
                \lcfirst(\json_last_error_msg())
            ));
        }
        // @codeCoverageIgnoreEnd

        if (!\is_array($definition)
            || !isset($definition['class'], $definition['payload'])
            || \count(\array_diff(\array_keys($definition), ['class', 'payload'])) !== 0
            || !\is_string($definition['class'])
            || !\is_array($definition['payload'])
        ) {
            throw new CommandSerializationException('Malformed JSON serialized command');
        }

        return $definition;
    }
}
