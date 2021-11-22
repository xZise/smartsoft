<?php

namespace SmartSoft\Processors;

require_once("classes/Processors/TableProcessor.php");
require_once("classes/Types/EmployeeType.php");

use SmartSoft\Types\EmployeeType;

/**
 * This processor handles all employee related actions (add, edit or delete).
 */
class EmployeeProcessor extends TableProcessor {

    /**
     * Creates a new processor instance for customers.
     */
    public function __construct() {
        parent::__construct(EmployeeType::getInstance());
    }

    /**
     * Returns the parsed value for the column. In case the column is Administrator, it would return whether the value
     * is on. Otherwise it'll call the original method.
     *
     * @param string $column The column for which the value should be determined.
     * @return mixed The value from the POST request.
     */
    protected function getValue(string $column): mixed {
        if ($column === "Administrator") {
            return $_POST[$column] == "on";
        } else {
            return parent::getValue($column);
        }
    }
}
