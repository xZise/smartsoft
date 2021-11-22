<?php

namespace SmartSoft\Types;

/**
 * The base type with a name and an array of fields.
 */
class BaseType {

    private string $typeName;
    private array $fields;

    /**
     * Creates a new type instance with the given name and fields.
     *
     * @param string $typeName The name of the type.
     * @param string $fields The fields for that type.
     */
    public function __construct(string $typeName, array $fields) {
        $this->typeName = $typeName;
        $this->fields = $fields;
    }

    /**
     * Returns the name.
     *
     * @return string The name.
     */
    public function getTypeName(): string {
        return $this->typeName;
    }

    /**
     * Returns the fields.
     *
     * @return array The fields.
     */
    public function getFields(): array {
        return $this->fields;
    }

}