<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\TypeOnlyAttribute;

/**
 * @Annotation
 */
final class AvroItems implements TypeOnlyAttribute
{
    use ContainsOnlyTypes;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::ITEMS;
    }
}
