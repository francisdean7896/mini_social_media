<?php
require_once '../app/models/friendmodel.php';

class FriendController {
    private $db;
    private $fm;

    public function __construct($db) {
        $this->db = $db;
        $this->fm = new FriendModel($db);
    }

    public function send() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        $to = isset($_POST['to']) ? intval($_POST['to']) : 0;
        if ($to && $to !== $_SESSION['user_id']) {
            $result = $this->fm->sendRequest($_SESSION['user_id'], $to);
            if ($result === true) {
                $_SESSION['flash'] = 'Friend request sent.';
            } elseif ($result === 'exists') {
                $_SESSION['flash'] = 'Friend request already exists or you are already friends.';
            } else {
                $_SESSION['flash'] = 'Unable to send friend request. Please try again later.';
            }
        }
        header('Location: ?url=profile&id=' . $to);
    }

    public function accept() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        $from = isset($_POST['from']) ? intval($_POST['from']) : 0;
        if ($from) {
            // current user is addressee
            $this->fm->acceptRequest($from, $_SESSION['user_id']);
        }
        header('Location: ?url=friends&tab=requests');
    }

    public function remove() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }
        $other = isset($_POST['other']) ? intval($_POST['other']) : 0;
        if ($other) {
            $this->fm->removeFriendship($_SESSION['user_id'], $other);
        }
        header('Location: ?url=friends&tab=list');
    }

    // Show friends page with tabs for list and requests
    public function index() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ?url=login'); exit(); }

        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'list';
        $userId = $_SESSION['user_id'];
        $friends = $this->fm->getFriends($userId);
        $received = $this->fm->getReceivedRequests($userId);
        $sent = $this->fm->getSentRequests($userId);

        require_once '../app/views/friends/index.php';
    }
}

?>
