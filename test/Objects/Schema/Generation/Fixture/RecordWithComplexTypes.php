<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroAliases;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDefault;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroItems;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroNamespace;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroOrder;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroSize;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroSymbols;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroValues;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Order;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("RecordWithComplexTypes")
 */
#[AvroType(Type::RECORD)]
#[AvroName("RecordWithComplexTypes")]
class RecordWithComplexTypes
{
    /**
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems("string"),
     *     @SerDe\AvroDefault({"foo", "bar"}),
     * })
     */
    #[AvroType(
        Type::ARRAY,
        new AvroItems(Type::STRING),
        new AvroDefault(["foo", "bar"]),
    )]
    private $array;

    /**
     * @SerDe\AvroType("map", attributes={
     *     @SerDe\AvroValues("int"),
     *     @SerDe\AvroDefault({"foo": 42, "bar": 42}),
     * })
     */
    #[AvroType(
        Type::MAP,
        new AvroValues(Type::INT),
        new AvroDefault(['foo' => 42, 'bar' => 42]),
    )]
    private $map;

    /**
     * @SerDe\AvroOrder("ascending")
     * @SerDe\AvroType("enum", attributes={
     *     @SerDe\AvroName("Suit"),
     *     @SerDe\AvroSymbols({"SPADES", "HEARTS", "DIAMONDS", "CLUBS"})
     * })
     */
    #[AvroOrder(Order::ASC)]
    #[AvroType(
        Type::ENUM,
        new AvroName("Suit"),
        new AvroSymbols("SPADES", "HEARTS", "DIAMONDS", "CLUBS"),
    )]
    private $enum;

    /**
     * @SerDe\AvroType("fixed", attributes={
     *     @SerDe\AvroName("md5"),
     *     @SerDe\AvroNamespace("org.acme"),
     *     @SerDe\AvroAliases({"foo", "bar"}),
     *     @SerDe\AvroSize(16)
     * })
     */
    #[AvroType(
        Type::FIXED,
        new AvroName("md5"),
        new AvroNamespace("org.acme"),
        new AvroAliases("foo", "bar"),
        new AvroSize(16)
    )]
    private $fixed;

    /**
     * @SerDe\AvroType("string")
     * @SerDe\AvroType("int")
     * @SerDe\AvroType("array", attributes={
     *     @SerDe\AvroItems("string"),
     * })
     */
    #[AvroType(Type::STRING)]
    #[AvroType(Type::INT)]
    #[AvroType(
        Type::ARRAY,
        new AvroItems(Type::STRING),
    )]
    private $union;
}
