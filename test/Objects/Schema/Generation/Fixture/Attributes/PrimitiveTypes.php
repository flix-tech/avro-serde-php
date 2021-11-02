<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroAliases;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDefault;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDoc;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroNamespace;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroOrder;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Order;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

#[AvroName("PrimitiveTypes")]
#[AvroNamespace("org.acme")]
#[AvroType(Type::RECORD)]
class PrimitiveTypes
{
    #[AvroDoc("null type")]
    #[AvroType(Type::NULL)]
    private $nullType;

    #[AvroName("isItTrue")]
    #[AvroDefault(false)]
    #[AvroType(Type::BOOLEAN)]
    private $booleanType;

    #[AvroType(Type::INT)]
    private $intType;

    #[AvroType(Type::LONG)]
    #[AvroOrder(Order::ASC)]
    private $longType;

    #[AvroType(Type::FLOAT)]
    #[AvroAliases("foo", "bar")]
    private $floatType;

    #[AvroType(Type::DOUBLE)]
    private $doubleType;

    #[AvroType(Type::BYTES)]
    private $bytesType;

    #[AvroType(Type::STRING)]
    private $stringType;
}
