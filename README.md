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

/* @var Gears\CQRS\Async\CommandQueue $commandQueu */
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

```
use Gears\CQRS\Async\Serializer\JsonCommandSerializer;

/* @var \Gears\CQRS\CommandBus $commandBus */
/* @var your_message_queue_manager $queue */

$serializer = new JsonCommandSerializer();

while (true) {
  $message = $queue->getMessage();
  
  if ($message !== null) {
    $commandBus->handle($serializer->fromSerialized($message))
  }
}
```

Be aware that commands retrieved from the messaging system are being handed to the original CommandBus and not the one decorated with `AsyncCommandBus`. This is very important because otherwise your commands will be continuously enqueued in an infinite loop 

### Discriminator

Discriminates whether a command should or should not be enqueued based on arbitrary conditions

Two discriminators are provided in this package

* `Gears\CQRS\Async\Discriminator\ClassCommandDiscriminator` which selects commands by their class or interface
 * `Gears\CQRS\Async\Discriminator\ParameterCommandDiscriminator` which does it by the presence of a command payload parameter (optionally by its value as well)

### Command queue

This is the one responsible for actual async handling, which would normally be send the serialized command to a message queue system such as RabbitMQ

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

### Serializer

Abstract command queue uses serializers to do command serialization so it can be sent to the message queue as a string message

Two serializers are provided out of the box

* `Gears\CQRS\Async\Serializer\JsonCommandSerializer` which is great in general or if you plan to use other languages aside PHP to handle async commands
* `Gears\CQRS\Async\Serializer\NativeCommandSerializer` only advised if you're only going to use PHP to dequeue commands

It's deadly simple to create your own if this two does not fit your needs by implementing `Gears\CQRS\Async\Serializer\CommandSerializer` interface

_This are helping classes that your custom implementation of `CommandQueue` might not need_

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/cqrs-async/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/cqrs-async/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/cqrs-async/blob/master/LICENSE) included with the source code for a copy of the license terms.
