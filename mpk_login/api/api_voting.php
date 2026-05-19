<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit;
}

$hasEditRights = in_array($_SESSION['posisi'], ['admin', 'pengurus_mpk']);
$action = $_POST['action'] ?? '';

try {
    if ($action === 'read_polls') {
        $stmt = $pdo->query("SELECT * FROM polls ORDER BY is_pinned DESC, status ASC, created_at DESC");
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($polls as &$poll) {
            $stmtCount = $pdo->prepare("SELECT vote_type, COUNT(*) as count FROM poll_votes WHERE poll_id = ? GROUP BY vote_type");
            $stmtCount->execute([$poll['id']]);
            $results = $stmtCount->fetchAll(PDO::FETCH_KEY_PAIR);
            $poll['setuju'] = $results['setuju'] ?? 0;
            $poll['tidak_setuju'] = $results['tidak_setuju'] ?? 0;
            
            $stmtUser = $pdo->prepare("SELECT vote_type FROM poll_votes WHERE poll_id = ? AND user_id = ?");
            $stmtUser->execute([$poll['id'], $_SESSION['user_id']]);
            $poll['user_voted'] = $stmtUser->fetchColumn();
        }
        
        echo json_encode(['status' => 'success', 'data' => $polls]);
    } 
    elseif ($action === 'save_poll') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE polls SET title=?, description=?, is_pinned=? WHERE id=?");
            $stmt->execute([$title, $desc, $is_pinned, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO polls (title, description, is_pinned) VALUES (?, ?, ?)");
            $stmt->execute([$title, $desc, $is_pinned]);
        }
        echo json_encode(['status' => 'success', 'message' => 'Voting berhasil disimpan!']);
    } 
    elseif ($action === 'toggle_status') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        
        $stmt = $pdo->prepare("SELECT status FROM polls WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $current = $stmt->fetchColumn();
        
        $newStatus = ($current === 'active') ? 'closed' : 'active';
        $stmtUpdate = $pdo->prepare("UPDATE polls SET status = ? WHERE id = ?");
        $stmtUpdate->execute([$newStatus, $_POST['id']]);
        
        echo json_encode(['status' => 'success', 'message' => 'Status berhasil diubah.']);
    }
    elseif ($action === 'delete_poll') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        $stmt = $pdo->prepare("DELETE FROM polls WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        echo json_encode(['status' => 'success', 'message' => 'Voting terhapus.']);
    }
    elseif ($action === 'cast_vote') {
        $poll_id = $_POST['poll_id'];
        $vote_type = $_POST['vote_type'];
        
        $stmtCheck = $pdo->prepare("SELECT status FROM polls WHERE id = ?");
        $stmtCheck->execute([$poll_id]);
        if($stmtCheck->fetchColumn() === 'closed') {
            echo json_encode(['status' => 'error', 'message' => 'Voting ini sudah ditutup.']); 
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO poll_votes (poll_id, user_id, vote_type) VALUES (?, ?, ?)");
            $stmt->execute([$poll_id, $_SESSION['user_id'], $vote_type]);
            echo json_encode(['status' => 'success', 'message' => 'Suara Anda berhasil direkam.']);
        } catch(PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Anda sudah memberikan suara pada voting ini.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>
