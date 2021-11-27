<?php

namespace SmartSoft\Processors;

require_once("classes/Database.php");
require_once("classes/Role.php");
require_once("classes/User.php");
require_once("classes/Exceptions/ProcessActionException.php");
require_once("classes/Exceptions/InvalidParameterException.php");
require_once("classes/Processors/Processor.php");
require_once("classes/Types/BaseType.php");

use SmartSoft\Database;
use SmartSoft\Role;
use SmartSoft\User;
use SmartSoft\Exceptions\ProcessActionException;
use SmartSoft\Exceptions\InvalidParameterException;
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
            if ($field->getColumn() !== "ID" &&
                    $field->getColumn() !== "Username" && $field->getColumn() !== "Password") {
                $this->fields[] = $field->getColumn();
            }
        }
        $this->user = User::create();
    }

    protected function processAction(string $action) {
        if ($this->user->getRole() != Role::Administrator) {
            throw new ProcessActionException(ProcessActionException::MISSING_PERMISSION);
        }

        if ($action != "add") {
            if (isset($_POST["ID"])) {
                $id = $_POST["ID"];
            } else {
                throw new InvalidParameterException("ID");
            }

            if ($action == "edit") {
                $this->processEditAction($id);
            } elseif ($action == "delete") {
                $this->processDeleteAction($id);
            } else {
                throw new InvalidParameterException(InvalidParameterException::PARAM_ACTION);
            }
        } else {
            $this->processAddAction();
        }
    }

    protected function getValue(string $column): mixed {
        return $_POST[$column];
    }

    private function bindParams(\PDOStatement $stmt) {
        foreach ($this->fields as $idx => $column) {
            $stmt->bindValue($idx + 1, $this->getValue($column));
        }
    }

    public function processAddAction() {
        /* First insert into `user` to get ID and then insert that into the type-related table. */

        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare("INSERT INTO user (Username, Password) VALUES (?, NULL)");
            $stmt->bindValue(1, $_POST["Username"]);
            $stmt->execute();

            $sql = "INSERT INTO {$this->baseType->getTypeName()} (ID, " . implode(", ", $this->fields) . ")
                    VALUES (LAST_INSERT_ID(), " . implode(", ", array_fill(0, count($this->fields), "?")) . ")";

            $stmt = $db->getDatabase()->prepare($sql);
            $this->bindParams($stmt);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    public function processEditAction(int $id) {
        /* Update both `user` and the type-related table. */

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
            $stmt = $db->getDatabase()->prepare("UPDATE user SET Username = ? WHERE ID = ?");
            $stmt->bindValue(1, $_POST["Username"]);
            $stmt->bindValue(2, $id);
            $stmt->execute();

            $stmt = $db->getDatabase()->prepare($sql);
            $this->bindParams($stmt);
            $stmt->bindParam(count($this->fields) + 1, $id);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    public function processDeleteAction(int $id) {
        $sql = "DELETE FROM user WHERE ID = ?";
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