<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Examples;

use Dotenv\Dotenv;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use FlixTech\AvroSerializer\Objects\SchemaResolvers\CallableResolver;
use PHPUnit\Framework\Assert;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Widmogrod\Functional\constt;

require __DIR__ . '/../vendor/autoload.php';

$dotEnv = new Dotenv(__DIR__ . '/..');
$dotEnv->load();
$dotEnv->required('SCHEMA_REGISTRY_HOST')->notEmpty();

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

$recordSerializer = DefaultRecordSerializerFactory::get(getenv('SCHEMA_REGISTRY_HOST'));

$avroSchema = '{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "age", "type": "int"}
  ]
}';

echo "Avro Schema:\n";
echo $avroSchema . "\n\n";

$user = new User('Thomas', 38);

echo "User object to be serialized:\n";
echo \var_export($user, true) . "\n\n";

$schemaResolver = new CallableResolver(constt(\AvroSchema::parse($avroSchema)));

$normalizer = new GetSetMethodNormalizer();
$encoder = new AvroSerDeEncoder($recordSerializer);

$symfonySerializer = new Serializer([$normalizer], [$encoder]);

$serialized = $symfonySerializer->serialize(
    $user,
    AvroSerDeEncoder::FORMAT_AVRO,
    [
        AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 'users-value',
        AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => $schemaResolver->valueSchemaFor($user),
    ]
);

echo "Confluent Avro wire format serialized binary as hex:\n";
echo bin2hex($serialized) . "\n\n";

$deserializedUser = $symfonySerializer->deserialize($serialized, User::class, AvroSerDeEncoder::FORMAT_AVRO);

echo "Deserialized User:\n";
echo \var_export($deserializedUser, true) . "\n";

Assert::assertEquals($deserializedUser, $user);
