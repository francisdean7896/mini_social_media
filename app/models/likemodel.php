<?php
class LikeModel {
    private $conn;
    private $table_name = 'likes';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Toggle like: return true if liked, false if unliked
    public function toggleLike($post_id, $user_id) {
        // check existing
        $query = "SELECT id FROM " . $this->table_name . " WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $del = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
            $del->bindParam(':id', $row['id']);
            $del->execute();
            return false;
        } else {
            $ins = $this->conn->prepare("INSERT INTO " . $this->table_name . " (post_id, user_id) VALUES (:post_id, :user_id)");
            $ins->bindParam(':post_id', $post_id);
            $ins->bindParam(':user_id', $user_id);
            $ins->execute();
            return true;
        }
    }

    public function countLikes($post_id) {
        $q = $this->conn->prepare("SELECT COUNT(*) as cnt FROM " . $this->table_name . " WHERE post_id = :post_id");
        $q->bindParam(':post_id', $post_id);
        $q->execute();
        $r = $q->fetch(PDO::FETCH_ASSOC);
        return $r ? intval($r['cnt']) : 0;
    }

    public function userLiked($post_id, $user_id) {
        $q = $this->conn->prepare("SELECT 1 FROM " . $this->table_name . " WHERE post_id = :post_id AND user_id = :user_id");
        $q->bindParam(':post_id', $post_id);
        $q->bindParam(':user_id', $user_id);
        $q->execute();
        return (bool) $q->fetch(PDO::FETCH_ASSOC);
    }
}
?>