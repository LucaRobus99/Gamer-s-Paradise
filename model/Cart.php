<?php

require_once '../util/database.php';

class Cart
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }
    public function addToCart($gameId, $userId)
    {
        $gameId = $this->conn->quote($gameId);
        $userId = $this->conn->quote($userId);

        $query = "INSERT INTO carts (game_id, user_id, quantity) VALUES ($gameId, $userId, 1)";
        $result = $this->conn->exec($query);

        return $result !== false;
    }

    public function getCartItems($userId)
    {
        $userId = $this->conn->quote($userId);

        $query = "SELECT * FROM carts WHERE user_id = $userId";
        $result = $this->conn->query($query);

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuantity($gameId, $userId)
    {
        $gameId = $this->conn->quote($gameId);
        $userId = $this->conn->quote($userId);

        $query = "SELECT quantity FROM carts WHERE game_id = $gameId AND user_id = $userId";
        $result = $this->conn->query($query);

        $quantity = $result->fetchColumn();
        return $quantity !== false ? $quantity : 0;
    }

    public function setQuantity($gameId, $userId, $quantity)
    {
        $gameId = $this->conn->quote($gameId);
        $userId = $this->conn->quote($userId);
        $quantity = $this->conn->quote($quantity);

        $query = "UPDATE carts SET quantity = $quantity WHERE game_id = $gameId AND user_id = $userId";
        $result = $this->conn->exec($query);

        return $result !== false;
    }

    public function gameExistsInCart($gameId, $userId)
    {
        $gameId = $this->conn->quote($gameId);
        $userId = $this->conn->quote($userId);

        $query = "SELECT COUNT(*) FROM carts WHERE game_id = $gameId AND user_id = $userId";
        $result = $this->conn->query($query);

        $count = $result->fetchColumn();
        return $count > 0;
    }



    public function removeGameFromCart($gameId, $userId)
    {
        $gameId = $this->conn->quote($gameId);
        $userId = $this->conn->quote($userId);

        $query = "DELETE FROM carts WHERE game_id = $gameId AND user_id = $userId";
        $result = $this->conn->exec($query);

        return $result !== false;
    }

    public function deleteCartFromUserId($userId)
    {
        $userId = $this->conn->quote($userId);

        $query = "DELETE FROM carts WHERE user_id = $userId";
        $result = $this->conn->exec($query);

        return $result !== false;
    }



}








