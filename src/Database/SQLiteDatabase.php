<?php
namespace src\Database;

use PDO;
use PDOException;
use Exception;

class SQLiteDatabase implements DatabaseInterface {
    private $filePath;
    private $connection;

    public function __construct($filePath) {
        $this->filePath = $filePath;
    }

    public function connect() {
        try {
            $this->connection = new PDO("sqlite:" . $this->filePath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function createTableIfNotExists($table, $columns) {
        $sql = "CREATE TABLE IF NOT EXISTS $table ($columns)";
        $this->connection->exec($sql);
    }

    public function insert($table, $columns, $data) {
        $columns = implode(',', $columns);
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
    }
}
