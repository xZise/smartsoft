<?php

namespace SmartSoft;

final class Database {

    private ?\PDO $database;

    public function __construct() {
        $user = "root";
        $this->database = new \PDO('mysql:host=localhost;dbname=smartsoft;charset=utf8mb4', $user);
        $this->database->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function __destruct() {
        $this->database = null;
    }

    public function getDatabase(): \PDO {
        return $this->database;
    }

    public function fetchAll(String $query, int $mode = \PDO::FETCH_NAMED, $params = null): array {
        $stmt = $this->getDatabase()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll($mode);
    }

    public function fetchValue(string $query, $params = null) {
        $stmt = $this->getDatabase()->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row[0];
    }
}