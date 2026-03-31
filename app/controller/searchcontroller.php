<?php
require_once '../app/models/UserModel.php';
require_once '../app/models/PostModel.php';

class SearchController {
    private $db;
    private $userModel;
    private $postModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new UserModel($db);
        $this->postModel = new PostModel($db);
    }

    public function index() {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $users = [];
        $posts = [];
        if ($q !== '') {
            // search users by username or full_name
            $stmt = $this->db->prepare("SELECT id, username, full_name FROM users WHERE username LIKE :q OR full_name LIKE :q LIMIT 50");
            $like = '%' . $q . '%';
            $stmt->bindParam(':q', $like);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // search posts by content
            $stmt2 = $this->db->prepare("SELECT p.*, u.username, u.full_name FROM posts p JOIN users u ON p.user_id = u.id WHERE p.content LIKE :q ORDER BY p.created_at DESC LIMIT 50");
            $stmt2->bindParam(':q', $like);
            $stmt2->execute();
            $posts = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        require_once '../app/views/search/results.php';
    }
}
?>