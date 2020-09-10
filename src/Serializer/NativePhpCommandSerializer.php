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

/**
 * @see https://github.com/symfony/messenger/blob/master/Transport/Serialization/PhpSerializer.php
 */
final class NativePhpCommandSerializer implements CommandSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Command $command): string
    {
        return \addslashes(\serialize($command));
    }

    /**
     * {@inheritdoc}
     */
    public function fromSerialized(string $serialized): Command
    {
        $serialized = \stripslashes($serialized);

        $unserializeException = new CommandSerializationException(
            \sprintf('Command deserialization failed: could not deserialize "%s"', $serialized)
        );
        $unserializeHandler = \ini_set('unserialize_callback_func', self::class . '::handleUnserializeCallback');
        $prevErrorHandler = \set_error_handler(
            function ($type, $msg, $file, $line, $context = []) use (&$prevErrorHandler, $unserializeException) {
                if (__FILE__ === $file) {
                    throw $unserializeException;
                }

                return $prevErrorHandler !== null ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
            }
        );

        try {
            $command = \unserialize($serialized);
        } finally {
            \restore_error_handler();

            if ($unserializeHandler !== false) {
                \ini_set('unserialize_callback_func', $unserializeHandler);
            }
        }

        if (!\is_object($command) || !$command instanceof Command) {
            throw new CommandSerializationException(\sprintf(
                'Command deserialization failed: not an instance of "%s", "%s" given',
                Command::class,
                \is_object($command) ? \get_class($command) : \gettype($command)
            ));
        }

        return $command;
    }

    /**
     * Called if an undefined class should be instantiated during unserializing.
     * To prevent getting an incomplete object "__PHP_Incomplete_Class".
     *
     * @param string $class
     */
    public static function handleUnserializeCallback(string $class): void
    {
        throw new CommandSerializationException(
            \sprintf('Command deserialization failed: command class "%s" cannot be found', $class)
        );
    }
}
