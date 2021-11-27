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
}
