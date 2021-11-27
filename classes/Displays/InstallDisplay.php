<?php

namespace SmartSoft\Displays;

require_once("classes/LoginState.php");
require_once("classes/Displays/Display.php");
require_once("classes/Database.php");

use SmartSoft\Database;
use SmartSoft\LoginState;
use SmartSoft\Notification;

/**
 * A display for the login screen.
 */
class InstallDisplay extends Display {

    /**
     * Creates the login form.
     *
     * @param bool $validPage Currently ignored
     * @param bool $validRights Currently ignored
     * @return string The login form as HTML code.
     */
    public function createPage(bool $validPage, bool $validRights): string {
        $notifications = Notification::createNotificationBox(array());

        return "<div class=\"login\">
        <div>
            <div>Installation</div>
            $notifications
            <p>Es gibt keine Datenbank mit dem Namen <span class=\"code\">smartsoft</span>.
            Soll eine Datenbank mit den passenden Tabellen angelegt werden?</p>
            <form action=\"process.php\" method=\"post\">
                <label for=\"withdata\">Mit Testdaten</label>
                <input type=\"checkbox\" checked=\"checked\" name=\"withdata\" id=\"withdata\" />
                <button name=\"page\" value=\"install\" id=\"page\">Datenbank erstellen</button>
            </form>
        </div>
    </div>";
    }

    public function getTitle(): string {
        return "Installation";
    }

}