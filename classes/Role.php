<?php
namespace SmartSoft;

/** An enum representing different the roles. */
final class Role {
    /** The user is an administrator. Implies that the user is an employee. */
    const Administrator = 0;
    /** The user is an employee. */
    const Employee = 1;
    /** The user is a customer. */
    const Customer = 2;

    private function __construct() {}
}