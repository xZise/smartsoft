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
    private ?User $user;

    public function __construct(BaseType $baseType) {
        parent::__construct($baseType->getTypeName());
        $this->baseType = $baseType;
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

    /**
     * Inserts a new user into the databases. Uses insertUser() with $_POST for the parameters.
     */
    private function processAddAction(): void {
        $db = new Database();
        try {
            $this->baseType->insertUser($db, $_POST);
        } finally {
            $db = null;
        }
    }

    private function processEditAction(int $id): void {
        /* Update both `user` and the type-related table. */

        $sql = "UPDATE {$this->baseType->getTypeName()} SET ";
        foreach ($this->baseType->getColumns() as $idx => $column) {
            if ($idx > 0) {
                $sql .= ", ";
            }
            $sql .= "$column = :$column";
        }
        $sql .= " WHERE ID = :id";
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare("UPDATE user SET Username = ? WHERE ID = ?");
            $stmt->bindValue(1, $_POST["Username"]);
            $stmt->bindValue(2, $id);
            $stmt->execute();

            $stmt = $db->getDatabase()->prepare($sql);
            $this->baseType->bindParams($stmt, $_POST);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    private function processDeleteAction(int $id): void {
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