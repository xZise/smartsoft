<?php

namespace SmartSoft\Displays;

require_once("classes/HtmlOption.php");
require_once("classes/User.php");
require_once("classes/Displays/UserDisplay.php");

use SmartSoft\HtmlOption;
use SmartSoft\User;

class AccountDisplay extends UserDisplay {

    public function __construct(User $user, String $action) {
        parent::__construct($user, $action, "account");
    }

    public function getList() {
        $disabled = HtmlOption::disabled();
        if ($this->user->hasPassword()) {
            $oldPasswordDisabled = "";
            $oldPassword = "";
            $oldPasswordType = "password";
        } else {
            $oldPasswordDisabled = $disabled;
            $oldPassword = "Kein Passwort festgelegt";
            $oldPasswordType = "text";
        }
        $code = "<form action=\"process.php\" method=\"post\" class=\"table\">
                    <label for=\"username\">Benutzername:</label>
                    <input type=\"text\" id=\"username\" name=\"username\" $disabled value=\"{$this->user->getUsername()}\" />
                    <label for=\"old_password\">Altes Passwort:</label>
                    <input type=\"$oldPasswordType\" id=\"old_password\" name=\"old_password\" value=\"$oldPassword\" $oldPasswordDisabled />
                    <label for=\"new_password\">Neues Passwort:</label>
                    <input type=\"password\" id=\"new_password\" name=\"new_password\" />
                    <label for=\"new_password_repeat\">Passwort wiederholen:</label>
                    <input type=\"password\" id=\"new_password_repeat\" name=\"new_password_repeat\" />
                    <input type=\"hidden\" name=\"page\" value=\"account\" />
                    <input type=\"submit\" class=\"anim-button bordered\">
                 </form>";

        return $code;
    }

    public function getTitle(): String {
        return "Konto";
    }

    public function checkRights(): bool {
        return true;
    }
}