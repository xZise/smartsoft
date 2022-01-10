<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Displays;

require_once("classes/Notification.php");
require_once("classes/Role.php");
require_once("classes/User.php");
require_once("classes/Displays/Display.php");
require_once("classes/Exceptions/InvalidActionException.php");

use SmartSoft\Notification;
use SmartSoft\Role;
use SmartSoft\User;
use SmartSoft\Exceptions\InvalidActionException;

/**
 * UserDisplay is a Display for a specific user. Usually all displays requiring a logged in user will subclass this.
 */
abstract class UserDisplay extends Display {

    protected User $user;
    protected string $action;
    protected string $pageName;

    /**
     * Creates a new UserDisplay instance.
     *
     * @param User $user The user for which this display should work. Usually the logged in user.
     * @param string $action The action selected, can be null when there is no action.
     * @param string $pageName The name of this page.
     */
    public function __construct(User $user, string $action, string $pageName) {
        $this->user = $user;
        $this->action = $action;
        $this->pageName = $pageName;
    }

    /**
     * Handles the selected action, given to the constructor. Places the header, footer and navigation around it.
     *
     * @return string The HTML code containing the content.
     */
    public abstract function handleAction(): string;

    /**
     * Creates a link which can be placed inside the navigation item. Requires an imaged named like "$linkPage.png" in
     * the img/ directory.
     *
     * @param string $linkPage The page it links to.
     * @param string $text The text it should show.
     * @return string The HTML code with the button.
     */
    private function createNavbarLink(string $linkPage, string $text): string {
        if ($this->pageName == $linkPage) {
            $class = " selected";
        } else {
            $class = "";
        }
        return "<div class=\"anim-button$class\">
                    <a href=\"?action=list&page=$linkPage\"><img src=\"img/$linkPage.png\" />$text</a>
                </div>";
    }

    public function createPage(bool $validPage, bool $validRights): string {
        $menu = $this->createNavbarLink("message", "Nachrichten");

        if ($this->user->getRole() != Role::Customer) {
            $menu .= $this->createNavbarLink("employee", "Mitarbeiter");
            $menu .= $this->createNavbarLink("customer", "Kunden");
        }

        switch ($this->user->getRole()) {
            case Role::Administrator:
                $role = "Administrator";
                break;
            case Role::Employee:
                $role = "Mitarbeiter";
                break;
            default:
                $role = "Kunde";
                break;
        }

        try {
            $content = $this->handleAction();
        } catch (InvalidActionException $e) {
            $validPage = false;
            $content = "";
        }

        $notifications = array();
        if (!$this->user->hasPassword()) {
            $notifications[] = new Notification('Bitte legen sie ein Passwort über die <a href="?page=account">Kontoinformation</a> fest.', true);
        }
        if (!$validRights) {
            $notifications[] = new Notification("Sie besitzen nicht die nötigen Rechte für die Seite.", true);
        }
        if (!$validPage) {
            $notifications[] = new Notification("Die Seite gibt es nicht.", true);
        }
        $notifications = Notification::createNotificationBox($notifications);

        return "<header>
                    <img src=\"img/SmartSoft-Logo.png\" alt=\"SmartSoft Logo\" title=\"Logo\" />
                    <div>Angemeldeter Benutzer:
                        <span><a href=\"?page=account\">{$this->user->getName()}</a> ($role)</span>
                    </div>
                </header>
                <div class=\"container\">
                    <nav>
                        $menu
                        <div class=\"anim-button\">
                            <form action=\"login_check.php\" method=\"post\">
                                <button name=\"type\" value=\"logout\" type=\"submit\">
                                    <img src=\"img/logout.png\"/>Abmelden
                                </button>
                            </form>
                        </div>
                    </nav>
                    <main>
                        $notifications
                        {$this->getTitleRow()}
                        $content
                    </main>
                </div>";
    }

    /**
     * Returns the title row of the content. Usually uses the title of this display surrounded by <h1>s.
     *
     * @return string The HTML code for the title row.
     */
    public function getTitleRow(): string {
        return "<h1>{$this->getTitle()}</h1>";
    }

    /**
     * Returns whether this action is valid for the given user.
     *
     * @return bool Whether this action is valid for the given user.
     */
    public abstract function checkRights(): bool;
}