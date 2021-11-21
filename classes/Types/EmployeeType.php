<?php

namespace SmartSoft\Types;

require_once("classes/Types/BaseType.php");
require_once("classes/Types/Field.php");

final class EmployeeType extends BaseType {

    private static EmployeeType $instance;
    
    private function __construct() {
        parent::__construct("employee", array(new Field("ID"), new Field("Name"), new Field("Benutzername", "Username"), new Field("Admin", "Administrator")));
    }

    public static function getInstance(): EmployeeType {
        static::$instance ??= new EmployeeType();
        return static::$instance;
    }

}