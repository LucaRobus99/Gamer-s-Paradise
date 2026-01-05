<?php


require_once '../util/database.php';

class User
{

    private $db;
    private $conn ;

    public function __construct()
    {
        $this->db=DatabaseConnection::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function userExists($email)
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function register($email, $first_name, $last_name, $rawPassword)
    {
        // Verifica se l'utente esiste già
        if ($this->userExists($email)) {
            return false; // L'utente esiste già, la registrazione non è possibile
        }

        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (email, first_name, last_name, password, role) VALUES (:email, :first_name, :last_name, :password, :role)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindValue(':role', 0);

        return $stmt->execute();
    }


    public function login($email, $rawPassword)
    {

       

        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false; // User not found
        }

        $hashedPassword = $user['password'];
        if (password_verify($rawPassword, $hashedPassword)) {
            return $user; // Return user data on successful login
        }

        return false; // Incorrect password
    }

    public function setEmail($userId, $newEmail)
    {
        $query = "UPDATE users SET email = :newEmail WHERE id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':newEmail', $newEmail);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    // Setter method for setting the password
    public function setPassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :hashedPassword WHERE id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hashedPassword', $hashedPassword);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    // Setter method for setting the first name
    public function setFirstName($userId, $newFirstName)
    {
        $query = "UPDATE users SET first_name = :newFirstName WHERE id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':newFirstName', $newFirstName);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    // Setter method for setting the last name
    public function setLastName($userId, $newLastName)
    {
        $query = "UPDATE users SET last_name = :newLastName WHERE id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':newLastName', $newLastName);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }
    public function getUserById($userId)
    {
        $query = "SELECT * FROM users WHERE id = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}


?>
