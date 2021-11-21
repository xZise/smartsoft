<?php

namespace SmartSoft\Processors;

require_once("classes/LoginState.php");

require_once("classes/Processors/EmployeeProcessor.php");
require_once("classes/Processors/CustomerProcessor.php");
require_once("classes/Processors/MessageProcessor.php");

session_start();

\SmartSoft\LoginState::checkLoggedIn();

if (isset($_POST["page"]) && isset($_POST["action"])) {
    $page = $_POST["page"];
    $action = $_POST["action"];

    // TODO: Check whether page and action are valid

    switch ($page) {
        case "employee":
            $processor = new EmployeeProcessor();
            break;
        case "customer":
            $processor = new CustomerProcessor();
            break;
        default:
            $processor = new MessageProcessor();
            break;
    }

    $processor->process($action);
}