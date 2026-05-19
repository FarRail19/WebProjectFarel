<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}
$isAdmin = ($_SESSION['posisi'] === 'admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPK Dashboard</title>
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <link rel="manifest" href="img/favicon/site.webmanifest">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .embed-container iframe { width: 100%; border-radius: 0.5rem; }
        #sidebar { transition: transform 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-slate-100 flex h-screen overflow-hidden text-slate-800 relative">

    <div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-20 hidden md:hidden"></div>

    <div id="sidebar" class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col z-30 text-white shrink-0 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0">
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <img src="img/logo.png" alt="Logo" class="w-8 h-8 object-contain" onerror="this.src='https://ui-avatars.com/api/?name=MPK&background=2563eb&color=fff'">
                <span class="text-lg font-bold tracking-wider">MPK Dashboard</span>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <button onclick="switchTab('tab-dashboard')" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-left text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-chart-line w-6 text-center"></i> Dashboard Utama
            </button>
            <button onclick="switchTab('tab-posts')" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-left text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-bullhorn w-6 text-center"></i> Pengumuman & Media
            </button>
            <button onclick="switchTab('tab-voting')" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-left text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-check-to-slot w-6 text-center"></i> Sistem Voting
            </button>
            <?php if ($isAdmin): ?>
            <button onclick="switchTab('tab-users')" class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg text-left text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <i class="fa-solid fa-users w-6 text-center"></i> Kelola Anggota
            </button>
            <?php endif; ?>
        </nav>
        <div class="p-4 border-t border-slate-800">
             <button onclick="logout()" class="w-full flex items-center justify-center px-4 py-2 bg-red-500/10 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition text-sm font-medium border border-red-500/20">
                <i class="fa-solid fa-power-off mr-2"></i> Keluar Akses
            </button>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden relative w-full">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8 shadow-sm z-10 shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden text-slate-600 hover:text-blue-600 focus:outline-none"><i class="fa-solid fa-bars text-xl"></i></button>
                <h2 class="text-base md:text-lg font-bold text-slate-700 truncate" id="header-title">Dashboard Utama</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-800 leading-tight"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></p>
                    <p class="text-xs text-blue-600 font-medium uppercase tracking-wider"><?= htmlspecialchars($_SESSION['posisi']) ?></p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-base md:text-lg border border-blue-200 shrink-0">
                    <?= substr($_SESSION['nama_lengkap'], 0, 1) ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
            
            <div id="tab-dashboard" class="tab-content block animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                    <div class="bg-white p-5 md:p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm text-slate-500 font-medium uppercase tracking-wider">Selamat Datang</p>
                            <h3 class="text-xl md:text-2xl font-bold text-slate-800 mt-1"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h3>
                        </div>
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl md:text-2xl border border-blue-100"><i class="fa-solid fa-user-check"></i></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col max-h-[500px]">
                        <div class="p-4 border-b border-slate-200 bg-slate-50 shrink-0">
                            <h3 class="font-bold text-slate-800 text-sm md:text-base"><i class="fa-solid fa-thumbtack text-blue-500 mr-2"></i>Pengumuman Tersorot</h3>
                        </div>
                        <div id="dashboard-pinned-posts" class="p-4 overflow-y-auto flex-1 space-y-4 bg-slate-50/50">
                            <div class="text-center text-slate-400 text-sm p-4 border border-dashed rounded-lg">Memuat...</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col max-h-[500px]">
                        <div class="p-4 border-b border-slate-200 bg-slate-50 shrink-0">
                            <h3 class="font-bold text-slate-800 text-sm md:text-base"><i class="fa-solid fa-check-to-slot text-blue-500 mr-2"></i>Voting Tersorot</h3>
                        </div>
                        <div id="dashboard-pinned-polls" class="p-4 overflow-y-auto flex-1 space-y-4 bg-slate-50/50">
                            <div class="text-center text-slate-400 text-sm p-4 border border-dashed rounded-lg">Memuat...</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                    <div class="p-4 md:p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 text-sm md:text-base"><i class="fa-regular fa-calendar-days text-blue-500 mr-2"></i>Papan Jadwal & Catatan</h3>
                    </div>
                    <div class="p-4 md:p-6">
                        <?php if($isAdmin): ?>
                        <form id="form-note" onsubmit="submitNote(event)" class="flex flex-col sm:flex-row gap-3 mb-6">
                            <input type="text" name="content" required placeholder="Tulis pengingat, jadwal..." class="flex-1 px-4 py-2 text-sm md:text-base border border-slate-300 rounded-lg outline-none bg-slate-50">
                            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 font-medium transition shadow-sm whitespace-nowrap">Tambah</button>
                        </form>
                        <?php endif; ?>
                        <ul id="notes-list" class="space-y-3"></ul>
                    </div>
                </div>
            </div>

            <div id="tab-posts" class="tab-content hidden animate-fade-in">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                    <div>
                        <h2 class="text-lg md:text-2xl font-bold text-slate-800">Pengumuman & Media</h2>
                        <p class="text-slate-500 text-xs md:text-sm">Informasi terbaru seputar kegiatan MPK.</p>
                    </div>
                    <?php if($isAdmin): ?>
                    <button onclick="openModal('modal-form-post')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-pen-nib mr-2"></i> Buat Post
                    </button>
                    <?php endif; ?>
                </div>
                <div id="posts-container" class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8"></div>
            </div>

            <div id="tab-voting" class="tab-content hidden animate-fade-in">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                    <div>
                        <h2 class="text-lg md:text-2xl font-bold text-slate-800">Sistem Voting & Polling</h2>
                        <p class="text-slate-500 text-xs md:text-sm">Berikan suara Anda untuk keputusan bersama.</p>
                    </div>
                    <?php if($isAdmin): ?>
                    <button onclick="openModal('modal-form-poll')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-square-poll-vertical mr-2"></i> Buat Polling
                    </button>
                    <?php endif; ?>
                </div>
                <div id="polls-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8"></div>
            </div>

            <?php if($isAdmin): ?>
            <div id="tab-users" class="tab-content hidden animate-fade-in">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[calc(100vh-10rem)] md:h-[calc(100vh-12rem)]">
                    <div class="p-4 md:p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50 gap-3 shrink-0">
                        <h3 class="font-bold text-slate-800 text-sm md:text-lg">Basis Data Pengguna</h3>
                        <button onclick="openModal('modal-form-user')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm w-full sm:w-auto"><i class="fa-solid fa-plus mr-2"></i> Entri Baru</button>
                    </div>
                    <div class="overflow-auto flex-1 p-0">
                        <table class="w-full text-left border-collapse min-w-[700px]">
                            <thead class="sticky top-0 bg-white z-10 shadow-sm">
                                <tr class="text-slate-500 text-[10px] md:text-xs uppercase tracking-wider border-b border-slate-200">
                                    <th class="p-3 md:p-4 font-semibold w-1/3">Identitas (Nama & Akun)</th>
                                    <th class="p-3 md:p-4 font-semibold w-1/6">Posisi & Kelas</th>
                                    <th class="p-3 md:p-4 font-semibold w-1/3">Kontak</th>
                                    <th class="p-3 md:p-4 font-semibold text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body" class="divide-y divide-slate-100 text-xs md:text-sm"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>

    <?php if($isAdmin): ?>
    
    <div id="modal-form-user" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity p-4">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform flex flex-col max-h-full">
            <div class="p-4 md:p-5 border-b border-slate-200 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-slate-800 text-base md:text-lg" id="modal-user-title">Form Anggota</h3>
                <button onclick="closeModal('modal-form-user')" class="text-slate-400 hover:text-red-500 bg-slate-100 w-8 h-8 rounded-full"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="form-user" onsubmit="submitUserForm(event)" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-4 md:p-6 overflow-y-auto flex-1 space-y-3 md:space-y-4">
                    <input type="hidden" name="action" id="form-action" value="create">
                    <input type="hidden" name="id" id="form-user-id" value="">
                    <div><label class="block text-xs md:text-sm font-semibold mb-1">Nama Lengkap</label><input type="text" name="nama_lengkap" id="input-nama" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-xs md:text-sm font-semibold mb-1">Username</label><input type="text" name="username" id="input-username" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"></div>
                        <div><label class="block text-xs md:text-sm font-semibold mb-1">Password</label><input type="password" name="password" id="input-password" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs md:text-sm font-semibold mb-1">Hak Akses</label>
                            <select name="posisi" id="input-posisi" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg">
                                <option value="anggota">Anggota Biasa</option><option value="pengurus_mpk">Pengurus MPK</option><option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div><label class="block text-xs md:text-sm font-semibold mb-1">Kelas</label><input type="text" name="kelas" id="input-kelas" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-xs md:text-sm font-semibold mb-1">Nomor Telepon</label><input type="text" name="no_telepon" id="input-telp" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"></div>
                        <div><label class="block text-xs md:text-sm font-semibold mb-1">Email</label><input type="email" name="email" id="input-email" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg"></div>
                    </div>
                </div>
                <div class="p-4 md:p-5 border-t border-slate-200 bg-slate-50 flex justify-end shrink-0 rounded-b-xl gap-2">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base font-medium w-full sm:w-auto">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-form-post" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl transform scale-95 transition-transform flex flex-col max-h-full">
            <div class="p-4 md:p-5 border-b border-slate-200 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-slate-800 text-base md:text-lg" id="modal-post-title">Form Pengumuman</h3>
                <button onclick="closeModal('modal-form-post')" class="text-slate-400 hover:text-red-500 bg-slate-100 w-8 h-8 rounded-full"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="form-post" onsubmit="submitPostForm(event)" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-4 md:p-6 overflow-y-auto flex-1 space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="save_post">
                    <input type="hidden" name="id" id="input-post-id" value="">
                    <div><label class="block text-xs md:text-sm font-semibold mb-1">Judul <span class="text-red-500">*</span></label><input type="text" name="title" id="input-post-title" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg outline-none"></div>
                    <div><label class="block text-xs md:text-sm font-semibold mb-1">Isi Konten</label><textarea name="content" id="input-post-content" rows="4" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg outline-none"></textarea></div>
                    <div><label class="block text-xs md:text-sm font-semibold mb-1">HTML Embed (Youtube/IG/Tiktok)</label><textarea name="embed_code" id="input-post-embed" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg font-mono text-xs bg-slate-50"></textarea></div>
                    <div class="flex items-center gap-2 mt-2 bg-yellow-50 border border-yellow-200 p-2 md:p-3 rounded-lg"><input type="checkbox" name="is_pinned" id="input-post-pinned" value="1" class="w-4 h-4 text-blue-600"><label for="input-post-pinned" class="text-xs md:text-sm font-bold text-yellow-800 cursor-pointer select-none">Sematkan postingan ini</label></div>
                </div>
                <div class="p-4 md:p-5 border-t border-slate-200 bg-slate-50 flex justify-end shrink-0 rounded-b-xl gap-2">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base font-medium w-full sm:w-auto">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-form-poll" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity p-4">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform flex flex-col max-h-full">
            <div class="p-4 md:p-5 border-b border-slate-200 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-slate-800 text-base md:text-lg" id="modal-poll-title">Buat Polling Baru</h3>
                <button onclick="closeModal('modal-form-poll')" class="text-slate-400 hover:text-red-500 bg-slate-100 w-8 h-8 rounded-full"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="form-poll" onsubmit="submitPollForm(event)" class="flex flex-col flex-1 overflow-hidden">
                <div class="p-4 md:p-6 overflow-y-auto flex-1 space-y-3 md:space-y-4">
                    <input type="hidden" name="action" value="save_poll">
                    <input type="hidden" name="id" id="input-poll-id" value="">
                    <div><label class="block text-xs md:text-sm font-semibold mb-1">Judul Polling <span class="text-red-500">*</span></label><input type="text" name="title" id="input-poll-title" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg outline-none"></div>
                    <div><label class="block text-xs md:text-sm font-semibold mb-1">Deskripsi Polling</label><textarea name="description" id="input-poll-desc" rows="4" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg outline-none"></textarea></div>
                    <div class="flex items-center gap-2 mt-2 bg-yellow-50 border border-yellow-200 p-2 md:p-3 rounded-lg"><input type="checkbox" name="is_pinned" id="input-poll-pinned" value="1" class="w-4 h-4 text-blue-600"><label for="input-poll-pinned" class="text-xs md:text-sm font-bold text-yellow-800 cursor-pointer select-none">Sematkan polling ini</label></div>
                </div>
                <div class="p-4 md:p-5 border-t border-slate-200 bg-slate-50 flex justify-end shrink-0 rounded-b-xl gap-2">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm md:text-base font-medium w-full sm:w-auto">Simpan Polling</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        const IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;
        let userDataCache = [], postDataCache = [], pollDataCache = [];

        // --- NAVIGATION ---
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('mobile-overlay').classList.toggle('hidden');
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            const targetTab = document.getElementById(tabId);
            if(targetTab) targetTab.classList.remove('hidden');
            
            let titles = { 'tab-dashboard': 'Dashboard Utama', 'tab-posts': 'Pengumuman & Media', 'tab-voting': 'Sistem Voting', 'tab-users': 'Manajemen Basis Data' };
            document.getElementById('header-title').innerText = titles[tabId] || 'MPK Dashboard';

            if(window.innerWidth < 768 && !document.getElementById('sidebar').classList.contains('-translate-x-full')) {
                toggleSidebar();
            }
        }

        // --- DATA FETCHERS ---
        function loadNotes() {
            let fd = new FormData(); fd.append('action', 'read_notes');
            fetch('api/api_content.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    let html = '';
                    res.data.forEach(n => {
                        let delBtn = IS_ADMIN ? `<button type="button" onclick="deleteAction('api/api_content.php', ${n.id}, loadNotes, 'delete_note')" class="text-red-400 hover:text-red-600 ml-3"><i class="fa-solid fa-xmark text-lg"></i></button>` : '';
                        html += `<li class="p-3 md:p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex justify-between items-center shadow-sm"><span class="text-yellow-800 font-medium text-xs md:text-sm">${n.content}</span>${delBtn}</li>`;
                    });
                    document.getElementById('notes-list').innerHTML = html || '<p class="text-slate-400 text-xs md:text-sm">Tidak ada jadwal aktif.</p>';
                }
            }).catch(e => console.error("Error Notes:", e));
        }

        function loadPosts() {
            let fd = new FormData(); fd.append('action', 'read_posts');
            fetch('api/api_content.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    postDataCache = res.data;
                    let fullHtml = '', pinnedHtml = '';
                    res.data.forEach(p => {
                        let pinBadge = p.is_pinned == 1 ? '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-[10px] md:text-xs font-bold"><i class="fa-solid fa-thumbtack mr-1"></i> Pinned</span>' : '';
                        let embedHtml = p.embed_code ? `<div class="mt-4 embed-container overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-2">${p.embed_code}</div>` : '';
                        let adminActions = IS_ADMIN ? `<div class="bg-slate-50 border-t border-slate-100 p-3 flex justify-end gap-2 mt-auto"><button type="button" onclick="openModal('modal-form-post', 'update', ${p.id})" class="text-blue-600 bg-blue-50 px-3 py-1.5 rounded text-xs md:text-sm font-medium hover:bg-blue-100">Edit</button><button type="button" onclick="deleteAction('api/api_content.php', ${p.id}, loadPosts, 'delete_post')" class="text-red-600 bg-red-50 px-3 py-1.5 rounded text-xs md:text-sm font-medium hover:bg-red-100">Hapus</button></div>` : '';
                        
                        let html = `
                            <div class="bg-white rounded-xl border ${p.is_pinned == 1 ? 'border-yellow-300 shadow-md' : 'border-slate-200 shadow-sm'} overflow-hidden flex flex-col">
                                <div class="p-4 md:p-5 flex-1">
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <h3 class="font-bold text-base md:text-lg text-slate-800 leading-tight">${p.title}</h3>
                                        ${pinBadge}
                                    </div>
                                    <p class="text-[10px] md:text-xs text-slate-400 mb-3 md:mb-4">Oleh ${p.nama_lengkap || 'Admin'} &bull; ${p.created_at}</p>
                                    <p class="text-slate-600 text-xs md:text-sm whitespace-pre-wrap">${p.content}</p>
                                    ${embedHtml}
                                </div>
                                ${adminActions}
                            </div>`;
                        
                        fullHtml += html;
                        if(p.is_pinned == 1) pinnedHtml += html;
                    });
                    document.getElementById('posts-container').innerHTML = fullHtml || '<div class="col-span-full p-8 text-center text-slate-400 border-2 border-dashed rounded-xl text-sm">Belum ada pengumuman.</div>';
                    document.getElementById('dashboard-pinned-posts').innerHTML = pinnedHtml || '<div class="text-center text-slate-400 text-sm p-4 border border-dashed rounded-lg">Tidak ada sematan.</div>';
                }
            }).catch(e => console.error("Error Posts:", e));
        }

        function loadPolls() {
            let fd = new FormData(); fd.append('action', 'read_polls');
            fetch('api/api_voting.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    pollDataCache = res.data || [];
                    let fullHtml = '', pinnedHtml = '';
                    
                    pollDataCache.forEach(poll => {
                        let totalVotes = parseInt(poll.setuju || 0) + parseInt(poll.tidak_setuju || 0);
                        let pctSetuju = totalVotes > 0 ? Math.round((poll.setuju / totalVotes) * 100) : 0;
                        let pctTidak = totalVotes > 0 ? Math.round((poll.tidak_setuju / totalVotes) * 100) : 0;
                        
                        let statusBadge = poll.status === 'active' ? '<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-[10px] font-bold">AKTIF</span>' : '<span class="bg-slate-200 text-slate-600 px-2 py-1 rounded text-[10px] font-bold">DITUTUP</span>';
                        let pinBadge = poll.is_pinned == 1 ? '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-[10px] font-bold ml-2"><i class="fa-solid fa-thumbtack"></i> Pinned</span>' : '';
                        
                        let voteArea = '';
                        if(poll.status === 'closed') {
                            voteArea = `<div class="text-center py-2 bg-slate-50 text-slate-500 rounded border text-xs font-medium">Voting ditutup.</div>`;
                        } else if(poll.user_voted) {
                            voteArea = `<div class="text-center py-2 bg-blue-50 text-blue-600 rounded border border-blue-200 text-xs font-medium">Suara Anda: ${poll.user_voted == 'setuju' ? 'Setuju' : 'Tidak Setuju'}</div>`;
                        } else {
                            voteArea = `
                                <div class="flex gap-2">
                                    <button type="button" onclick="castVote(${poll.id}, 'setuju')" class="flex-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200 py-2 rounded text-xs font-medium transition">Setuju</button>
                                    <button type="button" onclick="castVote(${poll.id}, 'tidak_setuju')" class="flex-1 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 py-2 rounded text-xs font-medium transition">Tidak Setuju</button>
                                </div>`;
                        }

                        let adminActions = IS_ADMIN ? `
                            <div class="border-t border-slate-100 p-3 flex justify-between gap-2 mt-4 bg-slate-50 rounded-b-xl">
                                <button type="button" onclick="togglePollStatus(${poll.id})" class="text-slate-600 bg-white border border-slate-300 px-3 py-1.5 rounded text-xs font-medium hover:bg-slate-100">${poll.status === 'active' ? 'Tutup' : 'Buka'}</button>
                                <div class="flex gap-2">
                                    <button type="button" onclick="openModal('modal-form-poll', 'update', ${poll.id})" class="text-blue-600 bg-blue-50 px-3 py-1.5 rounded text-xs font-medium hover:bg-blue-100">Edit</button>
                                    <button type="button" onclick="deleteAction('api/api_voting.php', ${poll.id}, loadPolls, 'delete_poll')" class="text-red-600 bg-red-50 px-3 py-1.5 rounded text-xs font-medium hover:bg-red-100">Hapus</button>
                                </div>
                            </div>` : '';

                        let html = `
                            <div class="bg-white rounded-xl border ${poll.is_pinned == 1 ? 'border-yellow-300 shadow-md' : 'border-slate-200 shadow-sm'} flex flex-col relative ${poll.status === 'closed' ? 'opacity-75' : ''}">
                                <div class="p-4 md:p-5 flex-1">
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <h3 class="font-bold text-base md:text-lg text-slate-800 leading-tight">${poll.title} ${pinBadge}</h3>
                                        ${statusBadge}
                                    </div>
                                    <p class="text-slate-600 text-xs md:text-sm mb-4">${poll.description}</p>
                                    <div class="mb-4">
                                        <div class="flex justify-between text-[10px] md:text-xs mb-1 font-bold">
                                            <span class="text-emerald-600">Setuju (${poll.setuju})</span>
                                            <span class="text-red-600">Tidak Setuju (${poll.tidak_setuju})</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-2 md:h-3 rounded-full flex overflow-hidden">
                                            <div class="bg-emerald-500 h-full" style="width: ${pctSetuju}%"></div>
                                            <div class="bg-red-500 h-full" style="width: ${pctTidak}%"></div>
                                        </div>
                                    </div>
                                    ${voteArea}
                                </div>
                                ${adminActions}
                            </div>`;
                        
                        fullHtml += html;
                        if(poll.is_pinned == 1) pinnedHtml += html;
                    });
                    
                    document.getElementById('polls-container').innerHTML = fullHtml || '<div class="col-span-full p-8 text-center text-slate-400 border-2 border-dashed rounded-xl text-sm">Belum ada voting yang dibuat.</div>';
                    document.getElementById('dashboard-pinned-polls').innerHTML = pinnedHtml || '<div class="text-center text-slate-400 text-sm p-4 border border-dashed rounded-lg">Tidak ada voting tersorot.</div>';
                } else {
                    console.error("API Error:", res.message);
                }
            }).catch(e => console.error("Fetch Polls Error:", e));
        }

        function loadUsers() {
            if(!IS_ADMIN) return;
            let fd = new FormData(); fd.append('action', 'read');
            fetch('api/api_users.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    userDataCache = res.data || [];
                    let html = '';
                    userDataCache.forEach(u => {
                        let badge = u.posisi === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700';
                        let kontak = (u.email || u.no_telepon) ? `<div class="truncate max-w-[150px]"><i class="fa-regular fa-envelope w-4"></i> ${u.email||'-'}</div><div class="mt-1"><i class="fa-solid fa-phone w-4"></i> ${u.no_telepon||'-'}</div>` : '-';
                        html += `
                            <tr class="border-b">
                                <td class="p-3 md:p-4 font-bold text-xs md:text-sm">${u.nama_lengkap}<br><span class="text-[10px] text-slate-400 font-normal">@${u.username}</span></td>
                                <td class="p-3 md:p-4"><span class="px-2 py-1 rounded text-[10px] font-bold uppercase ${badge}">${u.posisi.replace('_',' ')}</span><div class="mt-2 text-[11px] font-medium text-slate-500">${u.kelas||''}</div></td>
                                <td class="p-3 md:p-4 text-slate-600 text-[11px] md:text-xs">${kontak}</td>
                                <td class="p-3 md:p-4 text-center">
                                    <div class="flex justify-center gap-1.5">
                                        <button type="button" onclick="openModal('modal-form-user', 'update', ${u.id})" class="text-blue-500 bg-blue-50 p-1.5 rounded"><i class="fa-solid fa-pen"></i></button>
                                        <button type="button" onclick="deleteAction('api/api_users.php', ${u.id}, loadUsers)" class="text-red-500 bg-red-50 p-1.5 rounded"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                    document.getElementById('users-table-body').innerHTML = html || '<tr><td colspan="4" class="p-4 text-center">Kosong</td></tr>';
                }
            }).catch(e => console.error("Error Users:", e));
        }

        // --- ACTIONS ---
        function castVote(poll_id, vote_type) {
            let fd = new FormData(); fd.append('action', 'cast_vote'); fd.append('poll_id', poll_id); fd.append('vote_type', vote_type);
            fetch('api/api_voting.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if(res.status === 'success') { loadPolls(); fireToast(res.message); } 
                else { Swal.fire({title: 'Oops!', text: res.message, icon: 'error'}); }
            }).catch(e => console.error("Error Cast Vote:", e));
        }

        function togglePollStatus(id) {
            if(!IS_ADMIN) return;
            let fd = new FormData(); fd.append('action', 'toggle_status'); fd.append('id', id);
            fetch('api/api_voting.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if(res.status === 'success') { loadPolls(); fireToast('Status diubah!'); }
            }).catch(e => console.error(e));
        }

        function deleteAction(url, id, callback, actionName = 'delete') {
            if(!IS_ADMIN) return;
            Swal.fire({ title: 'Hapus data?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Hapus' }).then((r) => {
                if (r.isConfirmed) {
                    let fd = new FormData(); fd.append('action', actionName); fd.append('id', id);
                    fetch(url, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                        if(res.status === 'success') { callback(); fireToast('Dihapus!'); }
                        else Swal.fire('Gagal', res.message, 'error');
                    }).catch(e => console.error("Error:", e));
                }
            });
        }

        function fireToast(msg) { Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:3000}).fire({icon:'success', title:msg}); }
        function logout() { window.location.href = 'login.php?action=logout'; }

        // --- MODAL SYSTEM ---
        function openModal(id, mode = 'create', dataId = null) { 
            if(!IS_ADMIN) return;
            const modal = document.getElementById(id);
            if(!modal) return;
            
            if(id === 'modal-form-user') {
                const form = document.getElementById('form-user');
                if (mode === 'create') {
                    document.getElementById('modal-user-title').innerText = 'Entri Anggota Baru';
                    document.getElementById('form-action').value = 'create';
                    document.getElementById('input-username').disabled = false;
                    document.getElementById('input-password').required = true;
                    form.reset();
                } else if (mode === 'update') {
                    document.getElementById('modal-user-title').innerText = 'Edit Data Anggota';
                    document.getElementById('form-action').value = 'update';
                    document.getElementById('form-user-id').value = dataId;
                    const user = userDataCache.find(u => u.id == dataId);
                    if(user) {
                        document.getElementById('input-nama').value = user.nama_lengkap || '';
                        document.getElementById('input-username').value = user.username || '';
                        document.getElementById('input-username').disabled = true;
                        document.getElementById('input-posisi').value = user.posisi || 'anggota';
                        document.getElementById('input-kelas').value = user.kelas || '';
                        document.getElementById('input-email').value = user.email || '';
                        document.getElementById('input-telp').value = user.no_telepon || '';
                        document.getElementById('input-password').required = false;
                        document.getElementById('input-password').value = '';
                    }
                }
            } else if (id === 'modal-form-post') {
                const form = document.getElementById('form-post');
                if(mode === 'create') {
                    document.getElementById('modal-post-title').innerText = 'Buat Pengumuman Baru';
                    document.getElementById('input-post-id').value = '';
                    form.reset();
                } else {
                    document.getElementById('modal-post-title').innerText = 'Edit Pengumuman';
                    document.getElementById('input-post-id').value = dataId;
                    const post = postDataCache.find(p => p.id == dataId);
                    if(post) {
                        document.getElementById('input-post-title').value = post.title || '';
                        document.getElementById('input-post-content').value = post.content || '';
                        document.getElementById('input-post-embed').value = post.embed_code || '';
                        document.getElementById('input-post-pinned').checked = (post.is_pinned == 1);
                    }
                }
            } else if (id === 'modal-form-poll') {
                const form = document.getElementById('form-poll');
                if(mode === 'create') {
                    document.getElementById('modal-poll-title').innerText = 'Buat Polling Baru';
                    document.getElementById('input-poll-id').value = '';
                    form.reset();
                } else {
                    document.getElementById('modal-poll-title').innerText = 'Edit Polling';
                    document.getElementById('input-poll-id').value = dataId;
                    const poll = pollDataCache.find(p => p.id == dataId);
                    if(poll) {
                        document.getElementById('input-poll-title').value = poll.title || '';
                        document.getElementById('input-poll-desc').value = poll.description || '';
                        document.getElementById('input-poll-pinned').checked = (poll.is_pinned == 1);
                    }
                }
            }
            modal.classList.remove('hidden');
            setTimeout(() => modal.children[0].classList.remove('scale-95'), 10);
        }

        function closeModal(id) { 
            const modal = document.getElementById(id);
            if(modal) {
                modal.children[0].classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            }
        }

        // --- FORM SUBMITS ---
        function submitUserForm(e) {
            if(!IS_ADMIN) return;
            e.preventDefault();
            let fd = new FormData(e.target);
            if(document.getElementById('form-action').value === 'update') fd.append('username', document.getElementById('input-username').value);
            fetch('api/api_users.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if(res.status === 'success') { closeModal('modal-form-user'); loadUsers(); fireToast(res.message); }
                else { Swal.fire('Error', res.message, 'error'); }
            }).catch(e => console.error(e));
        }

        function submitNote(e) {
            if(!IS_ADMIN) return;
            e.preventDefault();
            let fd = new FormData(e.target); fd.append('action', 'save_note');
            fetch('api/api_content.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if(res.status === 'success') { e.target.reset(); loadNotes(); fireToast("Disimpan!"); }
            }).catch(e => console.error(e));
        }

        function submitPostForm(e) {
            if(!IS_ADMIN) return;
            e.preventDefault();
            let fd = new FormData(e.target);
            fetch('api/api_content.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if(res.status === 'success') { closeModal('modal-form-post'); loadPosts(); fireToast(res.message); }
                else { Swal.fire('Error', res.message, 'error'); }
            }).catch(e => console.error(e));
        }

        function submitPollForm(e) {
            if(!IS_ADMIN) return;
            e.preventDefault();
            let fd = new FormData(e.target);
            fetch('api/api_voting.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
                if(res.status === 'success') { closeModal('modal-form-poll'); loadPolls(); fireToast(res.message); }
                else { Swal.fire('Error', res.message, 'error'); }
            }).catch(e => console.error(e));
        }

        // --- INITIALIZATION ---
        window.onload = () => {
            loadNotes();
            loadPosts();
            loadPolls();
            if(IS_ADMIN) loadUsers();
            switchTab('tab-dashboard');
        };
    </script>
</body>
</html>
