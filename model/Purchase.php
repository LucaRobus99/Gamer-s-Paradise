<?php

require_once '../util/database.php';

class Purchase
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function addPurchase($order_date, $game_id, $user_id, $key_id, $card_id)
    {
        // Format the order date as a timestamp string
        $order_date = date("Y-m-d H:i:s", strtotime($order_date));

        // Quote other variables to prevent SQL injection
        $game_id = $this->conn->quote($game_id);
        $user_id = $this->conn->quote($user_id);
        $key_id = $this->conn->quote($key_id);
        $card_id = $this->conn->quote($card_id);

        // Construct the query using placeholders
        $query = "INSERT INTO purchases (order_date, game_id, user_id, key_id, card_id) VALUES (:order_date, $game_id, $user_id, $key_id, $card_id)";

        // Prepare and execute the query
        $statement = $this->conn->prepare($query);
        $statement->bindValue(':order_date', $order_date);

        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }




    public function getPurchasesByUserId($userId)
    {
        $userId = $this->conn->quote($userId);

        $query = "SELECT * FROM purchases WHERE user_id = $userId";
        $result = $this->conn->query($query);

        if ($result) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
    public function getMostSoldGames($limit = 10)
    {
        // Costruisci una query per ottenere gli ID dei videogiochi più venduti
        $query = "
            SELECT game_id, COUNT(game_id) AS total_sales
            FROM purchases
            GROUP BY game_id
            ORDER BY total_sales DESC
            LIMIT :limit
        ";

        // Prepara e esegui la query
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        // Estrai i risultati
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Crea un array per gli ID dei videogiochi più venduti
        $mostSoldGameIds = [];
        foreach ($results as $result) {
            $mostSoldGameIds[] = $result['game_id'];
        }

        return $mostSoldGameIds;
    }
}

?>









