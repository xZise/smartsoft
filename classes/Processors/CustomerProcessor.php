<?php

namespace SmartSoft\Processors;

require_once("classes/Processors/TableProcessor.php");
require_once("classes/Types/CustomerType.php");

use SmartSoft\Types\CustomerType;

/**
 * This processor handles all customer related actions (add, edit or delete).
 */
class CustomerProcessor extends TableProcessor {

    /**
     * Creates a new processor instance for customers.
     */
    public function __construct() {
        parent::__construct(CustomerType::getInstance());
    }
}