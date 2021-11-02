<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroItems;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroValues;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

#[AvroType(Type::RECORD)]
#[AvroName("ArraysWithComplexType")]
class ArraysWithComplexType
{
    #[AvroType(
        Type::ARRAY,
        new AvroItems(
            Type::STRING,
            new AvroType(
                Type::ARRAY,
                new AvroItems(
                    new AvroType(Type::STRING)
                )
            )
        )
    )]
    private $arrayWithUnion;

    #[AvroType(
        Type::ARRAY,
        new AvroItems(
            new AvroType(
                Type::MAP,
                new AvroValues(
                    new AvroType(Type::STRING)
                )
            )
        )
    )]
    private $arrayWithMap;
}
