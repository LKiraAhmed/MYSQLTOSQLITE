<?php

namespace src\Database;

interface DatabaseInterface{
    public function connect();
    public function createTableIfNotExists($table, $columns);
    public function insert($table,$columns,$data);
    public function getConnection();

}