<?php

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

abstract class UserDisplay extends Display {

    protected User $user;
    protected String $action;
    protected String $pageName;

    public function __construct(User $user, String $action, String $pageName) {
        $this->user = $user;
        $this->action = $action;
        $this->pageName = $pageName;
    }

    public abstract function getList();

    public function handleAction(): String {
        if ($this->action == "list") {
            return $this->getList();
        } else {
            throw new InvalidActionException();
        }
    }

    public function createNavbarLink(String $linkPage, String $text) {
        if ($this->pageName == $linkPage) {
            $class = " selected";
        } else {
            $class = "";
        }
        $code = "<div class=\"anim-button$class\"><a href=\"?action=list&page=$linkPage\"><img src=\"img/$linkPage.png\" />$text</a></div>";
        return $code;
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
        if (!$validRights) {
            $notifications[] = new Notification("Sie besitzen nicht die nötigen Rechte für die Seite.", true);
        }
        if (!$validPage) {
            $notifications[] = new Notification("Die Seite gibt es nicht.", true);
        }
        $notifications = Notification::createNotificationBox($notifications);

        return "<header><img src=\"img/SmartSoft-Logo.png\" alt=\"SmartSoft Logo\" title=\"Logo\" /><div>Angemeldeter Benutzer: <span>{$this->user->getName()} ($role)</span></div></header>
    <div class=\"container\">
        <nav>
            $menu
            <div class=\"anim-button\"><form action=\"login_check.php\" method=\"post\"><button name=\"type\" value=\"logout\" type=\"submit\"><img src=\"img/logout.png\"/>Abmelden</button></form></div>
        </nav>
        <main>
                $notifications
                {$this->getTitleRow()}
                $content
        </main>
    </div>";
    }

    public function getTitleRow(): string {
        return "<h1>{$this->getTitle()}</h1>";
    }

    public abstract function checkRights(): bool;
}