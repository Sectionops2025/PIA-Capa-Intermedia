<?php

class DatabaseConnection {
    private $server = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "OMISO";
    private $connection;

    
    public function __construct() {
        $this->connect();
    }

    
    private function connect() {
        $this->connection = new mysqli($this->server, $this->user, $this->pass, $this->db);

        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }
    }

    
    public function getConnection() {
        return $this->connection;
    }

   
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}


$dbConnection = new DatabaseConnection();
$conex = $dbConnection->getConnection(); 


$dbConnection->closeConnection();
?>
