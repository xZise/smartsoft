<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Types;

/**
 * A field for a given type, having a description and two column names. When this field is a column with a foreign key
 * to another table, the column represents the foreign key while the list column represents meaningful data
 * representing the foreign key.
 */
final class Field {

    private string $column;
    private string $listColumn;
    private string $description;

    /**
     * Creates a new field instance.
     *
     * @param string $description The description of this field.
     * @param ?string $column The column this field represents, is set to the description, when null.
     * @param ?string $listColumn The column this field represents inside a list. Is set to the column, when null,
     *                            and also set to description, when column is also null.
     */
    public function __construct(string $description, ?string $column = null, ?string $listColumn = null) {
        $this->description = $description;
        $this->column = $column === null ? $this->description : $column;
        $this->listColumn = $listColumn === null ? $this->column : $listColumn;
    }

    /**
     * Returns the description of this field.
     *
     * @return string The description of this field.
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Returns the column of this field containing the actual data.
     *
     * @return string The column of this field containing the actual data.
     */
    public function getColumn(): string {
        return $this->column;
    }

    /**
     * Returns the column of this field containing meaningful data.
     *
     * @return string The column of this field containing meaningful data.
     */
    public function getListColumn(): string {
        return $this->listColumn;
    }

    /**
     * Returns the value from a row specified by getColumn(). If the row is null, it'll return null. The returned value
     * will have HTML escaped characters by using htmlspecialchars.
     *
     * @param ?array $row The row from which the value should be read. When it is null, it'll return null.
     * @return mixed The value or null, specified by getColumn().
     */
    public function getRowValue(?array $row): mixed {
        return $row === null ? null : htmlspecialchars($row[$this->getColumn()]);
    }

    /**
     * Returns the value from the row specified by getListColumn(). The returned value will have HTML escaped
     * characters by using htmlspecialchars.
     *
     * @param mixed $row The row from which the value should be read.
     * @return mixed The value specified by getListColumn().
     */
    public function getListValue(array $row): mixed {
        return htmlspecialchars($row[$this->getListColumn()]);
    }
}