<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroDoc;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroTargetClass;
use FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes\AvroType;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("RecordWithRecordType")
 */
#[AvroType("record")]
#[AvroName("RecordWithRecordType")]
class RecordWithRecordType
{
    /**
     * @SerDe\AvroName("simpleField")
     * @SerDe\AvroType("record", attributes={
     *     @SerDe\AvroTargetClass("\FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\SimpleRecord"),
     *     @SerDe\AvroDoc("This a simple record for testing purposes")
     * })
     */
    #[AvroName("simpleField")]
    #[AvroType("record",
        new AvroTargetClass(SimpleRecord::class),
        new AvroDoc("This a simple record for testing purposes")
    )]
    private SimpleRecord $simpleRecord;

    /**
     * @SerDe\AvroName("unionField")
     * @SerDe\AvroType("null")
     * @SerDe\AvroType("org.acme.SimpleRecord")
     */
    #[AvroName("unionField")]
    #[AvroType("null")]
    #[AvroType("org.acme.SimpleRecord")]
    private ?SimpleRecord $unionRecord;
}
