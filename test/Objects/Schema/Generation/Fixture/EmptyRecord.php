<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroName("EmptyRecord")
 * @SerDe\AvroNamespace("org.acme")
 * @SerDe\AvroType("record")
 */
class EmptyRecord
{
}
