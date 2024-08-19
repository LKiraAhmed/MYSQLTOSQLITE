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

        // إذا لم يتم إدخال اسم جدول، قم بترحيل جميع الجداول
        if (empty($sourceTable)) {
            $tables = $this->getAllTables($sourceConn);
            foreach ($tables as $table) {
                $this->migrateTable($table, $table); // نقل الجدول بنفس الاسم
            }
        } else {
            $this->migrateTable($sourceTable, $destinationTable, $columns);
        }

        echo "Data migrated successfully.";
    }
    private function getTableColumnsWithTypes($sourceTable) {
        $sourceConn = $this->source->getConnection();
        $stmt = $sourceConn->query("DESCRIBE $sourceTable");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $columnDefinitions = [];
        foreach ($columns as $column) {
            $type = $this->mapMysqlTypeToSqlite($column['Type']);
            $columnDefinitions[] = "{$column['Field']} $type";
        }
        return implode(', ', $columnDefinitions);
    }
    
    private function mapMysqlTypeToSqlite($mysqlType) {
        // تحويل نوع بيانات MySQL إلى نوع مكافئ في SQLite
        if (strpos($mysqlType, 'int') !== false) {
            return 'INTEGER';
        } elseif (strpos($mysqlType, 'varchar') !== false || strpos($mysqlType, 'text') !== false) {
            return 'TEXT';
        } elseif (strpos($mysqlType, 'float') !== false || strpos($mysqlType, 'double') !== false) {
            return 'REAL';
        } else {
            return 'TEXT'; 
        }
    }
    private function migrateTable($sourceTable, $destinationTable, $columns = '*') {
        $sourceConn = $this->source->getConnection();
        $destinationConn = $this->destination->getConnection();
    
        if ($columns === '*') {
            $columns = $this->getTableColumns($sourceTable);
            $columnNames = explode(',', $columns);
            $destinationTableColumns = $this->getTableColumnsWithTypes($sourceTable);
        } else {
            $columnNames = explode(',', $columns);
            $destinationTableColumns = $this->generateTableColumnsDefinition($columnNames);
        }
    
        $this->destination->createTableIfNotExists($destinationTable, $destinationTableColumns);
    
        $quotedColumns = array_map(function($col) {
            return "`$col`";
        }, $columnNames);
        $quotedColumnsList = implode(', ', $quotedColumns);
    
        $stmt = $sourceConn->query("SELECT $quotedColumnsList FROM $sourceTable");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($data as $row) {
            $this->destination->insert($destinationTable, $columnNames, $row);
        }
    }
    
    private function getAllTables($sourceConn) {
        $stmt = $sourceConn->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getTableColumns($sourceTable) {
        $sourceConn = $this->source->getConnection();
        $stmt = $sourceConn->query("DESCRIBE $sourceTable");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return implode(',', $columns);
    }

    private function generateTableColumnsDefinition($columns) {
        $columnDefinitions = [];
        foreach ($columns as $column) {
            $columnDefinitions[] = "$column TEXT";
        }
        return implode(', ', $columnDefinitions);
    }
    
    
   
}