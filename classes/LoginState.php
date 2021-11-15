<?php

namespace SmartSoft;

final class LoginState {
    const LoggedOff = 0;
    const Failed = 1;
    const LoggedOut = 2;
    const LoggedIn = 3;

    private function __construct() {}

    public static function getState() {
        $loginState = LoginState::LoggedOff;
        if (isset($_SESSION["state"])) {
            $loginState = $_SESSION["state"];
        }
        return $loginState;
    }

    public static function getAndResetState() {
        $state = LoginState::getState();
        LoginState::setState(LoginState::LoggedOff);
        return $state;
    }

    public static function setState($state) {
        $_SESSION["state"] = $state;
        if ($state != LoginState::LoggedIn) {
            unset($_SESSION["loginId"]);
            unset($_SESSION["loginEmployee"]);
        }
    }

    public static function checkLoggedIn() {
        if (LoginState::getState() != LoginState::LoggedIn) {
            header("Location: " . dirname($_SERVER['REQUEST_URI']) . "/");
            die();
        }
    }

    public static function setLoggedIn(int $id, bool $employee) {
        $_SESSION["loginId"] = $id;
        $_SESSION["loginEmployee"] = $employee;
    }

    public static function getLoggedInId(): int {
        if (isset($_SESSION["loginId"])) {
            return $_SESSION["loginId"];
        } else {
            return -1;
        }
    }

    public static function getLoggedInEmployee(): bool {
        return isset($_SESSION["loginEmployee"]) && $_SESSION["loginEmployee"];
    }
}