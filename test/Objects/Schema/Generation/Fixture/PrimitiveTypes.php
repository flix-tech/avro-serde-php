<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroAliases;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDefault;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDoc;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroNamespace;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroOrder;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Order;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

/**
 * @SerDe\AvroName("PrimitiveTypes")
 * @SerDe\AvroNamespace("org.acme")
 * @SerDe\AvroType("record")
 */
#[AvroName("PrimitiveTypes")]
#[AvroNamespace("org.acme")]
#[AvroType(Type::RECORD)]
class PrimitiveTypes
{
    /**
     * @SerDe\AvroDoc("null type")
     * @SerDe\AvroType("null")
     */
    #[AvroDoc("null type")]
    #[AvroType(Type::NULL)]
    private $nullType;

    /**
     * @SerDe\AvroName("isItTrue")
     * @SerDe\AvroDefault(false)
     * @SerDe\AvroType("boolean")
     */
    #[AvroName("isItTrue")]
    #[AvroDefault(false)]
    #[AvroType(Type::BOOLEAN)]
    private $booleanType;

    /**
     * @SerDe\AvroType("int")
     */
    #[AvroType(Type::INT)]
    private $intType;

    /**
     * @SerDe\AvroType("long")
     * @SerDe\AvroOrder("ascending")
     */
    #[AvroType(Type::LONG)]
    #[AvroOrder(Order::ASC)]
    private $longType;

    /**
     * @SerDe\AvroType("float")
     * @SerDe\AvroAliases({"foo", "bar"})
     */
    #[AvroType(Type::FLOAT)]
    #[AvroAliases("foo", "bar")]
    private $floatType;

    /**
     * @SerDe\AvroType("double")
     */
    #[AvroType(Type::DOUBLE)]
    private $doubleType;

    /**
     * @SerDe\AvroType("bytes")
     */
    #[AvroType(Type::BYTES)]
    private $bytesType;

    /**
     * @SerDe\AvroType("string")
     */
    #[AvroType(Type::STRING)]
    private $stringType;
}
