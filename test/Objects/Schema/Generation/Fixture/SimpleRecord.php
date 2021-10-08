<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDefault;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroNamespace;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

/**
 * @SerDe\AvroName("SimpleRecord")
 * @SerDe\AvroNamespace("org.acme")
 * @SerDe\AvroType("record")
 */
#[AvroName("SimpleRecord")]
#[AvroNamespace("org.acme")]
#[AvroType(Type::RECORD)]
class SimpleRecord
{
    /**
     * @SerDe\AvroType("int")
     * @SerDe\AvroDefault(42)
     */
    #[AvroType(Type::INT)]
    #[AvroDefault(42)]
    private $intType;
}
