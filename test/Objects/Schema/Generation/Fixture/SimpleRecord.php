<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDefault;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroNamespace;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;

/**
 * @SerDe\AvroName("SimpleRecord")
 * @SerDe\AvroNamespace("org.acme")
 * @SerDe\AvroType("record")
 */
#[AvroName("SimpleRecord")]
#[AvroNamespace("org.acme")]
#[AvroType("record")]
class SimpleRecord
{
    /**
     * @SerDe\AvroType("int")
     * @SerDe\AvroDefault(42)
     */
    #[AvroType("int")]
    #[AvroDefault(42)]
    private $intType;
}
