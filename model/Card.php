<?php

require_once '../util/database.php';



class Card
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function createCard($cardData)
    {
        $query = "INSERT INTO cards (card_number, card_holder_name, card_holder_lastname, cvv, expiring_date, user_id)
              VALUES (:card_number, :card_holder_name, :card_holder_lastname, :cvv, :expiring_date, :user_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':card_number', $cardData['card_number']);
        $stmt->bindParam(':card_holder_name', $cardData['card_holder_name']);
        $stmt->bindParam(':card_holder_lastname', $cardData['card_holder_lastname']);
        $stmt->bindParam(':cvv', $cardData['cvv']);
        $stmt->bindParam(':expiring_date', $cardData['expiring_date']);
        $stmt->bindParam(':user_id', $cardData['user_id']);

        // Esegui l'istruzione SQL
        if ($stmt->execute()) {
            // Ottieni l'ID della carta appena inserita
            $lastInsertId = $this->conn->lastInsertId();

            // Restituisci l'ID come risultato dell'operazione
            return $lastInsertId;
        } else {
            // In caso di errore nell'esecuzione dell'istruzione SQL, restituisci false
            return false;
        }
    }


    public function getCardById($cardId)
    {
        $query = "SELECT * FROM cards WHERE id = :card_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':card_id', $cardId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCard($cardId, $cardData)
    {
        $query = "UPDATE cards SET card_number = :card_number, card_holder_name = :card_holder_name,
                  card_holder_lastname = :card_holder_lastname, cvv = :cvv, expiring_date = :expiring_date
                  WHERE id = :card_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':card_number', $cardData['card_number']);
        $stmt->bindParam(':card_holder_name', $cardData['card_holder_name']);
        $stmt->bindParam(':card_holder_lastname', $cardData['card_holder_lastname']);
        $stmt->bindParam(':cvv', $cardData['cvv']);
        $stmt->bindParam(':expiring_date', $cardData['expiring_date']);
        $stmt->bindParam(':card_id', $cardId);

        return $stmt->execute();
    }

    public function deleteCard($cardId, $userId)
    {
        $query = "DELETE FROM cards WHERE id = :card_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':card_id', $cardId);
        $stmt->bindParam(':user_id', $userId);

        return $stmt->execute();
    }


    public function getCardsByUserId($userId)
    {
        $query = "SELECT * FROM cards WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}



?>
