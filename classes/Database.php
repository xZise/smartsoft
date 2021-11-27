<?php

namespace SmartSoft;

/**
 * A helper class for a database connection. Does use PDO internally.
 */
final class Database {

    public const DATABASE_NAME = "smartsoft";

    private ?\PDO $database;

    /**
     * Creates a new instance with a connection set up.
     *
     * @param bool $database Whether it should connect to the actual database.
     */
    public function __construct(bool $database = true) {
        $user = "root";
        $dbname = $database ? "dbname=" . Database::DATABASE_NAME . ";" : "";
        $this->database = new \PDO("mysql:host=localhost;{$dbname}charset=utf8mb4", $user);
        $this->database->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * Clears the database connection.
     */
    public function __destruct() {
        $this->database = null;
    }

    /**
     * Returns whether the database exists.
     *
     * @return bool Whether the database exists.
     */
    public function checkInstalled(): bool {
        $numberOfDatabases = $this->fetchValue("SELECT COUNT(*)
                                                FROM INFORMATION_SCHEMA.SCHEMATA
                                                WHERE SCHEMA_NAME = '" . Database::DATABASE_NAME . "'");
        return $numberOfDatabases > 0;
    }

    /**
     * Returns the internal database object.
     */
    public function getDatabase(): \PDO {
        return $this->database;
    }

    /**
     * Fetches all rows and returns them.
     *
     * @param string $query The SQL query used.
     * @param int $mode The query mode, by default PDO::FETCH_NAMED.
     * @param mixed $params An array of parameters provided to the query.
     * @return array The result set.
     */
    public function fetchAll(string $query, int $mode = \PDO::FETCH_NAMED, $params = null): array {
        $stmt = $this->getDatabase()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll($mode);
    }

    /**
     * Fetches a single value. When the query returns multiple rows only the first row will be evaluated. It'll always
     * return the value from the first column.
     *
     * @param string $query The SQL query used.
     * @param mixed $params An array of parameters provided to the query.
     * @return mixed The resulting value.
     */
    public function fetchValue(string $query, $params = null): mixed {
        $stmt = $this->getDatabase()->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row[0];
    }
}