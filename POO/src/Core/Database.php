<?php

namespace App\Core;

class Database {

    private static $instance = null;
    public $pdo = null;
   
    public function __construct()
    {
        try {
            $this->pdo = new \PDO("mysql:host=localhost;dbname=helpdesk;charset=utf8",'helpdesk','helpdesk');
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

}