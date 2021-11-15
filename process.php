<?php

namespace SmartSoft;

require_once("classes/LoginState.php");
require_once("classes/Database.php");
require_once("classes/User.php");

require_once("classes/Exceptions/ProcessActionException.php");

require_once("classes/Types/Field.php");

require_once("classes/Types/BaseType.php");
require_once("classes/Types/CustomerType.php");
require_once("classes/Types/EmployeeType.php");

use SmartSoft\Exceptions\ProcessActionException;
use SmartSoft\Types\BaseType;
use SmartSoft\Types\CustomerType;
use SmartSoft\Types\EmployeeType;

session_start();

abstract class Processor {

    private string $page;

    public function __construct(string $page) {
        $this->page = $page;
    }

    public function process(String $action) {
        //TODO: We need to check the return value and do something (maybe?)
        try {
            $this->processAction($action);
        } catch (ProcessActionException $e) {

        }

        $params = array("page" => $this->page, "action" => "list");

        $paramsText = "";
        foreach ($params as $name => $value) {
            $paramsText .= $paramsText ? "&" : "?";
            $paramsText .= "$name=$value";
        }

        header("Location: " . dirname($_SERVER['REQUEST_URI']) . "/$paramsText");
    }

    protected abstract function processAction(string $action);

}

abstract class TableProcessor extends Processor {

    private BaseType $baseType;
    private array $fields;

    public function __construct(BaseType $baseType) {
        parent::__construct($baseType->getTypeName());
        $this->baseType = $baseType;
        $this->fields = array();
        foreach ($this->baseType->getFields() as $field) {
            $this->fields[] = $field->getColumn();
        }
    }

    protected function processAction(String $action) {
        if ($action != "add") {
            if (isset($_POST["ID"])) {
                $id = $_POST["ID"];
            } else {
                throw new ProcessActionException();
            }

            if ($action == "edit") {
                $this->processEditAction($id);
            } elseif ($action == "delete") {
                $this->processDeleteAction($id);
            } else {
                throw new ProcessActionException();
            }
        } else {
            $this->processAddAction();
        }
    }

    protected function getValue($column) {
        return $_POST[$column];
    }

    private function bindParams($stmt) {
        foreach ($this->fields as $idx => $column) {
            $stmt->bindValue($idx + 1, $this->getValue($column));
        }
    }

    public function processAddAction() {
        $sql = "INSERT INTO {$this->baseType->getTypeName()} (" . implode(", ", $this->fields) . ") VALUES (" . implode(", ", array_fill(0, count($this->fields), "?")) . ")";
        
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare($sql);
            $this->bindParams($stmt);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    public function processEditAction(int $id) {
        $sql = "UPDATE {$this->baseType->getTypeName()} SET ";
        foreach ($this->fields as $idx => $column) {
            if ($idx > 0) {
                $sql .= ", ";
            }
            $sql .= "$column = ?";
        }
        $sql .= " WHERE ID = ?";
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare($sql);
            $this->bindParams($stmt);
            $stmt->bindParam(count($this->fields) + 1, $id);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    public function processDeleteAction(int $id) {
        $sql = "DELETE FROM {$this->baseType->getTypeName()} WHERE ID = ?";
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }
}

class CustomerProcessor extends TableProcessor {

    public function __construct() {
        parent::__construct(CustomerType::getInstance());
    }
}

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

class MessageProcessor extends Processor {

    private User $user;

    public function __construct() {
        parent::__construct("message");
        $this->user = User::create();
    }

    protected function processAction(String $action) {
        switch ($action) {
            case "send": return $this->processSendAction();
            case "reply": return $this->processReplyAction();
            default: throw new ProcessActionException();
        }
    }

    private function processSendAction() {
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare("INSERT INTO thread (Customer, Subject) VALUES (?, ?)");
            $stmt->bindValue(1, $this->user->getId());
            $stmt->bindValue(2, $_POST["Subject"]);
            $stmt->execute();

            $stmt = $db->getDatabase()->prepare("INSERT INTO message (Thread, Sender, Text) VALUES (LAST_INSERT_ID(), NULL, ?)");
            $stmt->bindValue(1, $_POST["Text"]);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    private function processReplyAction() {
        $db = new Database();
        try {
            if ($this->user->getRole() == Role::Customer) {
                $sender = null;
            } else {
                $sender = $this->user->getId();
            }

            $stmt = $db->getDatabase()->prepare("INSERT INTO message (Thread, Sender, Text) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $_POST["ID"]);
            $stmt->bindValue(2, $sender, \PDO::PARAM_INT);
            $stmt->bindValue(3, $_POST["Text"]);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }
}

function process() {
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
}

var_dump($_POST);

process();