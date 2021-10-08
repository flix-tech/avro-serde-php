<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroItems;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroValues;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("ArraysWithComplexType")
 */
#[AvroType(Type::RECORD)]
#[AvroName("ArraysWithComplexType")]
class ArraysWithComplexType
{
    /**
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems({
     *         "string",
     *         @SerDe\AvroType("array", attributes={@SerDe\AvroItems(@SerDe\AvroType("string"))})
     *     })
     * })
     */
    #[AvroType(
        Type::ARRAY,
        new AvroItems(
            Type::STRING,
            new AvroType(
                Type::ARRAY,
                new AvroItems(
                    new AvroType(Type::STRING)
                )
            )
        )
    )]
    private $arrayWithUnion;

    /**
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems(
     *         @SerDe\AvroType("map", attributes={@SerDe\AvroValues(@SerDe\AvroType("string"))})
     *     )
     * })
     */
    #[AvroType(
        Type::ARRAY,
        new AvroItems(
            new AvroType(
                Type::MAP,
                new AvroValues(
                    new AvroType(Type::STRING)
                )
            )
        )
    )]
    private $arrayWithMap;
}
