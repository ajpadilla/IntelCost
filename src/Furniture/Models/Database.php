<?php


namespace IntelCost\Furniture\Models;

use PDO;
use PDOException;

class Database
{
    private $connection;
    private static $instance; //The single instance
    private $host = "intel-cost-mysql";
    private $username = "root";
    private $password = "";
    private $database = "furniture";

    public static function getInstance()
    {
        if(!self::$instance) // If no instance then make one
        {
            self::$instance = new self();
        }
        return self::$instance;
    }


    private function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->database}", "{$this->username}", "{$this->password}");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_PERSISTENT,true);
            $this->connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
        } catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function closeConnection(){
        return $this->connection = null;
    }

    public function __clone(){
        trigger_error('No esta permitido clonar esta clase', E_USER_ERROR);
    }

    public function __wakeup(){
        trigger_error("No puede deserializar una instancia de ". get_class($this) ." class.", E_USER_ERROR );
    }
}