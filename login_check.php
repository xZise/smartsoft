<?php

namespace smartsoft;

/**
 * This file is called, whenever someone logs in or out. Depneind on the type it updates the current login state for
 * that session via the LoginState class. It supports the types "login", which also requires "username", "role" and
 * "password", or "logout".
 */

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
            $result = $db->fetchAll("SELECT ID, Password FROM user WHERE Username = ?", \PDO::FETCH_NAMED, array($_POST["username"]));
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
                LoginState::setLoggedIn($result["ID"]);
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