# Avro SerDe for PHP 7.1+

[![Build Status](https://travis-ci.org/flix-tech/avro-serde-php.svg?branch=master)](https://travis-ci.org/flix-tech/avro-serde-php)
[![Latest Stable Version](https://poser.pugx.org/flix-tech/avro-serde-php/version)](https://packagist.org/packages/flix-tech/avro-serde-php)
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
[Conluent wire format](https://docs.confluent.io/current/schema-registry/docs/serializer-formatter.html#wire-format) and
integrates FlixTech's [Schema Registry Client](https://github.com/flix-tech/schema-registry-php-client).

## Installation

This library is using the [composer package manager](https://getcomposer.org/) for PHP.

```bash
composer require 'flix-tech/avro-serde-php:^1.0'
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

$schemaRegistryClient = new \FlixTech\SchemaRegistryApi\Registry\CachedRegistry(
    new \FlixTech\SchemaRegistryApi\Registry\PromisingRegistry(
        new \GuzzleHttp\Client(['base_uri' => 'registry.example.com'])
    ),
    new \FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter()
);
```

### 2. Build the `RecordSerializer` instance

The `RecordSerializer` is the main way you interact with this library. It provides the `encodeRecord` and
`decodeMessage` methods for SerDe operations.

```php
<?php

/** @var \FlixTech\SchemaRegistryApi\Registry $schemaRegistry */
$recordSerializer = new \FlixTech\AvroSerializer\Objects\RecordSerializer(
    $schemaRegistry,
    [
        // If you want to auto-register missing schemas set this to true
        \FlixTech\AvroSerializer\Objects\RecordSerializer::OPTION_REGISTER_MISSING_SCHEMAS => false,
        // If you want to auto-register missing subjects set this to true
        \FlixTech\AvroSerializer\Objects\RecordSerializer::OPTION_REGISTER_MISSING_SUBJECTS => false,
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
