<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("ArraysWithComplexType")
 */
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
    private $arrayWithUnion;

    /**
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems(
     *         @SerDe\AvroType("map", attributes={@SerDe\AvroValues(@SerDe\AvroType("string"))})
     *     )
     * })
     */
    private $arrayWithMap;
}
