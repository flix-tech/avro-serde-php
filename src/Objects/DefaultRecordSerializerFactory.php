<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use Assert\Assert;
use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;

final class DefaultRecordSerializerFactory
{
    /**
     * @param array<string,mixed> $guzzleClientOptions
     */
    public static function get(
        string $schemaRegistryURL,
        array $guzzleClientOptions = [],
        ?CacheAdapter $adapter = null
    ): RecordSerializer {
        Assert::that($schemaRegistryURL)->url();

        $guzzleClientOptions = \array_merge(
            $guzzleClientOptions,
            ['base_uri' => $schemaRegistryURL]
        );

        return new RecordSerializer(
            new CachedRegistry(
                new PromisingRegistry(
                    new Client($guzzleClientOptions)
                ),
                $adapter ?? new AvroObjectCacheAdapter()
            ),
            [
                RecordSerializer::OPTION_REGISTER_MISSING_SUBJECTS => true,
                RecordSerializer::OPTION_REGISTER_MISSING_SCHEMAS => true,
            ]
        );
    }
}
