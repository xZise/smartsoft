<?php

namespace SmartSoft\Types;

require_once("classes/Types/BaseType.php");

final class MessageType extends BaseType {

    private static MessageType $instance;
    
    private function __construct() {
        parent::__construct("message", array());
    }

    public static function getInstance(): MessageType {
        static::$instance ??= new MessageType();
        return static::$instance;
    }

}