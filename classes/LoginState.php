<?php

namespace SmartSoft;

/**
 * Describes the login states and information. Uses $_SESSION to store the
 * state, id and account type.
 */
final class LoginState {
    /** The user is logged off. */
    const LoggedOff = 0;
    /** There was an issue while logging in (e.g. wrong password). */
    const Failed = 1;
    /** The user has been logging off. Used to show a notification that the user logged off. */
    const LoggedOut = 2;
    /** The user is logged in. */
    const LoggedIn = 3;

    // Private constructor, to prevent someone to create an object of this.
    private function __construct() {}

    /**
     * Returns the login state falling back to LoginState::LoggedOff.
     *
     * @return int The login state.
     */
    public static function getState(): int {
        $loginState = LoginState::LoggedOff;
        if (isset($_SESSION["state"])) {
            $loginState = $_SESSION["state"];
        }
        return $loginState;
    }

    /**
     * Returns the login state and resets it to LoginState::LoggedOff afterwards.
     *
     * @return int The login state before it was reset.
     */
    public static function getAndResetState(): int {
        $state = LoginState::getState();
        LoginState::setState(LoginState::LoggedOff);
        return $state;
    }

    /**
     * Sets the login state to the given state. When the state is not set to
     * LoginStat::LoggedIn, it'll unset the login id and account type.
     */
    public static function setState(int $state) {
        $_SESSION["state"] = $state;
        if ($state != LoginState::LoggedIn) {
            unset($_SESSION["loginId"]);
        }
    }

    /**
     * Checks whether a user is not logged in. When the user is not logged in,
     * it'll redirect to the index page.
     */
    public static function checkLoggedIn() {
        if (LoginState::getState() != LoginState::LoggedIn) {
            header("Location: " . dirname($_SERVER['REQUEST_URI']) . "/");
            die();
        }
    }

    /**
     * Sets the current logged in id and account type.
     *
     * @param int $id The id of the logged in user.
     * @param bool $employee Whether this user is an employee.
     */
    public static function setLoggedIn(int $id) {
        $_SESSION["loginId"] = $id;
    }

    /**
     * Returns the logged in id or -1, when no one is logged in.
     *
     * @return int The logged in id.
     */
    public static function getLoggedInId(): int {
        if (isset($_SESSION["loginId"])) {
            return $_SESSION["loginId"];
        } else {
            return -1;
        }
    }
}