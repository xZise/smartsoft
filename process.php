<?php

namespace SmartSoft\Processors;

use SmartSoft\Exceptions\ProcessActionException;
use SmartSoft\Exceptions\InvalidParameterException;

/**
 * This file is called every time some change to the database needs to be executed. It'll select the appropriate
 * processor depending on the selected page and then handles the given action. Unknown pages lead to unsupported
 * behavior.
 */

require_once("classes/LoginState.php");

require_once("classes/Exceptions/ProcessActionException.php");
require_once("classes/Exceptions/InvalidParameterException.php");

require_once("classes/Processors/AccountProcessor.php");
require_once("classes/Processors/EmployeeProcessor.php");
require_once("classes/Processors/CustomerProcessor.php");
require_once("classes/Processors/MessageProcessor.php");

session_start();

\SmartSoft\LoginState::checkLoggedIn();

try {
    unset($_SESSION["processException"]);
    if (isset($_POST["page"])) {
        $page = $_POST["page"];
        $action = $_POST["action"] ?? "";

        // TODO: Check whether action is valid

        switch ($page) {
            case "employee":
                $processor = new EmployeeProcessor();
                break;
            case "customer":
                $processor = new CustomerProcessor();
                break;
            case "account":
                $processor = new AccountProcessor();
                break;
            case "message":
                $processor = new MessageProcessor();
                break;
            default:
                throw new InvalidParameterException(InvalidParameterException::PARAM_PAGE);
        }

        $processor->process($action);
    } else {
        throw new InvalidParameterException(InvalidParameterException::PARAM_PAGE);
    }
} catch (ProcessActionException $e) {
    $_SESSION["processException"] = $e->getHtmlCode();
    header("Location: " . dirname($_SERVER['REQUEST_URI']));
}