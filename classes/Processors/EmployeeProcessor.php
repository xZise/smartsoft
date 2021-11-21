<?php

namespace SmartSoft\Processors;

require_once("classes/Processors/TableProcessor.php");
require_once("classes/Types/EmployeeType.php");

use SmartSoft\Types\EmployeeType;

class EmployeeProcessor extends TableProcessor {

    public function __construct() {
        parent::__construct(EmployeeType::getInstance());
    }

    protected function getValue($column) {
        if ($column === "Administrator") {
            return $_POST[$column] == "on";
        } else {
            return parent::getValue($column);
        }
    }
}
