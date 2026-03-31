<?php
// 1. Basic Configuration & Session Start
session_start();
require_once '../app/config/database.php';
require_once '../app/controller/authcontroller.php';
require_once '../app/controller/postcontroller.php';
require_once '../app/controller/commentcontroller.php';
require_once '../app/controller/likecontroller.php';
require_once '../app/controller/profilecontroller.php';
require_once '../app/controller/searchcontroller.php';
require_once '../app/controller/profilecontroller.php';
require_once '../app/controller/recovercontroller.php';

// 2. Initialize Database
$database = new Database();
$db = $database->getConnection();

// 3. Simple Routing Logic
// We get the URL path (e.g., /login or /newsfeed)
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'login';

// 4. Route Dispatcher
// DEBUG: log every request's URL, session and GET for troubleshooting
@file_put_contents(__DIR__ . '/../debug.log', "REQUEST: " . date('c') . "\nURL=" . ($_GET['url'] ?? '') . "\nSESSION=" . print_r(isset($_SESSION) ? $_SESSION : [], true) . "GET=" . print_r($_GET, true) . "COOKIE=" . print_r($_COOKIE, true) . "\n---\n", FILE_APPEND);

switch ($url) {
    case 'login':
        $auth = new AuthController($db);
        $auth->login();
        break;

    case 'register':
        $auth = new AuthController($db);
        $auth->register();
        break;

    case 'logout':
        $auth = new AuthController($db);
        $auth->logout();
        break;

    case 'newsfeed':
        // Protected Route: Redirect to login if not logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login");
            exit();
        }
        $posts = new PostController($db);
        $posts->index();
        break;

    case 'profile':
        // Protected: show profile of logged in user
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?url=login");
            exit();
        }
        $profile = new ProfileController($db);
        $profile->index();
        break;

    case 'recover':
        $r = new RecoverController($db);
        $r->index();
        break;

    case 'posts/store':
        $posts = new PostController($db);
        $posts->store();
        break;

    case 'posts/share':
        $posts = new PostController($db);
        $posts->share();
        break;

    case 'posts/edit':
        $posts = new PostController($db);
        $posts->edit();
        break;

    case 'posts/update':
        $posts = new PostController($db);
        $posts->update();
        break;

    case 'posts/delete':
        $posts = new PostController($db);
        $posts->delete();
        break;

    case 'comments/store':
        $comments = new CommentController($db);
        $comments->store();
        break;

    case 'comments/edit':
        $comments = new CommentController($db);
        $comments->edit();
        break;

    case 'comments/update':
        $comments = new CommentController($db);
        $comments->update();
        break;

    case 'comments/delete':
        $comments = new CommentController($db);
        $comments->delete();
        break;

    case 'likes/toggle':
        $likes = new LikeController($db);
        $likes->toggle();
        break;

    case 'search':
        $s = new SearchController($db);
        $s->index();
        break;

    /* Friend-related routes removed */

    case 'profile/edit':
        $profile = new ProfileController($db);
        $profile->edit();
        break;

    case 'profile/update':
        $profile = new ProfileController($db);
        $profile->update();
        break;

    default:
        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}