<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDoc;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroTargetClass;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\Type;

#[AvroType(Type::RECORD)]
#[AvroName("RecordWithRecordType")]
class RecordWithRecordType
{
    #[AvroName("simpleField")]
    #[AvroType(
        Type::RECORD,
        new AvroTargetClass(SimpleRecord::class),
        new AvroDoc("This is a simple record for testing purposes")
    )]
    private $simpleRecord;

    #[AvroName("unionField")]
    #[AvroType(Type::NULL)]
    #[AvroType("org.acme.SimpleRecord")]
    private $unionRecord;
}
