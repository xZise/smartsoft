<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/User.php");
require_once("classes/Displays/InputField.php");
require_once("classes/Displays/TableDisplay.php");
require_once("classes/Types/EmployeeType.php");
require_once("classes/Types/Field.php");

use SmartSoft\Database;
use SmartSoft\User;
use SmartSoft\Displays\TableDisplay;
use SmartSoft\Types\EmployeeType;
use SmartSoft\Types\Field;


/**
 * EmployeeDisplay for listing, editing, adding and removing customers.
 */
class EmployeeDisplay extends TableDisplay {

    public function __construct(User $user, string $action) {
        parent::__construct($user, $action, EmployeeType::getInstance());
    }

    protected function getList(): array {
        $db = new Database();
        try {
            return $db->fetchAll($this->getSQLQuery());
        } finally {
            $db = null;
        }
    }

    protected function getFieldValue($row, Field $field): string {
        if ($field->getListColumn() == "Administrator") {
            $value = $field->getListValue($row);
            if ($value > 0) {
                return "☑";
            } else {
                return "☐";
            }
        } else {
            return parent::getFieldValue($row, $field);
        }
    }

    protected function generateEdit($row, Field $field): string|FormField {
        $formField = parent::generateEdit($row, $field);
        if ($field->getListColumn() == "Administrator" && $formField instanceof InputField) {
            $value = $field->getRowValue($row);
            $formField->setAttribute("type", "checkbox");
            $formField->removeAttribute("value");
            $formField->setOption("checked", $value > 0);
            $formField->setOption("disabled", $row !== null && $row["ID"] == $this->user->getId());
        }
        return $formField;
    }

    protected function getSQLQuery(): string {
        return "SELECT
                    user.ID,
                    Name,
                    Username,
                    Administrator,
                    CASE WHEN IFNULL(ContactCount, 0) + IFNULL(MessageCount, 0) > 0 THEN 1 ELSE 0 END AS Constrained
                FROM employee
                JOIN user ON user.ID = employee.ID
                LEFT JOIN (
                    SELECT COUNT(*) AS ContactCount, Contact FROM customer GROUP BY Contact
                    ) contactCounts ON contactCounts.Contact = employee.ID
                LEFT JOIN (
                    SELECT COUNT(*) AS MessageCount, Sender FROM message WHERE Sender IS NOT NULL GROUP BY Sender
                    ) messageCounts ON messageCounts.Sender = employee.ID";
    }

    protected function getSingular(): string {
        return "Mitarbeiter";
    }

    protected function getPlural(): string {
        return $this->getSingular();
    }

    protected function canDelete($row): bool {
        return $row["Constrained"] === 0;
    }
}