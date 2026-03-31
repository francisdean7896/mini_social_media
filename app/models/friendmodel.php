<?php
class FriendModel {
    private $conn;
    private $table = 'friendships';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function sendRequest($fromId, $toId) {
        try {
            // Prevent duplicate requests or creating a request when already friends
            $status = $this->getStatus($fromId, $toId);
            if ($status !== 'none') {
                // indicate that a relationship or pending request already exists
                return 'exists';
            }

            $sql = "INSERT INTO " . $this->table . " (requester_id, addressee_id, status, created_at) VALUES (:r, :a, 'pending', NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':r', $fromId);
            $stmt->bindValue(':a', $toId);
            $ok = $stmt->execute();
            return $ok ? true : false;
        } catch (Exception $e) {
            // Table may not exist or other DB error; fail gracefully
            return false;
        }
    }

    public function acceptRequest($fromId, $toId) {
        try {
            $sql = "UPDATE " . $this->table . " SET status='accepted' WHERE requester_id = :r AND addressee_id = :a AND status = 'pending'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':r', $fromId);
            $stmt->bindValue(':a', $toId);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function removeFriendship($a, $b) {
        try {
            $sql = "DELETE FROM " . $this->table . " WHERE (requester_id = :a AND addressee_id = :b) OR (requester_id = :b AND addressee_id = :a)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':a', $a);
            $stmt->bindValue(':b', $b);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    // status: 'none', 'pending_sent', 'pending_received', 'friends'
    public function getStatus($me, $other) {
        try {
            $sql = "SELECT requester_id, addressee_id, status FROM " . $this->table . " WHERE (requester_id = :me AND addressee_id = :other) OR (requester_id = :other AND addressee_id = :me) LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':me', $me);
            $stmt->bindValue(':other', $other);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return 'none';
            if ($row['status'] === 'accepted') return 'friends';
            if ($row['status'] === 'pending') {
                if ($row['requester_id'] == $me) return 'pending_sent';
                return 'pending_received';
            }
            return 'none';
        } catch (Exception $e) {
            // Table missing or error: treat as no relationship
            return 'none';
        }
    }

    // Get list of accepted friends (basic user info)
    public function getFriends($me) {
        try {
            $sql = "SELECT u.* FROM " . $this->table . " f JOIN users u ON u.id = f.addressee_id WHERE f.requester_id = :me AND f.status = 'accepted' 
                    UNION 
                    SELECT u.* FROM " . $this->table . " f JOIN users u ON u.id = f.requester_id WHERE f.addressee_id = :me2 AND f.status = 'accepted'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':me', $me);
            $stmt->bindValue(':me2', $me);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get incoming friend requests (users who requested me)
    public function getReceivedRequests($me) {
        try {
            $sql = "SELECT u.*, f.requester_id FROM " . $this->table . " f JOIN users u ON u.id = f.requester_id WHERE f.addressee_id = :me AND f.status = 'pending' ORDER BY f.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':me', $me);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get outgoing friend requests (users I requested)
    public function getSentRequests($me) {
        try {
            $sql = "SELECT u.*, f.addressee_id FROM " . $this->table . " f JOIN users u ON u.id = f.addressee_id WHERE f.requester_id = :me AND f.status = 'pending' ORDER BY f.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':me', $me);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

?>
