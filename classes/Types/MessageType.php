<?php
/** For the documentation, see in the class. */

namespace SmartSoft\Types;

require_once("classes/Types/BaseType.php");

/**
 * The message type with its name. Because this type is defined via multiple tables, it does not use specific fields.
 * As it is a singleton, the instance can be queried with MessageType::getInstance().
 */
final class MessageType extends BaseType {

    private static MessageType $instance;

    /** Creates a new instance, it is private as it is a singleton instance. */
    private function __construct() {
        parent::__construct("message", array());
    }

    /**
     * Returns the instance for this type.
     *
     * @return MessageType The singleton instance.
     */
    public static function getInstance(): MessageType {
        static::$instance ??= new MessageType();
        return static::$instance;
    }

}