<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); exit;
}

// Definisikan siapa yang punya hak edit (Admin & Pengurus MPK)
$hasEditRights = in_array($_SESSION['posisi'], ['admin', 'pengurus_mpk']);

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS notes (id INT AUTO_INCREMENT PRIMARY KEY, content TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (id INT AUTO_INCREMENT PRIMARY KEY, author_id INT, title VARCHAR(255), content TEXT, embed_code TEXT, is_pinned TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
} catch(PDOException $e) {}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'read_notes') {
        $stmt = $pdo->query("SELECT * FROM notes ORDER BY id DESC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } 
    elseif ($action === 'save_note') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        $stmt = $pdo->prepare("INSERT INTO notes (content) VALUES (?)");
        $stmt->execute([$_POST['content']]);
        echo json_encode(['status' => 'success']);
    } 
    elseif ($action === 'delete_note') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        echo json_encode(['status' => 'success']);
    } 
    elseif ($action === 'read_posts') {
        $stmt = $pdo->query("SELECT p.*, u.nama_lengkap FROM posts p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.is_pinned DESC, p.created_at DESC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } 
    elseif ($action === 'save_post') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'];
        $content = $_POST['content'];
        $embed = $_POST['embed_code'] ?? '';
        $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE posts SET title=?, content=?, embed_code=?, is_pinned=? WHERE id=?");
            $stmt->execute([$title, $content, $embed, $is_pinned, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO posts (author_id, title, content, embed_code, is_pinned) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $content, $embed, $is_pinned]);
        }
        echo json_encode(['status' => 'success', 'message' => 'Postingan disimpan!']);
    } 
    elseif ($action === 'delete_post') {
        if(!$hasEditRights) { echo json_encode(['status'=>'error', 'message'=>'Akses Ditolak']); exit; }
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        echo json_encode(['status' => 'success']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>
