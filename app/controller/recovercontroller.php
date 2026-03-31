<?php
require_once '../app/models/userModel.php';

class RecoverController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    public function index() {
        // show a simple lookup form or questions depending on input
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $step = $_GET['step'] ?? 'lookup';
        $username = $_REQUEST['username'] ?? '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'lookup';
            if ($action === 'lookup') {
                $username = $_POST['username'] ?? '';
                $user = $this->userModel->findByUsername($username);
                if (!$user) {
                    $error = 'User not found.';
                    require_once '../app/views/auth/recover.php';
                    return;
                }
                // load questions
                $questions = $this->userModel->getRecoveryQuestions($user['id']);
                if (empty($questions)) {
                    $error = 'No recovery questions set for this account.';
                    require_once '../app/views/auth/recover.php';
                    return;
                }
                require_once '../app/views/auth/recover.php';
                return;
            }

            if ($action === 'verify') {
                $username = $_POST['username'] ?? '';
                $user = $this->userModel->findByUsername($username);
                if (!$user) { $error = 'User not found.'; require_once '../app/views/auth/recover.php'; return; }

                $questions = $this->userModel->getRecoveryQuestions($user['id']);
                $ok = true;
                foreach ($questions as $idx => $q) {
                    $ans = trim($_POST['answer'][$idx] ?? '');
                    if (strcasecmp($ans, trim($q['answer'])) !== 0) {
                        $ok = false; break;
                    }
                }
                if ($ok) {
                    // allow reset
                    $_SESSION['recover_user'] = $user['id'];
                    require_once '../app/views/auth/reset_password.php';
                    return;
                } else {
                    $error = 'Answers did not match. Access denied.';
                    require_once '../app/views/auth/recover.php';
                    return;
                }
            }

            if ($action === 'reset') {
                $userId = $_SESSION['recover_user'] ?? null;
                if (!$userId) { $error = 'No recovery session found.'; require_once '../app/views/auth/recover.php'; return; }
                $pw = $_POST['password'] ?? '';
                $pw2 = $_POST['password_confirm'] ?? '';
                if ($pw === '' || $pw !== $pw2) { $error = 'Passwords do not match.'; require_once '../app/views/auth/reset_password.php'; return; }
                $this->userModel->updatePassword($userId, $pw);
                unset($_SESSION['recover_user']);
                header('Location: ?url=login&msg=password_reset');
                return;
            }
        }

        // default: show lookup form
        require_once '../app/views/auth/recover.php';
    }
}
