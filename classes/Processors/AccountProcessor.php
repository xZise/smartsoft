<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Processors;

require_once("classes/Database.php");
require_once("classes/User.php");

require_once("classes/Exceptions/ProcessActionException.php");
require_once("classes/Processors/Processor.php");

use SmartSoft\Database;
use SmartSoft\User;
use SmartSoft\Exceptions\ProcessActionException;

/**
 * This processor handles changing the password of the currently logged in user.
 */
class AccountProcessor extends Processor {

    private User $user;

    public function __construct() {
        parent::__construct("account");
        $this->user = User::create();
    }

    protected function processAction(string $action) {
        $newPassword = $_POST["new_password"] ?? "";
        if ($newPassword !== $_POST["new_password_repeat"]) {
            throw new ProcessActionException("Passwörter stimmen nicht überein");
        }

        if ($newPassword !== "") {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            $hash = null;
        }
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare("UPDATE user SET Password = ? WHERE ID = ?");
            $stmt->bindValue(1, $hash);
            $stmt->bindValue(2, $this->user->getId());
            $stmt->execute();
        } finally {
            $db = null;
        }
    }

    protected function getRedirectAction(): string {
        return "";
    }
}