<?php
namespace src\Database;

use PDO;
use Exception;

class MySQLDatabase implements DatabaseInterface {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;
    private $connection;

    public function __construct($host, $db, $user, $pass, $charset = 'utf8mb4') {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;
    }

    public function connect() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (Exception $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function createTableIfNotExists($table, $columns) {
        $stmt = $this->connection->query("SHOW CREATE TABLE $table");
        $tableDefinition = $stmt->fetchColumn(1);
        $tableDefinition = str_replace('AUTO_INCREMENT', '', $tableDefinition);
        $tableDefinition = preg_replace('/^\s*CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $tableDefinition);
        $sqlite_conn = new SQLiteDatabase('sqlite.sqlite');
        $sqlite_conn->createTableIfNotExists($table, $columns);
    }

    public function insert($table, $columns, $data) {
        $columns = implode(',', $columns);
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
    }
}
