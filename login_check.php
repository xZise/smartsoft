<?php

namespace smartsoft;

require_once("classes/LoginState.php");
require_once("classes/Database.php");

session_start();

if (isset($_POST["type"])) {
    $type = $_POST["type"];
} else {
    $type = null;
}

if ($type == "login") {
    if (isset($_POST["username"])) {
        $db = new Database();
        try {
            if (isset($_POST["role"]) && $_POST["role"] == "employee") {
                $result = $db->fetchAll("SELECT ID, Password FROM employee WHERE Username = ?", \PDO::FETCH_NAMED, array($_POST["username"]));
                $isEmployee = true;
            } else {
                $result = $db->fetchAll("SELECT ID, Password FROM customer WHERE Username = ?", \PDO::FETCH_NAMED, array($_POST["username"]));
                $isEmployee = false;
            }
            if (count($result) == 1) {
                $result = $result[0];
                if ($result["Password"] !== null) {
                    $success = password_verify($_POST["password"], $result["Password"]);
                } else {
                    $success = $_POST["password"] === "";
                }
            } else {
                $success = false;
            }
            if ($success) {
                LoginState::setLoggedIn($result["ID"], $isEmployee);
                LoginState::setState(LoginState::LoggedIn);
            } else {
                LoginState::setState(LoginState::Failed);
            }
        } finally {
            $db = null;
        }
    } else {
        LoginState::setState(LoginState::Failed);
    }
} elseif ($type == "logout") {
    LoginState::setState(LoginState::LoggedOut);
} else {
    LoginState::setState(LoginState::Failed);
}

header("Location: " . dirname($_SERVER['REQUEST_URI']) . "/");