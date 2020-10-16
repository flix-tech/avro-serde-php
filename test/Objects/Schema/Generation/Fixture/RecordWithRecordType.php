<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("RecordWithRecordType")
 */
class RecordWithRecordType
{
    /**
     * @SerDe\AvroName("simple")
     * @SerDe\AvroType("record", attributes={
     *     @SerDe\AvroTargetClass("\FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\SimpleRecord")
     * })
     */
    private $simpleRecord;
}
