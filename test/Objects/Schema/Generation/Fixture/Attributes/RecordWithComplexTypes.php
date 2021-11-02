<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes;

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

#[AvroType(Type::RECORD)]
#[AvroName("RecordWithComplexTypes")]
class RecordWithComplexTypes
{
    #[AvroType(
        Type::ARRAY,
        new AvroItems(Type::STRING),
        new AvroDefault(["foo", "bar"]),
    )]
    private $array;

    #[AvroType(
        Type::MAP,
        new AvroValues(Type::INT),
        new AvroDefault(['foo' => 42, 'bar' => 42]),
    )]
    private $map;

    #[AvroOrder(Order::ASC)]
    #[AvroType(
        Type::ENUM,
        new AvroName("Suit"),
        new AvroSymbols("SPADES", "HEARTS", "DIAMONDS", "CLUBS"),
    )]
    private $enum;

    #[AvroType(
        Type::FIXED,
        new AvroName("md5"),
        new AvroNamespace("org.acme"),
        new AvroAliases("foo", "bar"),
        new AvroSize(16)
    )]
    private $fixed;

    #[AvroType(Type::STRING)]
    #[AvroType(Type::INT)]
    #[AvroType(
        Type::ARRAY,
        new AvroItems(Type::STRING),
    )]
    private $union;
}
