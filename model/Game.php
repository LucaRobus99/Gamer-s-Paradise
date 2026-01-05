<?php

require_once '../util/database.php';

class Game
{
    private $db;
    private $conn ;

    public function __construct()
    {
        $this->db=DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function getAllGames()
    {


        $query = "SELECT * FROM games";
        $stmt = $this->conn ->prepare($query);
        $stmt->execute();
        $gamesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $gamesList;
    }

    public function getGameById($gameId)
    {
        $query = "SELECT * FROM games WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $gameId);
        $stmt->execute();
        $game = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($game) {
            return $game; // Restituisce il gioco se trovato
        } else {
            return false; // Restituisce false se non trovato
        }
    }

    public function addGame($title, $platform, $genre, $price, $description, $cover)
    {
        $query = "INSERT INTO games (title, platform, genre, price, description, cover) VALUES (:title, :platform, :genre, :price, :description, :cover)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':platform', $platform);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':cover', $cover);
        $result = $stmt->execute();

        // Check if the insertion was successful
        if ($result) {
            // Get the ID of the last inserted record
            $lastInsertedId = $this->conn->lastInsertId();

            // Use the getGameById function to retrieve the game information
            $lastInsertedGame = $this->getGameById($lastInsertedId);

            return $lastInsertedGame; // Return the last inserted game as an associative array
        } else {
            return false; // Return null if the insertion failed
        }
    }





    public function deleteGame($gameId)
    {


        $query = "DELETE FROM games WHERE id = :id";
        $stmt = $this->conn ->prepare($query);
        $stmt->bindParam(':id', $gameId);
        $result = $stmt->execute();

        return $result;
    }

    public function getGamesByPlatform($platform)
    {
        $query = "SELECT * FROM games WHERE platform LIKE :platformPattern";
        $stmt = $this->conn->prepare($query);

        // Aggiungiamo il carattere jolly % per cercare giochi che iniziano con la piattaforma specificata
        $platformPattern = $platform . '%';

        $stmt->bindParam(':platformPattern', $platformPattern);
        $stmt->execute();
        $gamesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($gamesList)) {
            // Se l'array dei giochi è vuoto, restituisci un array vuoto
            return array();
        } else {
            return $gamesList;
        }
    }

    public function getGamesByFilters($platform, $order, $genre)
    {
        if (is_array($platform)) {
            if (count($platform) === 1) {
                $query = "SELECT * FROM games WHERE platform = :platform";
            } elseif (count($platform) >= 2) {
                $query = "SELECT * FROM games WHERE platform IN (:platform1, :platform2)";
            }
        } else {
            $query = "SELECT * FROM games WHERE platform = :platform";
        }

        if (!empty($genre)) {
            $query .= " AND genre = :genre";
        }

        switch ($order) {
            case "Crescente":
                $query .= " ORDER BY price ASC";
                break;
            case "Decrescente":
                $query .= " ORDER BY price DESC";
                break;
            case "Alfabetico":
                $query .= " ORDER BY title ASC";
                break;
            // No default ordering in the original query
        }

        $stmt = $this->conn->prepare($query);

        if (is_array($platform)) {
            if (count($platform) === 1) {
                $stmt->bindParam(':platform', $platform[0], PDO::PARAM_STR);
            } elseif (count($platform) >= 2) {
                $stmt->bindParam(':platform1', $platform[0], PDO::PARAM_STR);
                $stmt->bindParam(':platform2', $platform[1], PDO::PARAM_STR);
            }
        } else {
            $stmt->bindParam(':platform', $platform, PDO::PARAM_STR);
        }

        if (!empty($genre)) {
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
        }

        $stmt->execute();
        $gamesList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $gamesList;
    }

    public function gameExistsByTitleAndPlatform($title, $platform)
    {
        $query = "SELECT COUNT(*) as count FROM games WHERE title = :title AND platform = :platform";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':platform', $platform, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Restituisci true se esiste almeno un gioco con il titolo e la piattaforma specificati, altrimenti false
        return ($result['count'] > 0);
    }




    public function getLatestGames($limit = 10)
    {
        $query = "SELECT * FROM games ORDER BY id DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $latestGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $latestGames;
    }



}

?>
