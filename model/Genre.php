<?php
require_once '../util/database.php';
class Genre{

    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }
    public function getAllGenres() {
        $query = "SELECT * FROM genre";
        $result = $this->conn->query($query);

        if ($result) {
            $genres = $result->fetchAll(PDO::FETCH_ASSOC);
            return $genres;
        } else {
            return false; // Gestione dell'errore
        }
    }
    public function getAllGenre() {
        $query = "SELECT * FROM genre ;";
       
         $stmt = $this->conn->prepare($query);
      
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }




}


?>