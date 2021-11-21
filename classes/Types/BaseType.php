<?php

namespace SmartSoft\Types;

class BaseType {

    private string $typeName;
    private array $fields;

    public function __construct(string $typeName, array $fields) {
        $this->typeName = $typeName;
        $this->fields = $fields;
    }

    public function getTypeName(): string {
        return $this->typeName;
    }

    public function getFields(): array {
        return $this->fields;
    }

}