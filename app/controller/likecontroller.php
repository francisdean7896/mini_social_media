<?php
require_once '../app/models/likemodel.php';

class LikeController {
    private $db;
    private $likeModel;

    public function __construct($db) {
        $this->db = $db;
        $this->likeModel = new LikeModel($db);
    }

    public function toggle() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $user_id = $_SESSION['user_id'];
            $this->likeModel->toggleLike($post_id, $user_id);
        }
        header('Location: ?url=newsfeed');
    }
}
?>