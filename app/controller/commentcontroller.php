<?php
require_once '../app/models/commentmodel.php';

class CommentController {
    private $db;
    private $commentModel;

    public function __construct($db) {
        $this->db = $db;
        $this->commentModel = new CommentModel($db);
    }

    public function store() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $content = isset($_POST['comment']) ? $_POST['comment'] : '';
            $user_id = $_SESSION['user_id'];
            if (trim($content) !== '') {
                $this->commentModel->createComment($post_id, $user_id, $content);
            }
        }
        header('Location: ?url=newsfeed');
    }

    public function edit() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $c = $this->commentModel->getCommentById($id);
        if (!$c || $c['user_id'] != $_SESSION['user_id']) { header('Location: ?url=newsfeed'); exit(); }
        $comment = $c;
        require_once '../app/views/comments/edit.php';
    }

    public function update() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?url=newsfeed'); exit(); }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $c = $this->commentModel->getCommentById($id);
        if (!$c || $c['user_id'] != $_SESSION['user_id']) { header('Location: ?url=newsfeed'); exit(); }
        $content = isset($_POST['comment']) ? $_POST['comment'] : '';
        if (trim($content) !== '') $this->commentModel->updateComment($id, $content);
        header('Location: ?url=newsfeed');
    }

    public function delete() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $c = $this->commentModel->getCommentById($id);
            if ($c && $c['user_id'] == $_SESSION['user_id']) $this->commentModel->deleteComment($id);
        }
        header('Location: ?url=newsfeed');
    }
}
?>
