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

CQRS command bus utilities for asynchronous bus handling

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

### Discriminator

Discriminates whether a command should or should not be enqueued based on arbitrary conditions

Three discriminators come bundled in this package

* `Gears\CQRS\Async\Discriminator\LocatorCommandDiscriminator` selects commands if they are present in the array provided
* `Gears\CQRS\Async\Discriminator\ClassCommandDiscriminator` selects commands by their class or interface
 * `Gears\CQRS\Async\Discriminator\ParameterCommandDiscriminator` selects commands by the presence of a command payload parameter (optionally by its value as well)

### Serializer

Abstract command queue uses serializers to do command serialization, so it can be sent to the message queue as a string message

Two serializers are available out of the box

* `Gears\CQRS\Async\Serializer\JsonCommandSerializer`, is a general serializer allowing maximum compatibility in case of commands being handled by other systems
* `Gears\CQRS\Async\Serializer\NativePhpCommandSerializer`, is a PHP centric serializer employing PHP native serialization mechanism

You can create your own serializer if the provided ones does not fit your needs, for example by using _JMS serializer_, by implementing `Gears\CQRS\Async\Serializer\CommandSerializer` interface

#### Distributed systems

On distributed systems, such as micro-service systems, commands can be dequeued on a completely different part of the system, this part should of course know about commands and their contents but could eventually not have access to the original command class itself and thus a transformation is needed

This can be solved either by transforming messages coming out from the message queue before handing them to the command serializer, or better by creating your custom `Gears\CQRS\Async\Serializer\CommandSerializer` encapsulating this transformation

In most cases the transformation will be as simple as changing the command class to the one the dequeueing side knows. At the end command payload will most probably stay the same

### Command queue

This is the one responsible for the actual async handling, which would normally be sending the serialized command to a message queue system such as RabbitMQ

No implementation is provided but an abstract base class so you can extend from it

```php
use Gears\CQRS\Async\AbstractCommandQueue;
use Gears\CQRS\Command;

class CustomCommandQueue extends AbstractCommandQueue
{
  public function send(Command $command): void
  {
    // Do the actual enqueue of $this->getSerializedCommand($command);
  }
}
```

You can require [cqrs-async-queue-interop](https://github.com/phpgears/cqrs-async-queue-interop) that uses [queue-interop](https://github.com/queue-interop/queue-interop) for enqueuing messages

## Contributing

Found a bug or have a feature request? [Please open a new issue](https://github.com/phpgears/cqrs-async/issues). Have a look at existing issues before.

See file [CONTRIBUTING.md](https://github.com/phpgears/cqrs-async/blob/master/CONTRIBUTING.md)

## License

See file [LICENSE](https://github.com/phpgears/cqrs-async/blob/master/LICENSE) included with the source code for a copy of the license terms.
