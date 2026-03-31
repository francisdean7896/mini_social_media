<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register a new user
    public function register($username, $password, $full_name) {
        $query = "INSERT INTO " . $this->table_name . " (username, password, full_name) VALUES (:username, :password, :full_name)";
        $stmt = $this->conn->prepare($query);

        // Hash the password before saving
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", htmlspecialchars(strip_tags($username)));
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":full_name", htmlspecialchars(strip_tags($full_name)));

        $ok = $stmt->execute();
        if ($ok) {
            // return the new user id
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Save recovery questions (expects array of ['question' => '', 'answer' => ''])
    public function saveRecoveryQuestions($userId, $qa = []) {
        if (empty($qa)) return true;
        try {
            $sql = "INSERT INTO recovery_questions (user_id, question, answer, created_at) VALUES (:uid, :q, :a, NOW())";
            $stmt = $this->conn->prepare($sql);
            foreach ($qa as $pair) {
                if (empty($pair['question']) || empty($pair['answer'])) continue;
                $stmt->bindValue(':uid', $userId);
                $stmt->bindValue(':q', htmlspecialchars(strip_tags($pair['question'])));
                $stmt->bindValue(':a', htmlspecialchars(strip_tags($pair['answer'])));
                $stmt->execute();
            }
            return true;
        } catch (Exception $e) {
            // Table may not exist yet; skip saving but do not fail registration
            return false;
        }
    }

    // Get recovery questions for a user
    public function getRecoveryQuestions($userId) {
        try {
            $sql = "SELECT id, question, answer FROM recovery_questions WHERE user_id = :uid ORDER BY id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':uid', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Update user password
    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE " . $this->table_name . " SET password = :p WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':p', $hashed);
        $stmt->bindValue(':id', $userId);
        return $stmt->execute();
    }

    // Find user by username for login
    public function findByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find user by id
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user by id with provided fields (associative array)
    public function updateById($id, $fields = []) {
        if (empty($fields)) return false;

        // Allow only certain fields to be updated
        $allowed = [
            'full_name','bio','address','avatar','username',
            'street','barangay','city','province','country','postal_code',
            'phone','birthdate'
        ];
        $setParts = [];
        $params = [];

        foreach ($fields as $key => $value) {
            if (!in_array($key, $allowed)) continue;
            $setParts[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        if (empty($setParts)) return false;

        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $pkey => $pval) {
            $stmt->bindValue($pkey, $pval);
        }
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>