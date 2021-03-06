<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/HtmlOption.php");
require_once("classes/Role.php");
require_once("classes/User.php");
require_once("classes/Displays/UserDisplay.php");

use SmartSoft\Database;
use SmartSoft\HtmlOption;
use SmartSoft\Role;
use SmartSoft\User;

/**
 * The display showing the account information and allowing the user to edit the password.
 */
class AccountDisplay extends UserDisplay {

    public function __construct(User $user, string $action) {
        parent::__construct($user, $action, "account");
    }

    public function handleAction(): string {
        $disabled = HtmlOption::disabled();
        if ($this->user->getRole() == Role::Customer) {
            $db = new Database();
            try {
                $contact = $db->fetchValue("SELECT employee.Name
                                            FROM employee
                                            JOIN customer ON customer.Contact = employee.ID
                                            WHERE customer.ID = ?", array($this->user->getId()));
            } finally {
                $db = null;
            }
            $contactRow = "<label for=\"contact\">Ansprechpartner</label>
                           <input type=\"text\" id=\"contact\" $disabled value=\"$contact\" />";
        } else {
            $contactRow = "";
        }
        if ($this->user->hasPassword()) {
            $oldPasswordDisabled = "";
            $oldPassword = "";
            $oldPasswordType = "password";
        } else {
            $oldPasswordDisabled = $disabled;
            $oldPassword = "Kein Passwort festgelegt";
            $oldPasswordType = "text";
        }

        $username = htmlspecialchars($this->user->getUsername());

        return "<form action=\"process.php\" method=\"post\" class=\"table\">
                   <label for=\"username\">Benutzername:</label>
                   <input type=\"text\" id=\"username\" name=\"username\"
                          value=\"{$username}\" $disabled />
                   $contactRow
                   <label for=\"old_password\">Altes Passwort:</label>
                   <input type=\"$oldPasswordType\" id=\"old_password\" name=\"old_password\"
                          value=\"$oldPassword\" $oldPasswordDisabled />
                   <label for=\"new_password\">Neues Passwort:</label>
                   <input type=\"password\" id=\"new_password\" name=\"new_password\" />
                   <label for=\"new_password_repeat\">Passwort wiederholen:</label>
                   <input type=\"password\" id=\"new_password_repeat\" name=\"new_password_repeat\" />
                   <input type=\"hidden\" name=\"page\" value=\"account\" />
                   <input type=\"submit\" class=\"anim-button bordered\">
                </form>";
    }

    public function getTitle(): string {
        return "Konto";
    }

    /**
     * Returns whether this display can be shown. As long as someone is logged in this should always work.
     *
     * @return bool Returns true.
     */
    public function checkRights(): bool {
        return true;
    }
}