<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Examples;

use Dotenv\Dotenv;
use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\Assert;

require __DIR__ . '/../vendor/autoload.php';

$dotEnv = new Dotenv(__DIR__ . '/..');
$dotEnv->load();
$dotEnv->required('SCHEMA_REGISTRY_HOST')->notEmpty();

$recordSerializer = DefaultRecordSerializerFactory::get(getenv('SCHEMA_REGISTRY_HOST'));

$avroSchema = '{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "firstName", "type": "string"},
    {"name": "lastName", "type": "string"},
    {"name": "age", "type": "int"}
  ]
}';

echo "Avro Schema:\n";
echo $avroSchema . "\n\n";

$userRecord = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'age' => 42,
];

echo "User record to be serialized:\n";
echo \var_export($userRecord, true) . "\n\n";

$parsedSchema = \AvroSchema::parse($avroSchema);
$serialized = $recordSerializer->encodeRecord('users-value', $parsedSchema, $userRecord);

echo "Confluent Avro wire format serialized binary as hex:\n";
echo bin2hex($serialized) . "\n\n";

// The reader schema may be different than the writer's one, according to the compatibility policies
$readerSchema = Schema::record()
    ->name('user')
    ->field('firstName', Schema::string())
    ->field('age', Schema::int())
    ->parse();

$deserializedRecord = $recordSerializer->decodeMessage($serialized, $readerSchema);

echo "Deserialized User:\n";
echo \var_export($deserializedRecord, true) . "\n";

Assert::assertEquals($deserializedRecord, [
    'firstName' => 'John',
    'age' => 42,
]);
