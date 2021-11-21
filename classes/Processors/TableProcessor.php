<?php

namespace SmartSoft\Processors;

require_once("classes/Database.php");
require_once("classes/Role.php");
require_once("classes/User.php");
require_once("classes/Exceptions/ProcessActionException.php");
require_once("classes/Processors/Processor.php");
require_once("classes/Types/BaseType.php");

use SmartSoft\Database;
use SmartSoft\Role;
use SmartSoft\User;
use SmartSoft\Exceptions\ProcessActionException;
use SmartSoft\Types\BaseType;

abstract class TableProcessor extends Processor {

    private BaseType $baseType;
    private array $fields;
    private User $user;

    public function __construct(BaseType $baseType) {
        parent::__construct($baseType->getTypeName());
        $this->baseType = $baseType;
        $this->fields = array();
        foreach ($this->baseType->getFields() as $field) {
            $this->fields[] = $field->getColumn();
        }
        $this->user = User::create();
    }

    protected function processAction(String $action) {
        if ($this->user->getRole() != Role::Administrator) {
            throw new ProcessActionException();
        }

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