# Avro SerDe for PHP 7.2+

[![Build Status](https://travis-ci.org/flix-tech/avro-serde-php.svg?branch=master)](https://travis-ci.org/flix-tech/avro-serde-php)
[![Latest Stable Version](https://poser.pugx.org/flix-tech/avro-serde-php/version)](https://packagist.org/packages/flix-tech/avro-serde-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/flix-tech/avro-serde-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/flix-tech/avro-serde-php/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/flix-tech/avro-serde-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/flix-tech/avro-serde-php/?branch=master)
[![Total Downloads](https://poser.pugx.org/flix-tech/avro-serde-php/downloads)](https://packagist.org/packages/flix-tech/avro-serde-php)
[![License](https://poser.pugx.org/flix-tech/avro-serde-php/license)](https://packagist.org/packages/flix-tech/avro-serde-php)

## Motivation

When serializing and deserializing messages using the [Avro](http://avro.apache.org/docs/current/) serialization format,
especially when integrating with the [Confluent Platform](https://docs.confluent.io/current/avro.html), you want to make
sure that schemas are evolved in a way that downstream consumers are not affected.

Hence [Confluent](https://www.confluent.io/) developed the
[Schema Registry](https://docs.confluent.io/current/schema-registry/docs/index.html) which has the responsibility to
validate a given schema evolution against a configurable compatibility policy.

Unfortunately Confluent is not providing an official Avro SerDe package for PHP. This library aims to provide an Avro
SerDe library for PHP that implements the
[Confluent wire format](https://docs.confluent.io/current/schema-registry/docs/serializer-formatter.html#wire-format) and
integrates FlixTech's [Schema Registry Client](https://github.com/flix-tech/schema-registry-php-client).

## Installation

This library is using the [composer package manager](https://getcomposer.org/) for PHP.

```bash
composer require 'flix-tech/avro-serde-php:^2.0'
```

## Quickstart

> **NOTE**
>
> You should **always** use a cached schema registry client, since otherwise you'd make an HTTP request for every
> message serialized or deserialized.

### 1. Create a cached Schema Registry client

See the [Schema Registry client documentation on caching](https://github.com/flix-tech/schema-registry-php-client#caching)
for more detailed information.

```php
<?php

use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;

$schemaRegistryClient = new CachedRegistry(
    new PromisingRegistry(
        new Client(['base_uri' => 'registry.example.com'])
    ),
    new AvroObjectCacheAdapter()
);
```

### 2. Build the `RecordSerializer` instance

The `RecordSerializer` is the main way you interact with this library. It provides the `encodeRecord` and
`decodeMessage` methods for SerDe operations.

```php
<?php

use FlixTech\AvroSerializer\Objects\RecordSerializer;

/** @var \FlixTech\SchemaRegistryApi\Registry $schemaRegistry */
$recordSerializer = new RecordSerializer(
    $schemaRegistry,
    [
        // If you want to auto-register missing schemas set this to true
        RecordSerializer::OPTION_REGISTER_MISSING_SCHEMAS => false,
        // If you want to auto-register missing subjects set this to true
        RecordSerializer::OPTION_REGISTER_MISSING_SUBJECTS => false,
    ]
);
```

### 3. Encoding records

This is a simple example on how you can use the `RecordSerializer` to encode messages in the Confluent Avro wire format.

```php
<?php

/** @var \FlixTech\AvroSerializer\Objects\RecordSerializer $recordSerializer */
$subject = 'my-topic-value';
$avroSchema = AvroSchema::parse('{"type": "string"}');
$record = 'Test message';

$encodedBinaryAvro = $recordSerializer->encodeRecord($subject, $avroSchema, $record);
// Send this over the wire...
```

### 4. Decoding messages

This is a simple example on how you can use the `RecordSerializer` to decode messages.

```php
<?php

/** @var \FlixTech\AvroSerializer\Objects\RecordSerializer $recordSerializer */
/** @var string $encodedBinaryAvro */
$record = $recordSerializer->decodeMessage($encodedBinaryAvro);

echo $record; // 'Test message'
```

## Schema Resolvers

Schema Resolvers are responsible to know which Avro schema belongs to which type of record. This is especially useful
if you want to manage your Avro schemas in separate files. Schema Resolvers enable you to integrate with whatever schema
management concept you may have outside of the scope of this library.

Schema Resolvers take a `$record` of any type and try to resolve a matching [`AvroSchema`]() instance for it.

### FileResolver

In even moderately complicated applications you want to manage your schemas within the VCS, most probably as `.avsc`
files. These files contain JSON that is describing the Avro schema.

The resolver takes a `$baseDir` in which you want to manage the files and an inflector `callable`, which is a simple
function that takes the record as first parameter, and a second boolean `$isKey` parameter indicating if the inflection
is targeting a key schema.

```php
<?php

namespace MyNamespace;

use FlixTech\AvroSerializer\Objects\SchemaResolvers\FileResolver;
use function get_class;use function is_object;
use function str_replace;

class MyRecord {}

$record = new MyRecord();

$baseDir = __DIR__ . '/files';

$inflector = static function ($record, bool $isKey) {
    $ext = $isKey ? '.key.avsc' : '.avsc';
    $fileName = is_object($record)
        ? str_replace('\\', '.', get_class($record))
        : 'default';
    
    return $fileName . $ext;
};


echo $inflector($record, false); // MyNamespace.MyRecord.avsc
echo $inflector($record, true); // MyNamespace.MyRecord.key.avsc

$resolver = new FileResolver($baseDir, $inflector);

$resolver->valueSchemaFor($record); // This will load from $baseDir . '/' . MyNamespace.MyRecord.avsc
$resolver->keySchemaFor($record); // This will load from $baseDir . '/' . MyNamespace.MyRecord.key.avsc
```

### CallableResolver

This is the simplest but also most flexible resolver. It just takes two `callables` that are responsible to fetch either
value- or key-schemas respectively. A key schema resolver is optional.

```php
<?php

use FlixTech\AvroSerializer\Objects\SchemaResolvers\CallableResolver;
use PHPUnit\Framework\Assert;
use function Widmogrod\Functional\constt;

$valueSchemaJson = '
{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "age", "type": "int"}
  ]
}
';
$valueSchema = AvroSchema::parse($valueSchemaJson);

$resolver = new CallableResolver(
    constt(
        AvroSchema::parse($valueSchemaJson)
    )
);

$record = [ 'foo' => 'bar' ];

$schema = $resolver->valueSchemaFor($record);

Assert::assertEquals($schema, $valueSchema);
```

### DefinitionInterfaceResolver

This library also provides a [`HasSchemaDefinitionInterface`](src/Objects/HasSchemaDefinitionInterface.php)
that exposes two static methods:

* `HasSchemaDefinitionInterface::valueSchemaJson` returns the schema definition for the value as JSON string
* `HasSchemaDefinitionInterface::keySchemaJson` returns either `NULL` or the schema definition for the key as JSON
string.

The `DefinitionInterfaceResolver` checks if a given record implements that interface (if not it will throw an 
`InvalidArgumentException`) and resolves the schemas via the static methods.

```php
<?php

namespace MyNamespace;

use FlixTech\AvroSerializer\Objects\HasSchemaDefinitionInterface;
use FlixTech\AvroSerializer\Objects\SchemaResolvers\DefinitionInterfaceResolver;

class MyRecord implements HasSchemaDefinitionInterface {
    public static function valueSchemaJson() : string
    {
        return '
               {
                 "type": "record",
                 "name": "user",
                 "fields": [
                   {"name": "name", "type": "string"},
                   {"name": "age", "type": "int"}
                 ]
               }
               ';
    }
    
    public static function keySchemaJson() : ?string
    {
        return '{"type": "string"}';
    }
}

$record = new MyRecord();

$resolver = new DefinitionInterfaceResolver();

$resolver->valueSchemaFor($record); // Will resolve from $record::valueSchemaJson();
$resolver->keySchemaFor($record); // Will resolve from $record::keySchemaJson();
```

### ChainResolver

The chain resolver is a useful tool for composing multiple resolvers. The first resolver to be able to resolve a schema
will win. If none of the resolvers in the chain is able to determine a schema, an `InvalidArgumentException` is thrown.

```php
<?php

namespace MyNamespace;

use FlixTech\AvroSerializer\Objects\SchemaResolvers\ChainResolver;

$record = ['foo' => 'bar'];

/** @var \FlixTech\AvroSerializer\Objects\SchemaResolvers\FileResolver $fileResolver */
/** @var \FlixTech\AvroSerializer\Objects\SchemaResolvers\CallableResolver $callableResolver */

$resolver = new ChainResolver($fileResolver, $callableResolver);
// or new ChainResolver(...[$fileResolver, $callableResolver]);

$resolver->valueSchemaFor($record); // Will resolve $fileResolver, then $callableResolver
$resolver->keySchemaFor($record); // Will resolve $fileResolver, then $callableResolver
```

## Symfony Serializer Integration

This library provides integrations with the [Symfony Serializer component](https://symfony.com/doc/master/components/serializer.html).

```php
<?php

use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use PHPUnit\Framework\Assert;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class User
{
    /** @var string */
    private $name;

    /** @var int */
    private $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}

$recordSerializer = DefaultRecordSerializerFactory::get(
    getenv('SCHEMA_REGISTRY_HOST')
);

$avroSchemaJson = '{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "age", "type": "int"}
  ]
}';

$user = new User('Thomas', 38);

$normalizer = new GetSetMethodNormalizer();
$encoder = new AvroSerDeEncoder($recordSerializer);

$symfonySerializer = new Serializer([$normalizer], [$encoder]);

$serialized = $symfonySerializer->serialize(
    $user,
    AvroSerDeEncoder::FORMAT_AVRO,
    [
        AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 'users-value',
        AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => AvroSchema::parse($avroSchemaJson),
    ]
);

$deserializedUser = $symfonySerializer->deserialize(
    $serialized,
    User::class,
    AvroSerDeEncoder::FORMAT_AVRO
);

Assert::assertEquals($deserializedUser, $user);

```

## Examples

This library provides a few executable examples in the [examples](examples) folder. You should hae a look to get an
understanding how this library works.
