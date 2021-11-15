<?php

namespace SmartSoft\Displays;

require_once("classes/LoginState.php");
require_once("classes/Displays/Display.php");

use SmartSoft\LoginState;
use SmartSoft\Notification;

class LoginDisplay extends Display {

    public function createPage(bool $validPage, bool $validRights): string {
        $notifications = array();
        $loginState = LoginState::getAndResetState();
        
        if ($loginState == LoginState::LoggedOut) {
            $notifications[] = new Notification("Erfolgreich abgemeldet", false);
        } elseif ($loginState == LoginState::Failed) {
            $notifications[] = new Notification("Anmeldeversuch fehlgeschlagen. Vielleicht falscher Benutzername oder falsche Role?", true);
        }
        $notifications = Notification::createNotificationBox($notifications);

        return "<div class=\"login\">
        <div>
            <div>Anmelden</div>
            $notifications
            <form action=\"login_check.php\" method=\"post\">
                <label for=\"username\">Benutzername:</label>
                <input type=\"text\" id=\"username\" name=\"username\">
                <label for=\"role\">Rolle:</label>
                <select id=\"role\" name=\"role\">
                    <option value=\"customer\">Kunde</option>
                    <option value=\"employee\">Mitarbeiter</option>
                </select>
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