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
        if ($this->user->getRole() != Role::Customer) {
            throw new ProcessActionException();
        }
        
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