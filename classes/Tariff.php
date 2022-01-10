<?php
/** For the documentation, see in the class. */

namespace SmartSoft;

/**
 * An enum representing the different tariffs. The values also represent the IDs in the database.
 */
final class Tariff {
    /** The base tariff. */
    const Basis = 1;
    /** The medium tariff. */
    const Medium = 2;
    /** The premium tariff. */
    const Premium = 3;

    private function __construct() {}
}