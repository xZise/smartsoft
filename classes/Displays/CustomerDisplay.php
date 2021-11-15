<?php

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/User.php");
require_once("classes/Displays/TableDisplay.php");
require_once("classes/Types/CustomerType.php");
require_once("classes/Types/Field.php");

use SmartSoft\Database;
use SmartSoft\User;
use SmartSoft\Role;
use SmartSoft\Displays\TableDisplay;
use SmartSoft\Types\CustomerType;
use SmartSoft\Types\Field;

class CustomerDisplay extends TableDisplay {

    public function __construct(User $user, String $action) {
        parent::__construct($user, $action, CustomerType::getInstance());
    }

    public function getList() {
        $db = new Database();
        try {
            $params = array();
            $condition = "";
            if ($this->user->getRole() != Role::Administrator) {
                $params[] = $this->user->getId();
                $condition = " WHERE customer.Contact = ?";
            }
            $customers = $db->fetchAll("SELECT customer.ID, customer.CustomerNo, customer.Username, employee.Name As ContactName, Tariff, tariff.Name As TariffName
                                        FROM customer
                                        JOIN employee ON customer.Contact = employee.ID
                                        JOIN tariff ON customer.Tariff = tariff.ID $condition", \PDO::FETCH_NAMED, $params);
        } finally {
            $db = null;
        }
        return $this->getTableInner($customers);
    }

    protected function getFieldValue($row, Field $field): string {
        $value = parent::getFieldValue($row, $field);
        if ($field->getColumn() == "Tariff") {
            $tariff = $field->getRowValue($row);
            $value .= " " . str_repeat("🪙 ", $tariff);
            $value = "<span style=\"white-space: nowrap;\">$value</span>";
        }
        return $value;
    }

    private function generateOption(String $name, String $query, $value): String {
        $db = new Database();
        try {
            $data = $db->fetchAll($query);
        } finally {
            $db = null;
        }
        $code = "<select id=\"$name\" name=\"$name\">";
        foreach ($data as $row) {
            $selected = $value !== null && $row["ID"] == $value ? 'selected="selected"' : "";
            $code .= "<option value=\"$row[ID]\" $selected>$row[Name]</option>";
        }
        $code .= "</select>";
        return $code;
    }

    public function generateEdit($row, Field $field): String {
        $value = $field->getRowValue($row);
        if ($field->getColumn() == "Contact") {
            return $this->generateOption($field->getColumn(), "SELECT ID, Name FROM employee ORDER BY Name", $value);
        } elseif ($field->getColumn() == "Tariff") {
            return $this->generateOption($field->getColumn(), "SELECT ID, Name FROM tariff ORDER BY ID", $value);
        } else {
            return parent::generateEdit($row, $field);
        }
    }

    protected function getSQLQuery(): String {
        return "SELECT ID, CustomerNo, Username, Contact, Tariff FROM customer";
    }

    protected function getSingular(): String {
        return "Kunde";
    }

    protected function getPlural(): String {
        return "Kunden";
    }
}