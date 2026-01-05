<?php
require_once '../util/database.php';
class Platform{

    private $db;
    private $conn;

    public function __construct(){
        $this->db = DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }


    public function getAllPlatforms() {
        $query = "SELECT * FROM platform";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllConsoleByPlatform($platform) {
       $query = "SELECT * FROM platform WHERE platform LIKE :platformPattern";
      
        $stmt = $this->conn->prepare($query);
        $platformPattern = $platform . '%';

       $stmt->bindParam(':platformPattern', $platformPattern);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}




?>