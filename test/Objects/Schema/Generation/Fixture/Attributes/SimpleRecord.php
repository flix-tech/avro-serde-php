<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDefault;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroNamespace;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

#[AvroName("SimpleRecord")]
#[AvroNamespace("org.acme")]
#[AvroType(Type::RECORD)]
class SimpleRecord
{
    #[AvroType(Type::INT)]
    #[AvroDefault(42)]
    private $intType;
}
