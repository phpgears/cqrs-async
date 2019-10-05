[![PHP version](https://img.shields.io/badge/PHP-%3E%3D7.1-8892BF.svg?style=flat-square)](http://php.net)
[![Latest Version](https://img.shields.io/packagist/v/phpgears/cqrs-async.svg?style=flat-square)](https://packagist.org/packages/phpgears/cqrs-async)
[![License](https://img.shields.io/github/license/phpgears/cqrs-async.svg?style=flat-square)](https://github.com/phpgears/cqrs-async/blob/master/LICENSE)

[![Build Status](https://img.shields.io/travis/phpgears/cqrs-async.svg?style=flat-square)](https://travis-ci.org/phpgears/cqrs-async)
[![Style Check](https://styleci.io/repos/150497403/shield)](https://styleci.io/repos/150497403)
[![Code Quality](https://img.shields.io/scrutinizer/g/phpgears/cqrs-async.svg?style=flat-square)](https://scrutinizer-ci.com/g/phpgears/cqrs-async)
[![Code Coverage](https://img.shields.io/coveralls/phpgears/cqrs-async.svg?style=flat-square)](https://coveralls.io/github/phpgears/cqrs-async)

[![Total Downloads](https://img.shields.io/packagist/dt/phpgears/cqrs-async.svg?style=flat-square)](https://packagist.org/packages/phpgears/cqrs-async/stats)
[![Monthly Downloads](https://img.shields.io/packagist/dm/phpgears/cqrs-async.svg?style=flat-square)](https://packagist.org/packages/phpgears/cqrs-async/stats)

# Async CQRS

Async decorator for CQRS command bus

## Installation

### Composer

```
composer require phpgears/cqrs-async
```

## Usage

Require composer autoload file

```php
require './vendor/autoload.php';
```

### Asynchronous Commands Bus

Command bus decorator to handle commands asynchronously

#### Enqueue

```php
use Gears\CQRS\Async\AsyncCommandBus;
use Gears\CQRS\Async\Serializer\JsonCommandSerializer;
use Gears\CQRS\Async\Discriminator\ParameterCommandDiscriminator;

/* @var \Gears\CQRS\CommandBus $commandBus */

/* @var Gears\CQRS\Async\CommandQueue $commandQueue */
$commandQueue = new CustomCommandQueue(new JsonCommandSerializer());

$asyncCommandBus new AsyncCommandBus(
    $commandBus,
    $commandQueue,
    new ParameterCommandDiscriminator('async')
);

$asyncCommand = new CustomCommand(['async' => true]);

$asyncCommandBus->handle($asyncCommand);
```

#### Dequeue

This part is highly dependent on your message queue, though command serializers can be used to deserialize queue message

This is just an example of the process

```php
use Gears\CQRS\Async\ReceivedCommand;
use Gears\CQRS\Async\Serializer\JsonCommandSerializer;

/* @var \Gears\CQRS\Async\AsyncCommandBus $asyncCommandBus */
/* @var your_message_queue_manager $queue */

$serializer = new JsonCommandSerializer();

while (true) {
  $message = $queue->getMessage();

  if ($message !== null) {
    $command = new ReceivedCommand($serializer->fromSerialized($message));

    $asyncCommandBus->handle($command);
  }
}
```

Deserialized commands should be wrapped in Gears\CQRS\Async\ReceivedCommand in order to avoid infinite loops should you decide to handle the commands to an async command bus. If you decide to use a non-async bus on the dequeue side you don't need to do this

### Discriminator

Discriminates whether a command should or should not be enqueued based on arbitrary conditions

Three discriminators are provided in this package

* `Gears\CQRS\Async\Discriminator\ArrayCommandDiscriminator` selects commands if they are present in the array provided
* `Gears\CQRS\Async\Discriminator\ClassCommandDiscriminator` selects commands by their class or interface
 * `Gears\CQRS\Async\Discriminator\ParameterCommandDiscriminator` selects commands by the presence of a command payload parameter (optionally by its value as well)

### Command queue

This is the one responsible for actual async handling, which would normally be sending the serialized command to a message queue system such as RabbitMQ

No implementation is provided but an abstract base class so you can extend from it



```
use Gears\CQRS\Async\AbstractCommandQueue;

class CustomCommandQueue extends AbstractCommandQueue
{
  public function send(Command $command): void
  {
    // Do the actual enqueue of $this->getSerializedCommand($command);
  }
}
```

You can use [cqrs-async-queue-interop](https://github.com/phpgears/cqrs-async-queue-interop) that uses [queue-interop](https://github.com/queue-interop/queue-interop) for enqueuing messages

### Serializer

Abstract command queue uses serializers to do command serialization so it can be sent to the message queue as a string message

`Gears\CQRS\Async\Serializer\JsonCommandSerializer` is directly provided as a general serializer allowing maximum compatibility in case of commands being handled by other systems

You can create your own serializer if the one provided does not fit your needs, for example by using _JMS serializer_, by implementing `Gears\CQRS\Async\Serializer\CommandSerializer` interface

### Distributed systems

On distributed systems, such as micro-service systems, commands can be dequeued on a completely different part of the system, this part should of course know about commands and their contents but could eventually not have access to the command class itself

For example in the context of Domain Events on DDD a bounded context could handle command delivered by another completely different bounded context and of course won't be able to deserialize the original command as it is located on another domain

This can be solved in one of two ways, transform messages coming out from the message queue before handing them to the command serializer, or better by creating a custom `Gears\CQRS\Async\Serializer\CommandSerializer` encapsulating this transformation

_Transformation can be as simple as changing command class to be reconstituted_

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/cqrs-async/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/cqrs-async/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/cqrs-async/blob/master/LICENSE) included with the source code for a copy of the license terms.
