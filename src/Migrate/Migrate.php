<?php
namespace src\migrate;

use src\Database\DatabaseInterface;
use PDO;

class Migrate {
    private $source;
    private $destination;

    public function __construct(DatabaseInterface $source, DatabaseInterface $destination) {
        $this->source = $source;
        $this->destination = $destination;
    }

    public function migrate($sourceTable, $destinationTable, $columns) {
        $this->source->connect();
        $this->destination->connect();
        $sourceConn = $this->source->getConnection();
        $destinationConn = $this->destination->getConnection();

        $columnNames = explode(',', $columns);

        $destinationTableColumns = $this->generateTableColumnsDefinition($columnNames);

        $this->destination->createTableIfNotExists($destinationTable, $destinationTableColumns);

        $stmt = $sourceConn->query("SELECT $columns FROM $sourceTable");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as $row) {
            $this->destination->insert($destinationTable, $columnNames, $row);
        }

        echo "Data migrated successfully.";
    }

    private function generateTableColumnsDefinition($columns) {
        $columnDefinitions = [];
        foreach ($columns as $column) {
            $columnDefinitions[] = "$column TEXT";
        }
        return implode(', ', $columnDefinitions);
    }
}
