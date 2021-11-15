<?php

namespace SmartSoft\Types;

final class Field {

    private string $column;
    private string $listColumn;
    private string $description;

    public function __construct(string $description, string $column = null, string $listColumn = null) {
        $this->description = $description;
        $this->column = $column === null ? $this->description : $column;
        $this->listColumn = $listColumn === null ? $this->column : $listColumn;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getColumn(): string {
        return $this->column;
    }

    public function getListColumn(): string {
        return $this->listColumn;
    }

    public function getRowValue(array|null $row) {
        return $row === null ? null : htmlspecialchars($row[$this->getColumn()]);
    }

    public function getListValue(array $row) {
        return htmlspecialchars($row[$this->getListColumn()]);
    }
}