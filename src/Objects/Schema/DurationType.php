<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class DurationType extends LogicalType
{
    public function __construct()
    {
        parent::__construct(
            TypeName::DURATION,
            TypeName::FIXED,
            ['size' => 12]
        );
    }

    public function name(string $name): self
    {
        return $this->attribute(AttributeName::NAME, $name);
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute(AttributeName::NAMESPACE, $namespace);
    }

    public function aliases(string $alias, string ...$aliases): self
    {
        \array_unshift($aliases, $alias);

        return $this->attribute(AttributeName::ALIASES, $aliases);
    }
}
