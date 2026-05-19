<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Memperluas hak akses untuk admin dan pengurus_mpk
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['posisi'], ['admin', 'pengurus_mpk'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nama = $_POST['nama_lengkap'];
    $posisi = $_POST['posisi'];
    $kelas = $_POST['kelas'] ?? null;
    $email = $_POST['email'] ?? null;
    $no_telepon = $_POST['no_telepon'] ?? null;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, posisi, kelas, email, no_telepon) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $password, $nama, $posisi, $kelas, $email, $no_telepon])) {
        echo json_encode(['status' => 'success', 'message' => 'Anggota berhasil ditambahkan!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambah anggota.']);
    }
}

elseif ($action === 'read') {
    $stmt = $pdo->query("SELECT id, username, nama_lengkap, posisi, kelas, email, no_telepon FROM users ORDER BY id DESC");
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

elseif ($action === 'update') {
    $id = $_POST['id'];
    $nama = $_POST['nama_lengkap'];
    $posisi = $_POST['posisi'];
    $kelas = $_POST['kelas'] ?? null;
    $email = $_POST['email'] ?? null;
    $no_telepon = $_POST['no_telepon'] ?? null;

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap=?, posisi=?, kelas=?, email=?, no_telepon=?, password=? WHERE id=?");
        $result = $stmt->execute([$nama, $posisi, $kelas, $email, $no_telepon, $password, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap=?, posisi=?, kelas=?, email=?, no_telepon=? WHERE id=?");
        $result = $stmt->execute([$nama, $posisi, $kelas, $email, $no_telepon, $id]);
    }

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Data anggota diperbarui.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
    }
}

elseif ($action === 'delete') {
    $id = $_POST['id'];
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak bisa menghapus akun Anda sendiri!']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'Anggota berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus anggota.']);
    }
}
?>
