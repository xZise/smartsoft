<?php

namespace SmartSoft\Types;

require_once("classes/Database.php");

use SmartSoft\Database;

/**
 * The base type with a name and an array of fields.
 */
class BaseType {

    private string $typeName;
    private array $fields;
    private array $columns;

    /**
     * Creates a new type instance with the given name and fields.
     *
     * @param string $typeName The name of the type.
     * @param string $fields The fields for that type.
     */
    public function __construct(string $typeName, array $fields) {
        $this->typeName = $typeName;
        $this->fields = $fields;
        $this->columns = array();
        foreach ($fields as $field) {
            if ($field->getColumn() !== "ID" &&
                    $field->getColumn() !== "Username" && $field->getColumn() !== "Password") {
                $this->columns[] = $field->getColumn();
            }
        }
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

    /**
     * Returns the columns of the additional table (excluding the ID, Username and Password).
     *
     * @return array The columns of this type.
     */
    public function getColumns(): array {
        return $this->columns;
    }

    /**
     * Returns the value from the array for the given column. Can be overridden to manipulate the value.
     *
     * @param string $column The name of the column
     * @param string $params The parameters from which the value is read.
     * @return mixed The read value.
     */
    protected function getValue(string $column, array $params): mixed {
        return $params[$column];
    }

    /**
     * Binds the values from the parameter array, using the keys from $fields.
     *
     * @param PDOStatement $stmt The statement to which the values are set.
     * @param array $params The parameters which must have the keys from the $fields.
     */
    public function bindParams(\PDOStatement $stmt, array $params): void {
        foreach ($this->getColumns() as $idx => $column) {
            $stmt->bindValue(":$column", $this->getValue($column, $params));
        }
    }

    /**
     * Inserts a new user into the database.
     *
     * @param Database $db The database instance which is used to create the statements.
     * @param array $params The parameters for the different values. The keys must match the columns from $fields.
     */
    public function insertUser(Database $db, array $params): void {
        /* First insert into `user` to get ID and then insert that into the type-related table. */
        $stmt = $db->getDatabase()->prepare("INSERT INTO user (Username, Password) VALUES (?, ?)");
        $stmt->bindValue(1, $params["Username"]);
        if (isset($params["SetPassword"]) && isset($params["NewPassword"]) && $params["NewPassword"] !== "") {
            $stmt->bindValue(2, $params["NewPassword"]);
        } else {
            $stmt->bindValue(2, null);
        }
        $stmt->execute();

        $columns = $this->getColumns();

        $sql = "INSERT INTO {$this->getTypeName()} (ID, " . implode(", ", $columns) . ")
                VALUES (LAST_INSERT_ID()";
        foreach ($this->getColumns() as $column) {
            $sql .= ", :$column";
        }
        $sql .= ")";

        $stmt = $db->getDatabase()->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();
    }

}