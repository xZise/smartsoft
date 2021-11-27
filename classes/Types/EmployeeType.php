<?php

namespace SmartSoft\Types;

require_once("classes/Types/BaseType.php");
require_once("classes/Types/Field.php");

/**
 * The employee type with its name and fields. As it is a singleton, the instance can be queried with
 * EmployeeType::getInstance().
 */
final class EmployeeType extends BaseType {

    private static EmployeeType $instance;

    /** Creates a new instance, it is private as it is a singleton instance. */
    private function __construct() {
        parent::__construct("employee", array(new Field("ID"), new Field("Name"), new Field("Benutzername", "Username"), new Field("Admin", "Administrator")));
    }

    /**
     * Returns the instance for this type.
     *
     * @return EmployeeType The singleton instance.
     */
    public static function getInstance(): EmployeeType {
        static::$instance ??= new EmployeeType();
        return static::$instance;
    }

    /**
     * Returns the parsed value for the column. In case the column is Administrator, it would return whether the value
     * is on. Otherwise it'll call the original method.
     *
     * @param string $column The column for which the value should be determined.
     * @return mixed The value from the POST request.
     */
    protected function getValue(string $column, array $params): mixed {
        if ($column === "Administrator") {
            return $params[$column] == "on";
        } else {
            return parent::getValue($column, $params);
        }
    }

}