<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("MapsWithComplexType")
 */
class MapsWithComplexType
{
    /**
     * @SerDe\AvroType("map", attributes={
     *     @SerDe\AvroValues({
     *         "string",
     *         @SerDe\AvroType("array", attributes={@SerDe\AvroItems("string")})
     *     })
     * })
     */
    private $mapWithUnion;

    /**
     * @SerDe\AvroType("map", attributes={
     *     @SerDe\AvroValues(
     *         @SerDe\AvroType("array", attributes={@SerDe\AvroItems("string")})
     *     )
     * })
     */
    private $mapWithArray;
}
