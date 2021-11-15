<?php

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/User.php");
require_once("classes/Displays/TableDisplay.php");
require_once("classes/Types/EmployeeType.php");
require_once("classes/Types/Field.php");

use SmartSoft\Database;
use SmartSoft\User;
use SmartSoft\HtmlOption;
use SmartSoft\Displays\TableDisplay;
use SmartSoft\Types\EmployeeType;
use SmartSoft\Types\Field;


class EmployeeDisplay extends TableDisplay {

    public function __construct(User $user, String $action) {
        parent::__construct($user, $action, EmployeeType::getInstance());
    }

    public function getList() {
        $db = new Database();
        try {
            $employees = $db->fetchAll($this->getSQLQuery());
        } finally {
            $db = null;
        }
        return $this->getTableInner($employees);
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

    protected function generateEdit($row, Field $field): string {
        if ($field->getListColumn() == "Administrator") {
            $value = $field->getRowValue($row);
            $options = HtmlOption::checked($value > 0) . HtmlOption::disabled($row !== null && $row["ID"] == $this->user->getId());
            return "<label for=\"{$field->getColumn()}\"><input type=\"checkbox\" name=\"{$field->getColumn()}\" id=\"{$field->getColumn()}\" $options/></label>";
        } else {
            return parent::generateEdit($row, $field);
        }
    }

    protected function getSQLQuery(): String {
        return "SELECT ID, Name, Username, Administrator FROM employee";
    }

    protected function getSingular(): String {
        return "Mitarbeiter";
    }

    protected function getPlural(): String {
        return $this->getSingular();
    }
}