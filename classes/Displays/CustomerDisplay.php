<?php

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/HtmlOption.php");
require_once("classes/User.php");
require_once("classes/Displays/InputField.php");
require_once("classes/Displays/TableDisplay.php");
require_once("classes/Types/CustomerType.php");
require_once("classes/Types/Field.php");

use SmartSoft\Database;
use SmartSoft\HtmlOption;
use SmartSoft\User;
use SmartSoft\Role;
use SmartSoft\Displays\TableDisplay;
use SmartSoft\Types\CustomerType;
use SmartSoft\Types\Field;


/**
 * CustomerDisplay for listing, editing, adding and removing customers.
 */
class CustomerDisplay extends TableDisplay {

    public function __construct(User $user, string $action) {
        parent::__construct($user, $action, CustomerType::getInstance());
    }

    protected function getList(): array {
        $db = new Database();
        try {
            $params = array();
            $condition = "";
            if ($this->user->getRole() != Role::Administrator) {
                $params[] = $this->user->getId();
                $condition = " WHERE customer.Contact = ?";
            }
            $customers = $db->fetchAll("SELECT
                                            customer.ID,
                                            customer.CustomerNo,
                                            user.Username,
                                            employee.Name AS ContactName,
                                            Tariff,
                                            tariff.Name AS TariffName,
                                            IFNULL(ThreadCount, 0) AS ThreadCount
                                        FROM customer
                                        JOIN user ON user.ID = customer.ID
                                        JOIN employee ON customer.Contact = employee.ID
                                        LEFT JOIN (
                                            SELECT COUNT(*) AS ThreadCount, Customer FROM thread
                                            ) counts ON counts.Customer = customer.ID
                                        JOIN tariff ON customer.Tariff = tariff.ID $condition",
                                        \PDO::FETCH_NAMED, $params);
        } finally {
            $db = null;
        }
        return $customers;
    }

    protected function getFieldValue($row, Field $field): string {
        $value = parent::getFieldValue($row, $field);
        if ($field->getColumn() == "Tariff") {
            $tariff = $field->getRowValue($row);
            $tokens = " " . str_repeat("<span>🪙</span>", $tariff);
            $value = "$value <span class=\"tariff-level\">$tokens</span>";
        }
        return $value;
    }

    /**
     * Queries the database with the given query and creates an select with those items. The query needs to have two
     * columns named ID and Name. The Name column is used for the text of the option, while the ID is used for the
     * value of each option. The option which has the same "ID" as to $value will be preselected.
     *
     * @param string $name The name and id of the select.
     * @param string $query The SQL query to determine each selectable value. Needs to return an ID and Name column.
     * @param mixed $value The preselected value. It won't preselect any option when set to null.
     * @return string The HTML code for a select with the queried options.
     */
    private function generateOption(string $name, string $query, $value): string {
        $db = new Database();
        try {
            $data = $db->fetchAll($query);
        } finally {
            $db = null;
        }
        $code = "<select id=\"$name\" name=\"$name\">";
        foreach ($data as $row) {
            $selected = HtmlOption::selected($value !== null && $row["ID"] == $value);
            $code .= "<option value=\"$row[ID]\" $selected>$row[Name]</option>";
        }
        $code .= "</select>";
        return $code;
    }

    public function generateEdit($row, Field $field): string|FormField {
        $value = $field->getRowValue($row);
        if ($field->getColumn() == "Contact") {
            return $this->generateOption($field->getColumn(), "SELECT ID, Name FROM employee ORDER BY Name", $value);
        } elseif ($field->getColumn() == "Tariff") {
            return $this->generateOption($field->getColumn(), "SELECT ID, Name FROM tariff ORDER BY ID", $value);
        } else {
            $formField = parent::generateEdit($row, $field);
            if ($field->getColumn() == "CustomerNo" && $formField instanceof InputField) {
                $formField->setAttribute("minlength", "12");
                $formField->setAttribute("maxlength", "12");
            }
            return $formField;
        }
    }

    protected function getDeleteMessage($item): string {
        $message = "<p>" . parent::getDeleteMessage($item) . "</p>";

        if ($item["MessageCount"] == 1) {
            $messageCount = "einer Nachricht";
        } else {
            $messageCount = "{$item["MessageCount"]} Nachrichten";
        }
        if ($item["ThreadCount"] == 1) {
            $message .= "<p>Es wird damit auch das Gespräch mit $messageCount gelöscht</p>";
        } elseif ($item["ThreadCount"] > 1) {
            $message .= "<p>Es werden damit auch {$item["ThreadCount"]} Gespräche mit $messageCount gelöscht</p>";
        }
        return $message;
    }

    protected function getSQLQuery(): string {
        return "SELECT
                    customer.ID,
                    customer.CustomerNo,
                    customer.Contact,
                    user.Username,
                    employee.Name AS ContactName,
                    Tariff,
                    tariff.Name AS TariffName,
                    IFNULL(ThreadCount, 0) AS ThreadCount,
                    IFNULL(MessageCount, 0) AS MessageCount
                FROM customer
                JOIN user ON user.ID = customer.ID
                JOIN employee ON customer.Contact = employee.ID
                LEFT JOIN (
                    SELECT
                        COUNT(*) As MessageCount,
                        COUNT(DISTINCT(thread.ID)) As ThreadCount,
                        thread.Customer
                    FROM thread
                    JOIN message ON message.Thread = thread.ID
                    GROUP BY thread.Customer
                    ) counts ON counts.Customer = customer.ID
                JOIN tariff ON customer.Tariff = tariff.ID";
    }

    protected function getSingular(): string {
        return "Kunde";
    }

    protected function getPlural(): string {
        return "Kunden";
    }

    protected function canDelete($row): bool {
        return true;
    }
}