<?php

namespace SmartSoft\Processors;

require_once("classes/Database.php");
require_once("classes/Role.php");
require_once("classes/User.php");

require_once("classes/Exceptions/ProcessActionException.php");
require_once("classes/Processors/Processor.php");

use SmartSoft\Database;
use SmartSoft\Role;
use SmartSoft\User;
use SmartSoft\Exceptions\ProcessActionException;
use Throwable;

class AccountProcessor extends Processor {

    private User $user;

    public function __construct() {
        parent::__construct("account");
        $this->user = User::create();
    }

    protected function processAction(String $action) {
        $newPassword = $_POST["new_password"] ?? "";
        if ($newPassword === "" || $newPassword !== $_POST["new_password_repeat"]) {
            throw new ProcessActionException();
        }

        if ($this->user->getRole() == Role::Customer) {
            $table = "customer";
        } else {
            $table = "employee";
        }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare("UPDATE $table SET Password = ? WHERE ID = ?");
            $stmt->bindValue(1, $hash);
            $stmt->bindValue(2, $this->user->getId());
            $stmt->execute();
        } finally {
            $db = null;
        }
    }
}