<?php


require_once '../util/database.php';

class Key
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function getKeyFromId($keyId) {
        $sql = "SELECT * FROM keys_ WHERE id = :keyId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':keyId', $keyId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markKeyAsAcquired($keyId) {
        $sql = "UPDATE keys_ SET acquired = 1 WHERE id = :keyId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':keyId', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function insertNewKey($keyValue, $gameId) {
        $sql = "INSERT INTO keys_ (key_value, game_id, acquired) VALUES (:keyValue, :gameId, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':keyValue', $keyValue, PDO::PARAM_STR);
        $stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $success = $stmt->execute();

        if ($success) {
            // Ottieni l'ID dell'ultima chiave inserita
            $lastInsertId = $this->conn->lastInsertId();
            $lastkey = $this->getKeyFromId($lastInsertId);

            return $lastkey;
        } else {
            return false;
        }
    }

    public function deleteKeyFromId($keyId) {
        $sql = "DELETE FROM keys_ WHERE id = :keyId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':keyId', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function getUnacquiredKeysForGame($gameId) {
        $sql = "SELECT * FROM keys_ WHERE game_id = :gameId AND acquired = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUnacquiredKeysForGameWithQuantity($gameId, $quantity) {
        $sql = "SELECT * FROM keys_ WHERE game_id = :gameId AND acquired = 0 LIMIT :quantity";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllKeysForGame($gameId) {
        $sql = "SELECT * FROM keys_ WHERE game_id = :gameId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':gameId', $gameId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllKeys() {
        $sql = "SELECT * FROM keys_";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}



?>