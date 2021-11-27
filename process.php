<?php
/**
 * This file is called every time some change to the database needs to be executed. It'll select the appropriate
 * processor depending on the selected page and then handles the given action. Unknown pages lead to unsupported
 * behavior.
 */

namespace SmartSoft\Processors;

use SmartSoft\Database;

use SmartSoft\Exceptions\ProcessActionException;
use SmartSoft\Exceptions\InvalidParameterException;
use SmartSoft\LoginState;

require_once("classes/Database.php");
require_once("classes/LoginState.php");

require_once("classes/Exceptions/ProcessActionException.php");
require_once("classes/Exceptions/InvalidParameterException.php");

require_once("classes/Processors/AccountProcessor.php");
require_once("classes/Processors/EmployeeProcessor.php");
require_once("classes/Processors/CustomerProcessor.php");
require_once("classes/Processors/InstallProcessor.php");
require_once("classes/Processors/MessageProcessor.php");

session_start();

try {
    unset($_SESSION["processException"]);

    $page = $_POST["page"];
    $action = $_POST["action"] ?? "";

    $db = new Database(false);
    try {
        $installed = $db->checkInstalled();
    } finally {
        $db = null;
    }

    if (!$installed) {
        if ($page == "install") {
            $processor = new InstallProcessor();
        } else {
            throw new InvalidParameterException(InvalidParameterException::PARAM_PAGE);
        }
    } elseif (LoginState::getState() != LoginState::LoggedIn) {
        throw new ProcessActionException(ProcessActionException::MISSING_PERMISSION);
    } else {
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
    }

    $processor->process($action);
} catch (ProcessActionException $e) {
    $_SESSION["processException"] = $e->getHtmlCode();
    header("Location: " . dirname($_SERVER['REQUEST_URI']));
}