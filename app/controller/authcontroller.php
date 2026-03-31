<?php
require_once '../app/models/userModel.php';

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    public function register() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postStep = isset($_POST['step']) ? (int)$_POST['step'] : 1;
            // initialize signup storage
            if (!isset($_SESSION['signup'])) $_SESSION['signup'] = [];

            if ($postStep === 1) {
                // store basic account info and go to step 2
                $_SESSION['signup']['full_name'] = $_POST['full_name'] ?? '';
                $_SESSION['signup']['username'] = $_POST['username'] ?? '';
                $_SESSION['signup']['password'] = $_POST['password'] ?? '';
                header('Location: ?url=register&step=2');
                exit;
            }

            if ($postStep === 2) {
                // personal info
                $fields = [
                    'street','barangay','city','province','country','postal_code','phone','birthdate'
                ];
                foreach ($fields as $f) {
                    $_SESSION['signup'][$f] = $_POST[$f] ?? '';
                }
                header('Location: ?url=register&step=3');
                exit;
            }

            if ($postStep === 3) {
                // recovery questions - expect arrays question[] and answer[]
                $questions = $_POST['question'] ?? [];
                $answers = $_POST['answer'] ?? [];
                $qa = [];
                for ($i = 0; $i < count($questions); $i++) {
                    $q = trim($questions[$i]);
                    $a = trim($answers[$i] ?? '');
                    if ($q === '' || $a === '') continue;
                    $qa[] = ['question' => $q, 'answer' => $a];
                }

                if (count($qa) < 3) {
                    $error = 'Please provide at least 3 recovery question & answer pairs.';
                    require_once '../app/views/auth/register_step3.php';
                    return;
                }

                // create user
                $s = $_SESSION['signup'];
                $newId = $this->userModel->register($s['username'], $s['password'], $s['full_name']);
                if ($newId) {
                    // update optional fields
                    $updateFields = [];
                    $map = ['street','barangay','city','province','country','postal_code','phone','birthdate'];
                    foreach ($map as $m) {
                        if (!empty($s[$m])) $updateFields[$m] = $s[$m];
                    }
                    if (!empty($updateFields)) {
                        $this->userModel->updateById($newId, $updateFields);
                    }
                    // save recovery questions (if table exists)
                    $this->userModel->saveRecoveryQuestions($newId, $qa);
                    unset($_SESSION['signup']);
                    header('Location: ?url=login&msg=registered');
                    exit;
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }

        // show appropriate step view
        if ($step === 2) {
            require_once '../app/views/auth/register_step2.php';
            return;
        }
        if ($step === 3) {
            require_once '../app/views/auth/register_step3.php';
            return;
        }

        require_once '../app/views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ?url=newsfeed");
            } else {
                // Distinguish failed password when username exists
                if ($user) {
                    $showForgot = true;
                    $error = "Invalid password.";
                } else {
                    $error = "Invalid username or password.";
                }
            }
        }
        require_once '../app/views/auth/login.php';
    }

    public function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_destroy();
        header("Location: ?url=login");
    }
}