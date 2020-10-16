<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Examples;

use Dotenv\Dotenv;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use FlixTech\AvroSerializer\Objects\DefaultSchemaGeneratorFactory;
use PHPUnit\Framework\Assert;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

require __DIR__ . '/../vendor/autoload.php';

$dotEnv = new Dotenv(__DIR__ . '/..');
$dotEnv->load();
$dotEnv->required('SCHEMA_REGISTRY_HOST')->notEmpty();

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("user")
 */
class WriterUser
{
    /**
     * @SerDe\AvroType("string")
     * @var string
     */
    private $firstName;

    /**
     * @SerDe\AvroType("string")
     * @var string
     */
    private $lastName;

    /**
     * @SerDe\AvroType("int")
     * @var int
     */
    private $age;

    public function __construct(string $firstName, string $lastName, int $age)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("user")
 */
class ReaderUser
{
    /**
     * @SerDe\AvroType("string")
     * @var string
     */
    private $firstName;

    /**
     * @SerDe\AvroType("int")
     * @var int
     */
    private $age;

    public function __construct(string $firstName, int $age)
    {
        $this->firstName = $firstName;
        $this->age = $age;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

$recordSerializer = DefaultRecordSerializerFactory::get(getenv('SCHEMA_REGISTRY_HOST'));
$schemaGenerator = DefaultSchemaGeneratorFactory::get();

$user = new WriterUser('Francisco', 'Rodrigues', 42);

echo "User object to be serialized:\n";
echo \var_export($user, true) . "\n\n";

$normalizer = new PropertyNormalizer();
$encoder = new AvroSerDeEncoder($recordSerializer);

$symfonySerializer = new Serializer([$normalizer], [$encoder]);

$serialized = $symfonySerializer->serialize(
    $user,
    AvroSerDeEncoder::FORMAT_AVRO,
    [
        AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 'users-value',
        AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => $schemaGenerator->generate(WriterUser::class)->parse(),
    ]
);

echo "Confluent Avro wire format serialized binary as hex:\n";
echo bin2hex($serialized) . "\n\n";

/** @var ReaderUser $deserializedUser */
$deserializedUser = $symfonySerializer->deserialize($serialized, ReaderUser::class, AvroSerDeEncoder::FORMAT_AVRO, [
    AvroSerDeEncoder::CONTEXT_DECODE_READERS_SCHEMA => $schemaGenerator->generate(ReaderUser::class)->parse(),
]);

echo "Deserialized User:\n";
echo \var_export($deserializedUser, true) . "\n";

Assert::assertEquals(
    $user->getFirstName(),
    $deserializedUser->getFirstName()
);

Assert::assertEquals(
    $user->getAge(),
    $deserializedUser->getAge()
);
