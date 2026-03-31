<?php
class CommentModel {
    private $conn;
    private $table_name = 'comments';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createComment($post_id, $user_id, $content) {
        $q = "INSERT INTO " . $this->table_name . " (post_id, user_id, content) VALUES (:post_id, :user_id, :content)";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $clean = htmlspecialchars(strip_tags($content));
        $stmt->bindParam(':content', $clean);
        return $stmt->execute();
    }

    public function getCommentsByPost($post_id) {
        $q = "SELECT c.*, u.username, u.full_name FROM " . $this->table_name . " c JOIN users u ON c.user_id = u.id WHERE c.post_id = :post_id ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommentById($id) {
        $q = "SELECT c.*, u.username, u.full_name FROM " . $this->table_name . " c JOIN users u ON c.user_id = u.id WHERE c.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateComment($id, $content) {
        $q = "UPDATE " . $this->table_name . " SET content = :content WHERE id = :id";
        $stmt = $this->conn->prepare($q);
        $clean = htmlspecialchars(strip_tags($content));
        $stmt->bindParam(':content', $clean);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteComment($id) {
        $q = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
