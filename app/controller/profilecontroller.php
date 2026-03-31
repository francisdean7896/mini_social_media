<?php
require_once '../app/models/UserModel.php';
require_once '../app/models/PostModel.php';
// friend model removed from profile controller — friend features disabled

class ProfileController {
    private $db;
    private $userModel;
    private $postModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new UserModel($db);
        $this->postModel = new PostModel($db);
    }

    // Show profile for the logged in user
    public function index() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login");
            exit();
        }

        // allow viewing other users' profiles via ?id=NN
        $user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
        $user = $this->userModel->findById($user_id);
        $posts = $this->postModel->getPostsByUser($user_id);

        // friend features disabled — no friend status provided
        $friendStatus = null;

        // provide DB to the view for like counting and other models
        $db = $this->db;
        require_once '../app/views/profile/profile.php';
    }

    // Show edit form
    public function edit() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->findById($user_id);
        require_once '../app/views/profile/edit.php';
    }

    // Handle profile update (including avatar upload)
    public function update() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fields = [];
            $fields['username'] = isset($_POST['username']) ? trim($_POST['username']) : null;
            
            // Check if username is already taken
            if (!empty($fields['username'])) {
                $existingUser = $this->userModel->findByUsername($fields['username']);
                if ($existingUser && $existingUser['id'] != $user_id) {
                    header("Location: ?url=profile/edit&error=username_taken");
                    exit();
                }
            }

            $fields['full_name'] = isset($_POST['full_name']) ? trim($_POST['full_name']) : null;
            $fields['bio'] = isset($_POST['bio']) ? trim($_POST['bio']) : null;
            // Address components
            $fields['street'] = isset($_POST['street']) ? trim($_POST['street']) : null;
            $fields['barangay'] = isset($_POST['barangay']) ? trim($_POST['barangay']) : null;
            $fields['city'] = isset($_POST['city']) ? trim($_POST['city']) : null;
            $fields['province'] = isset($_POST['province']) ? trim($_POST['province']) : null;
            $fields['country'] = isset($_POST['country']) ? trim($_POST['country']) : null;
            $fields['postal_code'] = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : null;
            $fields['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : null;
            $fields['birthdate'] = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : null;

            // Handle avatar upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                $allowed = ['png','jpg','jpeg','gif'];

                if ($file['size'] <= $maxSize) {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, $allowed)) {
                        $uploadDir = __DIR__ . '/../../public/assets/uploads/avatars/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $newName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                        $dest = $uploadDir . $newName;
                        if (move_uploaded_file($file['tmp_name'], $dest)) {
                            // store relative filename
                            $fields['avatar'] = $newName;
                        }
                    }
                }
            }

            // Remove null fields so updateById doesn't try to set them as empty
            $clean = [];
            foreach ($fields as $k => $v) {
                if ($v !== null && $v !== '') $clean[$k] = $v;
            }

            if (!empty($clean)) {
                $this->userModel->updateById($user_id, $clean);
                if (isset($clean['username'])) {
                    $_SESSION['username'] = $clean['username'];
                }
            }
        }

        header("Location: ?url=profile");
        exit();
    }
}
?>
