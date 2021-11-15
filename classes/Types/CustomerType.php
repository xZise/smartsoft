<?php

namespace SmartSoft\Types;

require_once("classes/Types/BaseType.php");

final class CustomerType extends BaseType {

    private static CustomerType $instance;
    
    private function __construct() {
        parent::__construct("customer", array(new Field("ID", "ID"), new Field("Benutzername", "Username"), new Field("Kundennummer", "CustomerNo"), new Field("Ansprechpartner", "Contact", "ContactName"), new Field("Tarif", "Tariff", "TariffName")));
    }

    public static function getInstance(): CustomerType {
        static::$instance ??= new CustomerType();
        return static::$instance;
    }

}