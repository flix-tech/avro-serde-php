<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("RecordWithRecordType")
 */
class RecordWithRecordType
{
    /**
     * @SerDe\AvroName("simpleField")
     * @SerDe\AvroType("record", attributes={
     *     @SerDe\AvroTargetClass("\FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\SimpleRecord"),
     *     @SerDe\AvroDoc("This is a simple record for testing purposes")
     * })
     */
    private $simpleRecord;

    /**
     * @SerDe\AvroName("unionField")
     * @SerDe\AvroType("null")
     * @SerDe\AvroType("org.acme.SimpleRecord")
     */
    private $unionRecord;
}
