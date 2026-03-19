<?php
class Database {
    private $host = "localhost";
    private $db_name = "attendance_db";
    private $username = "root";
    private $password = ""; 
    public $conn;

    // MAKE SURE THIS FUNCTION NAME MATCHES EXACTLY
    public function getConnection() {
        $this->conn = null;
        try {
            // We connect to the server first to ensure the DB exists
            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // This creates the DB if it's your first time running it
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            $this->conn->exec("USE " . $this->db_name);
            
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>