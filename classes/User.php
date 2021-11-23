<?php

namespace SmartSoft;

require_once("classes/LoginState.php");
require_once("classes/Database.php");

use \PDO as PDO;

/**
 * A class representing a user. It is only possible to create an User object, with the create static method.
 */
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
            $result = $db->fetchAll("SELECT
                                        IFNULL(customer.CustomerNo, employee.Name) AS Name,
                                        employee.ID AS EmployeeID,
                                        IFNULL(employee.Administrator, 0) AS Administrator,
                                        user.Username,
                                        user.Password
                                     FROM user
                                     LEFT JOIN customer ON customer.ID = user.ID
                                     LEFT JOIN employee ON employee.ID = user.ID
                                     WHERE user.ID = ?", PDO::FETCH_NAMED, array($id));
            if (count($result) == 1) {
                $result = $result[0];
                if ($result["EmployeeID"] !== null) {
                    if ($result["Administrator"]) {
                        $role = Role::Administrator;
                    } else {
                        $role = Role::Employee;
                    }
                } else {
                    $role = Role::Customer;
                }
                return new User($result["Name"], $result["Username"], $id, $role, $result["Password"] !== null);
            } else {
                return null;
            }
        } finally {
            $db = null;
        }
    }

    private string $name;
    private string $username;
    private int $id;
    private int $role;
    private bool $hasPasswordSet;

    private function __construct(string $name, string $username, int $id, int $role, bool $hasPassword) {
        $this->name = $name;
        $this->username = $username;
        $this->id = $id;
        $this->role = $role;
        $this->hasPasswordSet = $hasPassword;
    }

    /**
     * Returns the name of the user, which is the customer number for customers and the name itself for employees.
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Returns the username of the user.
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Returns the id of the user.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Returns the role of the user.
     */
    public function getRole(): int {
        return $this->role;
    }

    /**
     * Returns whether the user has a password set.
     */
    public function hasPassword(): bool {
        return $this->hasPasswordSet;
    }

}