<?php
require_once '../app/models/postmodel.php';

class PostController {
    private $db;
    private $postModel;

    public function __construct($db) {
        $this->db = $db;
        $this->postModel = new PostModel($db);
    }

    private function uploadErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File is too large.';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder on server.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by PHP extension.';
            default:
                return 'Unknown upload error.';
        }
    }

    // Handle viewing the Newsfeed
    public function index() {
        $posts = $this->postModel->getAllPosts();
        // make DB available to view
        $db = $this->db;
        require_once '../app/views/posts/newsfeed.php';
    }

    // Handle creating a post
    public function store() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $user_id = $_SESSION['user_id'];
            $price = isset($_POST['price']) && $_POST['price'] !== '' ? $_POST['price'] : null;
            $currency = isset($_POST['currency']) && $_POST['currency'] !== '' ? $_POST['currency'] : null;

            // Handle multiple image uploads (images[])
            $imageNames = [];
            if (isset($_FILES['images'])) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $files = $_FILES['images'];
                $allowed = ['png','jpg','jpeg','gif'];
                $maxSize = 4 * 1024 * 1024; // 4MB each
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                        $_SESSION['flash_error'] = 'Image upload error: ' . $this->uploadErrorMessage($files['error'][$i]);
                        header("Location: ?url=newsfeed"); exit();
                    }
                    if ($files['size'][$i] > $maxSize) { $_SESSION['flash_error'] = 'One of the images is too large (max 4MB).'; header("Location: ?url=newsfeed"); exit(); }
                    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed)) { $_SESSION['flash_error'] = 'Invalid image type.'; header("Location: ?url=newsfeed"); exit(); }
                    $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    if (!move_uploaded_file($files['tmp_name'][$i], $uploadDir . $newName)) { $_SESSION['flash_error'] = 'Failed to save an uploaded image.'; header("Location: ?url=newsfeed"); exit(); }
                    $imageNames[] = $newName;
                }
            }

            // Handle multiple video uploads (videos[])
            $videoNames = [];
            if (isset($_FILES['videos'])) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $files = $_FILES['videos'];
                $allowedV = ['mp4','webm','ogg'];
                $maxV = 20 * 1024 * 1024; // 20MB each
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                        $_SESSION['flash_error'] = 'Video upload error: ' . $this->uploadErrorMessage($files['error'][$i]);
                        header("Location: ?url=newsfeed"); exit();
                    }
                    if ($files['size'][$i] > $maxV) { $_SESSION['flash_error'] = 'One of the videos is too large (max 20MB).'; header("Location: ?url=newsfeed"); exit(); }
                    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedV)) { $_SESSION['flash_error'] = 'Invalid video type.'; header("Location: ?url=newsfeed"); exit(); }
                    $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    if (!move_uploaded_file($files['tmp_name'][$i], $uploadDir . $newName)) { $_SESSION['flash_error'] = 'Failed to save an uploaded video.'; header("Location: ?url=newsfeed"); exit(); }
                    $videoNames[] = $newName;
                }
            }
            if (trim($content) !== '' || !empty($imageNames) || $price !== null || !empty($videoNames)) {
                $this->postModel->createPost($user_id, $content, $imageNames, $price, $videoNames, $currency);
            }
            header("Location: ?url=newsfeed");
        }
    }

    // Show edit form for a post
    public function edit() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $post = $this->postModel->getPostById($id);
        if (!$post) { header('Location: ?url=newsfeed'); exit(); }
        if ($post['user_id'] != $_SESSION['user_id']) { header('Location: ?url=newsfeed'); exit(); }
        $db = $this->db;
        require_once '../app/views/posts/edit.php';
    }

    // Handle post update
    public function update() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?url=newsfeed'); exit(); }
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $post = $this->postModel->getPostById($id);
        if (!$post || $post['user_id'] != $_SESSION['user_id']) { header('Location: ?url=newsfeed'); exit(); }

        $fields = [];
        $fields['content'] = isset($_POST['content']) ? trim($_POST['content']) : null;
        $fields['price'] = isset($_POST['price']) && $_POST['price'] !== '' ? $_POST['price'] : null;
        if ($fields['price'] !== null) {
            $fields['currency'] = isset($_POST['currency']) && $_POST['currency'] !== '' ? $_POST['currency'] : null;
        } else {
            $fields['currency'] = null;
        }

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['png','jpg','jpeg','gif'];
            if (in_array($ext, $allowed)) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) $fields['image'] = $newName;
            }
        }

        // Handle explicit remove image (if no new upload provided)
        if (empty($fields['image']) && isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            // delete existing file
            if (!empty($post['image'])) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';
                $path = $uploadDir . $post['image'];
                if (is_file($path)) @unlink($path);
            }
            // set to NULL so DB column will be cleared
            $fields['image'] = null;
        }

        // Handle new video upload
        if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['video'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['mp4','webm','ogg'];
            if (in_array($ext, $allowed)) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) $fields['video'] = $newName;
            }
        }

        // Handle explicit remove video (if no new upload provided)
        if (empty($fields['video']) && isset($_POST['remove_video']) && $_POST['remove_video'] == '1') {
            if (!empty($post['video'])) {
                $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';
                $path = $uploadDir . $post['video'];
                if (is_file($path)) @unlink($path);
            }
            $fields['video'] = null;
        }

        // Clean and update
        // include NULLs (explicit removal) but skip empty strings
        $clean = [];
        foreach ($fields as $k => $v) if ($v !== '') $clean[$k] = $v;
        if (!empty($clean)) $this->postModel->updatePost($id, $clean);
        header('Location: ?url=newsfeed');
    }

    // Delete a post
    public function delete() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $post = $this->postModel->getPostById($id);
            if ($post && $post['user_id'] == $_SESSION['user_id']) {
                $this->postModel->deletePost($id);
            }
        }
        header('Location: ?url=newsfeed');
    }

    // Share a post (duplicate as share)
    public function share() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login"); exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            // find original post
            $all = $this->postModel->getAllPosts();
            $orig = null;
            foreach ($all as $p) { if ($p['id'] == $post_id) { $orig = $p; break; } }
            if ($orig) {
                $shared_post_id = !empty($orig['shared_post_id']) ? $orig['shared_post_id'] : $orig['id'];
                $user_id = $_SESSION['user_id'];
                $content = isset($_POST['share_content']) ? trim($_POST['share_content']) : '';
                
                $this->postModel->createPost($user_id, $content, [], null, [], null, $shared_post_id);
            }
        }
        header("Location: ?url=newsfeed");
    }
}
?>