<?php

namespace SmartSoft\Displays;

require_once("classes/Database.php");
require_once("classes/Role.php");
require_once("classes/User.php");
require_once("classes/Displays/UserDisplay.php");
require_once("classes/Exceptions/InsufficientRightsException.php");
require_once("classes/Types/BaseType.php");
require_once("classes/Types/Field.php");

use SmartSoft\Database;
use SmartSoft\Role;
use SmartSoft\User;
use SmartSoft\Exceptions\InsufficientRightsException;
use SmartSoft\Types\BaseType;
use SmartSoft\Types\Field;

abstract class TableDisplay extends UserDisplay {

    private bool $canModify;
    private $properties;
    private String $nameProperty;

    public function __construct(User $user, String $action, BaseType $baseType) {
        parent::__construct($user, $action, $baseType->getTypeName());
        $this->properties = $baseType->getFields();
        $this->canModify = $this->user->getRole() == Role::Administrator;
        $this->nameProperty = $this->properties[1]->getColumn();
    }

    public function checkRights(): bool {
        return $this->user->getRole() != Role::Customer && ($this->action == "list" || $this->canModify);
    }

    private function checkModify() {
        if (!$this->canModify) {
            throw new InsufficientRightsException();
        }
    }

    protected function generateEdit($row, Field $field): String {
        $value = $row == null ? "" : $field->getRowValue($row);
        return "<input type=\"text\" name=\"{$field->getColumn()}\" id=\"{$field->getColumn()}\" value=\"$value\" />";
    }

    private function createEditForm($item) {
        $action = $item === null ? "add" : "edit";
        $htmlCode = "<form class=\"table\" action=\"process.php\" method=\"POST\"><input type=\"hidden\" name=\"page\" value=\"{$this->pageName}\" /><input type=\"hidden\" name=\"action\" value=\"$action\" />";
        foreach ($this->properties as $field) {
            if ($field->getColumn() == "ID") {
                if ($item !== null) {
                    $htmlCode .= "<input type=\"hidden\" name=\"{$field->getColumn()}\" value=\"$item[ID]\" />";
                }
            } else {
                $htmlCode .= "<label for=\"{$field->getColumn()}\">{$field->getDescription()}:</label>";
                $htmlCode .= $this->generateEdit($item, $field);
            }
        }
        $htmlCode .= "<input type=\"submit\" class=\"anim-button bordered\"></form>";
        return $htmlCode;
    }

    protected function getAddPage() {
        $this->checkModify();

        return $this->createEditForm(null);
    }

    protected function getEditPage() {
        $this->checkModify();

        $item = $this->getQueryItem(false);

        return $this->createEditForm($item);
    }

    protected function getDeletePage() {
        $this->checkModify();

        $item = $this->getQueryItem(false);

        $htmlCode = "Soll {$item[$this->nameProperty]} wirklich gelöscht werden? <form method=\"POST\" action=\"process.php\"><input type=\"hidden\" name=\"page\" value=\"{$this->pageName}\" /><input type=\"hidden\" name=\"ID\" value=\"$item[ID]\" /><button name=\"action\" value=\"delete\">Löschen bestätigen</button></form>";
        return $htmlCode;
    }

    protected function getTableInner($data) {
        $count = count($data);
        $type = $count == 1 ? $this->getSingular() : $this->getPlural();
        $htmlCode = "<div>$count $type"; 
        if ($this->canModify) {
            $htmlCode .= "<form class=\"addnew\"><input type=\"hidden\" name=\"action\" value=\"add\" /><button name=\"page\" value=\"{$this->pageName}\" type=\"submit\" class=\"anim-button bordered\">Neu anlegen</button></form>";
        }
        $htmlCode .= "</div><div><table class=\"list\"><tr>";
        foreach ($this->properties as $field) {
            $htmlCode .= "<th>{$field->getDescription()}</th>";
        }
        if ($this->canModify) {
            $htmlCode .= "<th>Aktion</th>";
        }
        $htmlCode .= "</tr>";
        foreach ($data as $row) {
            $htmlCode .= "<tr>";
            foreach ($this->properties as $field) {
                $htmlCode .= "<td>{$this->getFieldValue($row, $field)}</td>";
            }
            if ($this->canModify) {
                $htmlCode .= "<td><form class=\"operation\"><input type=\"hidden\" value=\"$row[ID]\" name=\"ID\" /><input type=\"hidden\" name=\"page\" value=\"{$this->pageName}\" /><button name=\"action\" value=\"edit\" style=\"color: green;\" class=\"anim-button bordered\">✎</button>
                <button name=\"action\" value=\"delete\" style=\"color: red;\" class=\"anim-button bordered\">✖</button></form></td>";
            }
            $htmlCode .= "</tr>";
        }
        $htmlCode .= "</table></div>";
        return $htmlCode;
    }

    protected function getFieldValue($row, Field $field): string {
        return $field->getListValue($row);
    }

    public function handleAction(): String {
        switch ($this->action) {
            case "add": return $this->getAddPage();
            case "edit": return $this->getEditPage();
            case "delete": return $this->getDeletePage();
            default: return parent::handleAction($this->action);
        }
    }

    private function getQueryItem(bool $inPost) {
        $arr = $inPost ? $_POST : $_GET;
        if (isset($arr["ID"])) {
            return $this->getItem($arr["ID"]);
        } else {
            throw new \Exception();
        }
    }

    protected function getItem(int $id) {
        $db = new Database();
        try {
            $stmt = $db->getDatabase()->prepare("{$this->getSQLQuery()} WHERE ID = ?");
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_NAMED);
        } finally {
            $db = null;
        }
    }

    protected abstract function getSQLQuery(): String;

    protected abstract function getSingular(): String;

    protected abstract function getPlural(): String;

    public function getTitle(): String {
        switch ($this->action) {
            case "add": return "{$this->getSingular()} hinzufügen";
            case "edit": return "{$this->getSingular()} bearbeiten";
            case "delete": return "{$this->getSingular()} entfernen";
            default:
            case "list": return $this->getPlural();
        }
    }
}