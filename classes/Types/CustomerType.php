<?php

namespace SmartSoft\Types;

require_once("classes/Types/BaseType.php");
require_once("classes/Types/Field.php");

/**
 * The customer type with its name and fields. As it is a singleton, the instance can be queried with
 * CustomerType::getInstance().
 */
final class CustomerType extends BaseType {

    private static CustomerType $instance;
    
    /** Creates a new instance, it is private as it is a singleton instance. */
    private function __construct() {
        parent::__construct("customer", array(new Field("ID", "ID"), new Field("Benutzername", "Username"), new Field("Kundennummer", "CustomerNo"), new Field("Ansprechpartner", "Contact", "ContactName"), new Field("Tarif", "Tariff", "TariffName")));
    }

    /**
     * Returns the instance for this type.
     *
     * @return CustomerType The singleton instance.
     */
    public static function getInstance(): CustomerType {
        static::$instance ??= new CustomerType();
        return static::$instance;
    }

}