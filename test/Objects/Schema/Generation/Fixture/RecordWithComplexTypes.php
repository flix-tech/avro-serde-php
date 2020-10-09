<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("RecordWithComplexTypes")
 */
class RecordWithComplexTypes
{
    /**
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems("string"),
     *     @SerDe\AvroDefault({"foo", "bar"}),
     * })
     */
    private $array;

    /**
     * @SerDe\AvroType("map", attributes={
     *     @SerDe\AvroValues("int"),
     *     @SerDe\AvroDefault({"foo": 42, "bar": 42}),
     * })
     */
    private $map;

    /**
     * @SerDe\AvroOrder("ascending")
     * @SerDe\AvroType("enum", attributes={
     *     @SerDe\AvroName("Suit"),
     *     @SerDe\AvroSymbols({"SPADES", "HEARTS", "DIAMONDS", "CLUBS"})
     * })
     */
    private $enum;

    /**
     * @SerDe\AvroType("fixed", attributes={
     *     @SerDe\AvroName("md5"),
     *     @SerDe\AvroNamespace("org.acme"),
     *     @SerDe\AvroAliases({"foo", "bar"}),
     *     @SerDe\AvroSize(16)
     * })
     */
    private $fixed;

    /**
     * @SerDe\AvroType("string")
     * @SerDe\AvroType("int")
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems("string"),
     * })
     */
    private $union;
}
