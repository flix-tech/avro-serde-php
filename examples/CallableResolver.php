<?php

namespace FlixTech\AvroSerializer\Examples\CallableResolver;

use PHPUnit\Framework\Assert;

require __DIR__ . '/../vendor/autoload.php';

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
$valueSchema = \AvroSchema::parse($valueSchemaJson);

$resolver = new \FlixTech\AvroSerializer\Objects\SchemaResolvers\CallableResolver(
    \Widmogrod\Functional\constt(\AvroSchema::parse($valueSchemaJson))
);

$anyData = [ 'foo' => 'bar' ];

$schema = $resolver->valueSchemaFor($anyData);

Assert::assertEquals($valueSchema, $schema);
