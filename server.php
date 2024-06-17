<?php
require_once __DIR__ . '/vendor/autoload.php';

use src\Database\MySQLDatabase;
use src\Database\SQLiteDatabase;
use src\migrate\Migrate;

echo "Hello, I'm L. This is my private office that helps you transfer data from MySQL to SQLite.\n";

$mysql_host = readline("Enter MySQL host: ");
$mysql_dbname = readline("Enter MySQL database name: ");
$mysql_username = readline("Enter MySQL username: ");
$mysql_password = readline("Enter MySQL password: ");

$mysql_conn = new MySQLDatabase($mysql_host, $mysql_dbname, $mysql_username, $mysql_password);
$mysql_conn->connect();

$sqlite_filePath = readline("Enter SQLite file path (will be created if not exists): ");
$sqlite_conn = new SQLiteDatabase($sqlite_filePath);
$sqlite_conn->connect();

$sourceTable = readline("Enter source table name: ");
$destinationTable = readline("Enter destination table name: ");
$columns = readline("Enter columns to migrate (comma-separated): ");

$migrate = new Migrate($mysql_conn, $sqlite_conn);
$migrate->migrate($sourceTable, $destinationTable, $columns);
