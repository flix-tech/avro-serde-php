<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroName("PrimitiveTypes")
 * @SerDe\AvroNamespace("org.acme")
 * @SerDe\AvroType("record")
 */
class PrimitiveTypes
{
    /**
     * @SerDe\AvroDoc("null type")
     * @SerDe\AvroType("null")
     */
    private $nullType;

    /**
     * @SerDe\AvroName("isItTrue")
     * @SerDe\AvroDefault(false)
     * @SerDe\AvroType("boolean")
     */
    private $booleanType;

    /**
     * @SerDe\AvroType("int")
     */
    private $intType;

    /**
     * @SerDe\AvroType("long")
     * @SerDe\AvroOrder("ascending")
     */
    private $longType;

    /**
     * @SerDe\AvroType("float")
     * @SerDe\AvroAliases({"foo", "bar"})
     */
    private $floatType;

    /**
     * @SerDe\AvroType("double")
     */
    private $doubleType;

    /**
     * @SerDe\AvroType("bytes")
     */
    private $bytesType;

    /**
     * @SerDe\AvroType("string")
     */
    private $stringType;
}
