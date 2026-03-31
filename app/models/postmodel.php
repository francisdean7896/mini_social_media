<?php
class PostModel {
    private $conn;
    private $table_name = "posts";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all posts (Newsfeed)
    public function getAllPosts() {
        $query = "SELECT p.*, u.username, u.full_name, u.avatar 
                  FROM " . $this->table_name . " p 
                  JOIN users u ON p.user_id = u.id 
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // attach media for each post (if post_media table exists)
        if (!empty($posts)) {
            foreach ($posts as &$p) {
                $p['media'] = $this->getMediaByPost($p['id']);
                if (!empty($p['shared_post_id'])) {
                    $p['shared_post'] = $this->getPostById($p['shared_post_id'], false);
                }
            }
            unset($p);
        }
        return $posts;
    }

    // Create a Post
    // $images and $videos can be arrays of filenames (multiple media support)
    public function createPost($user_id, $content, $images = [], $price = null, $videos = [], $currency = null, $shared_post_id = null) {
        // Determine which columns actually exist in the table so we don't try to insert unknown columns
        $existing = $this->getTableColumns();
        // Always include user_id and content
        $cols = ['user_id','content'];
        $params = [':user_id' => $user_id, ':content' => htmlspecialchars(strip_tags($content))];
        // legacy single-image/video columns are optional; we won't populate them for multi-media posts
        if ($price !== null && in_array('price', $existing)) {
            $cols[] = 'price';
            $params[':price'] = $price;
        }
        if ($currency !== null && in_array('currency', $existing)) {
            $cols[] = 'currency';
            $params[':currency'] = $currency;
        }
        if ($shared_post_id !== null && in_array('shared_post_id', $existing)) {
            $cols[] = 'shared_post_id';
            $params[':shared_post_id'] = $shared_post_id;
        }


        $placeholders = array_map(function($c){ return ':' . $c; }, $cols);
        $sql = "INSERT INTO " . $this->table_name . " (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $p => $v) {
            $stmt->bindValue($p, $v);
        }
        $ok = $stmt->execute();
        if (!$ok) return false;

        $postId = $this->conn->lastInsertId();

        // insert media entries if provided
        if (!empty($images) && is_array($images)) {
            foreach ($images as $imgFile) {
                $this->insertMedia($postId, $imgFile, 'image');
            }
        }
        if (!empty($videos) && is_array($videos)) {
            foreach ($videos as $vidFile) {
                $this->insertMedia($postId, $vidFile, 'video');
            }
        }

        return true;
    }

    // Insert a media row for a post (post_media table)
    public function insertMedia($postId, $filename, $type = 'image') {
        try {
            $sql = "INSERT INTO post_media (post_id, filename, media_type, created_at) VALUES (:pid, :fn, :mt, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':pid', $postId);
            $stmt->bindValue(':fn', $filename);
            $stmt->bindValue(':mt', $type);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    // Get media rows for a post
    public function getMediaByPost($postId) {
        try {
            $sql = "SELECT filename, media_type FROM post_media WHERE post_id = :pid ORDER BY id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':pid', $postId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Helper: get table columns (names) for this table
    private function getTableColumns() {
        try {
            $stmt = $this->conn->prepare("DESCRIBE " . $this->table_name);
            $stmt->execute();
            $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            return $cols ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    // Get posts by a specific user
    public function getPostsByUser($user_id) {
        $query = "SELECT p.*, u.username, u.full_name, u.avatar 
                  FROM " . $this->table_name . " p 
                  JOIN users u ON p.user_id = u.id 
                  WHERE p.user_id = :user_id 
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($posts)) {
            foreach ($posts as &$p) {
                $p['media'] = $this->getMediaByPost($p['id']);
                if (!empty($p['shared_post_id'])) {
                    $p['shared_post'] = $this->getPostById($p['shared_post_id'], false);
                }
            }
            unset($p);
        }
        return $posts;
    }

    // Get single post by id
    public function getPostById($id, $fetchShared = true) {
        $q = "SELECT p.*, u.username, u.full_name, u.avatar FROM " . $this->table_name . " p JOIN users u ON p.user_id = u.id WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($post) {
            $post['media'] = $this->getMediaByPost($post['id']);
            if ($fetchShared && !empty($post['shared_post_id'])) {
                $post['shared_post'] = $this->getPostById($post['shared_post_id'], false);
            }
        }
        return $post;
    }

    // Update a post (only content, image, price, video)
    public function updatePost($id, $fields = []) {
        if (empty($fields)) return false;
        $allowed = ['content','image','price','video','currency'];
        $set = [];
        $params = [];
        foreach ($fields as $k => $v) {
            if (!in_array($k, $allowed)) continue;
            $set[] = "`$k` = :$k";
            $params[":$k"] = $v;
        }
        if (empty($set)) return false;
        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $set) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $p => $val) $stmt->bindValue($p, $val);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    // Delete a post
    public function deletePost($id) {
        $q = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($q);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>