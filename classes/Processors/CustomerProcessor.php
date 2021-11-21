<?php

namespace SmartSoft\Processors;

require_once("classes/Processors/TableProcessor.php");
require_once("classes/Types/CustomerType.php");

use SmartSoft\Types\CustomerType;

class CustomerProcessor extends TableProcessor {

    public function __construct() {
        parent::__construct(CustomerType::getInstance());
    }
}