<?php

namespace SmartSoft;

/**
 * A helper class for a database connection. Does use PDO internally.
 */
final class Database {

    private ?\PDO $database;

    /**
     * Creates a new instance with a connection set up.
     */
    public function __construct() {
        $user = "root";
        $this->database = new \PDO('mysql:host=localhost;dbname=smartsoft;charset=utf8mb4', $user);
        $this->database->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * Clears the database connection.
     */
    public function __destruct() {
        $this->database = null;
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
    public function fetchAll(String $query, int $mode = \PDO::FETCH_NAMED, $params = null): array {
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