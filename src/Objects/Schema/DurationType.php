<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class DurationType extends LogicalType
{
    public function __construct()
    {
        parent::__construct('duration', 'fixed', [
            'size' => 12,
        ]);
    }

    public function name(string $name): self
    {
        return $this->attribute('name', $name);
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute('namespace', $namespace);
    }

    public function aliases(string $alias, string ...$aliases): self
    {
        \array_unshift($aliases, $alias);

        return $this->attribute('aliases', $aliases);
    }
}
