<?php
session_start();
require_once 'config/database.php';

// Menangani permintaan logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? 'login';
    
    if ($action === 'login') {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $userData = $stmt->fetch();

        if ($userData && password_verify($pass, $userData['password'])) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['posisi'] = $userData['posisi'];
            $_SESSION['nama_lengkap'] = $userData['nama_lengkap'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Kredensial tidak ditemukan atau salah.";
        }
    } elseif ($action === 'register') {
        $user = trim($_POST['reg_username']);
        $pass = $_POST['reg_password'];
        $nama = trim($_POST['reg_nama']);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$user]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username sudah terdaftar. Silakan gunakan yang lain.";
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, posisi) VALUES (?, ?, ?, 'anggota')");
            if ($stmt->execute([$user, $hash, $nama])) {
                $success = "Pendaftaran berhasil! Silakan masuk dengan akun Anda.";
            } else {
                $error = "Terjadi kesalahan sistem saat mendaftar.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal MPK - Akses</title>
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <link rel="manifest" href="img/favicon/site.webmanifest">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> .hidden-form { display: none; } </style>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200">
        <div class="text-center mb-8">
            <img src="img/logo.png" alt="Logo MPK" class="w-20 h-20 object-contain mx-auto mb-4 drop-shadow-md">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight" id="form-title">MPK Login</h1>
            <p class="text-slate-500 text-sm mt-1" id="form-subtitle">Otentikasi akses diperlukan</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-5 text-center font-medium border border-red-100">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-emerald-50 text-emerald-600 p-3 rounded-lg text-sm mb-5 text-center font-medium border border-emerald-100">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="login-form" class="space-y-5 <?= (isset($_POST['action']) && $_POST['action'] === 'register' && !$success) ? 'hidden-form' : '' ?>">
            <input type="hidden" name="action" value="login">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Username Login</label>
                <input type="text" name="username" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2 mt-2">
                Masuk <i class="fa-solid fa-arrow-right"></i>
            </button>
            <p class="text-center text-sm text-slate-600 mt-4">Belum memiliki akun? <button type="button" onclick="toggleForm('register')" class="text-blue-600 font-bold hover:underline">Daftar sekarang</button></p>
        </form>

        <form method="POST" action="" id="register-form" class="space-y-5 <?= (!isset($_POST['action']) || $_POST['action'] === 'login' || $success) ? 'hidden-form' : '' ?>">
            <input type="hidden" name="action" value="register">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="reg_nama" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Username Baru</label>
                <input type="text" name="reg_username" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Buat Password</label>
                <input type="password" name="reg_password" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg hover:bg-emerald-700 transition shadow-md flex items-center justify-center gap-2 mt-2">
                Daftar Akun <i class="fa-solid fa-user-plus"></i>
            </button>
            <p class="text-center text-sm text-slate-600 mt-4">Sudah terdaftar? <button type="button" onclick="toggleForm('login')" class="text-blue-600 font-bold hover:underline">Masuk di sini</button></p>
        </form>
    </div>

    <script>
        function toggleForm(type) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const subtitle = document.getElementById('form-subtitle');
            if(type === 'register') {
                loginForm.classList.add('hidden-form');
                registerForm.classList.remove('hidden-form');
                subtitle.innerText = 'Buat akun anggota baru';
            } else {
                registerForm.classList.add('hidden-form');
                loginForm.classList.remove('hidden-form');
                subtitle.innerText = 'Otentikasi akses diperlukan';
            }
        }
    </script>
</body>
</html>
