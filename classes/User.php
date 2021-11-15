<?php

namespace SmartSoft;

require_once("classes/LoginState.php");
require_once("classes/Database.php");

use \PDO as PDO;

final class User {

    /**
     * Creates a new User object of the logged in User. Returns null, if there is no one logged in.
     */
    public static function create(): ?User {
        $id = LoginState::getLoggedInId();
        if ($id < 0) {
            return null;
        }
        $db = new Database();
        try {
            $isEmployee = LoginState::getLoggedInEmployee();
            if ($isEmployee) {
                $result = $db->fetchAll("SELECT Name, Administrator FROM employee WHERE ID = ?", PDO::FETCH_NAMED, array($id));
            } else {
                $result = $db->fetchAll("SELECT CustomerNo As Name FROM customer WHERE ID = ?", PDO::FETCH_NAMED, array($id));
            }
            if (count($result) == 1) {
                if ($isEmployee) {
                    if ($result[0]["Administrator"]) {
                        $role = Role::Administrator;
                    } else {
                        $role = Role::Employee;
                    }
                } else {
                    $role = Role::Customer;
                }
                return new User($result[0]["Name"], $id, $role);
            } else {
                return null;
            }
        } finally {
            $db = null;
        }
    }

    private String $name;
    private int $id;
    private int $role;

    private function __construct(String $name, int $id, int $role) {
        $this->name = $name;
        $this->id = $id;
        $this->role = $role;
    }

    public function getName(): String {
        return $this->name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getRole(): int {
        return $this->role;
    }

}