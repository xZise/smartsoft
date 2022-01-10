<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Displays;

require_once("classes/LoginState.php");
require_once("classes/Displays/Display.php");

use SmartSoft\LoginState;
use SmartSoft\Notification;

/**
 * A display for the login screen.
 */
class LoginDisplay extends Display {

    /**
     * Creates the login form.
     *
     * @param bool $validPage Currently ignored
     * @param bool $validRights Currently ignored
     * @return string The login form as HTML code.
     */
    public function createPage(bool $validPage, bool $validRights): string {
        $notifications = array();
        $loginState = LoginState::getAndResetState();

        if ($loginState == LoginState::LoggedOut) {
            $notifications[] = new Notification("Erfolgreich abgemeldet", false);
        } elseif ($loginState == LoginState::Failed) {
            $notifications[] = new Notification("Anmeldeversuch fehlgeschlagen. Vielleicht falscher Benutzername, falsches Passwort oder falsche Role?", true);
        }
        $notifications = Notification::createNotificationBox($notifications);

        return "<div class=\"single-container\">
        <div>
            <div>Anmelden</div>
            $notifications
            <form action=\"login_check.php\" method=\"post\" class=\"login\">
                <label for=\"username\">Benutzername:</label>
                <input type=\"text\" id=\"username\" name=\"username\">
                <label for=\"password\">Passwort:</label>
                <input type=\"password\" id=\"password\" name=\"password\">
                <input class=\"anim-button bordered\" type=\"submit\" value=\"Anmelden\" />
                <input type=\"hidden\" name=\"type\" value=\"login\"/>
            </form>
        </div>
    </div>";
    }

    public function getTitle(): string {
        return "Anmelden";
    }

}