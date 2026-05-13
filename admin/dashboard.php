<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'Dashboard Overview';

$stats = [
    'berita' => 0,
    'admin' => 0,
    'prodi' => 0,
    'alumni_testi' => 0,
    'alumni_form' => 0,
    'alumni_pending' => 0,
    'kerjasama' => 0,
    'fasilitas' => 0,
    'slides' => 0,
    'pesan' => 0,
    'pesan_unread' => 0,
];

$tables = [
    'berita' => "SELECT COUNT(*) as total FROM berita",
    'admin' => "SELECT COUNT(*) as total FROM users",
    'prodi' => "SELECT COUNT(*) as total FROM program_studi",
    'alumni_testi' => "SELECT COUNT(*) as total FROM alumni_testimoni",
    'fasilitas' => "SELECT COUNT(*) as total FROM fasilitas",
    'slides' => "SELECT COUNT(*) as total FROM konten_home WHERE bagian='hero_slider'",
];

foreach ($tables as $key => $sql) {
    try {
        $result = $conn->query($sql);
        if ($result) $stats[$key] = $result->fetch_assoc()['total'];
    } catch (Exception $e) {}
}

// New tables (might not exist yet)
try {
    $result = $conn->query("SELECT COUNT(*) as total FROM alumni_form");
    if ($result) $stats['alumni_form'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM alumni_form WHERE status='Pending'");
    if ($result) $stats['alumni_pending'] = $result->fetch_assoc()['total'];
} catch (Exception $e) {}

try {
    $result = $conn->query("SELECT COUNT(*) as total FROM kerjasama");
    if ($result) $stats['kerjasama'] = $result->fetch_assoc()['total'];
} catch (Exception $e) {}

try {
    $result = $conn->query("SELECT COUNT(*) as total FROM pesan_kontak");
    if ($result) $stats['pesan'] = $result->fetch_assoc()['total'];
    $result = $conn->query("SELECT COUNT(*) as total FROM pesan_kontak WHERE status='Belum Dibaca'");
    if ($result) $stats['pesan_unread'] = $result->fetch_assoc()['total'];
} catch (Exception $e) {}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-brand-navy">Selamat Datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?>!</h2>
            <p class="text-gray-500 mt-1">Ini adalah pusat kendali sistem website STIKES Mitra Ria Husada Jakarta.</p>
        </div>

        <?php if ($stats['alumni_pending'] > 0): ?>
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-amber-800">Ada <?= $stats['alumni_pending'] ?> pendaftaran alumni yang menunggu verifikasi!</p>
                <a href="kelola-alumni-form.php?status=Pending" class="text-sm text-amber-600 hover:text-amber-800 font-medium underline">Tinjau sekarang &rarr;</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($stats['pesan_unread'] > 0): ?>
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-blue-800"><?= $stats['pesan_unread'] ?> pesan baru belum dibaca!</p>
                <a href="kelola-pesan.php?status=Belum+Dibaca" class="text-sm text-blue-600 hover:text-blue-800 font-medium underline">Lihat pesan &rarr;</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Berita</p>
                    <h4 class="text-2xl font-bold text-gray-800"><?= $stats['berita'] ?></h4>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-teal-50 text-brand-teal flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Program Studi</p>
                    <h4 class="text-2xl font-bold text-gray-800"><?= $stats['prodi'] ?></h4>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Data Alumni Masuk</p>
                    <h4 class="text-2xl font-bold text-gray-800"><?= $stats['alumni_form'] ?></h4>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Admin Terdaftar</p>
                    <h4 class="text-2xl font-bold text-gray-800"><?= $stats['admin'] ?></h4>
                </div>
            </div>
        </div>

        <!-- Second Row of Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hero Slides</p>
                    <h4 class="text-xl font-bold text-gray-800"><?= $stats['slides'] ?></h4>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Fasilitas</p>
                    <h4 class="text-xl font-bold text-gray-800"><?= $stats['fasilitas'] ?></h4>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Alumni Testimoni</p>
                    <h4 class="text-xl font-bold text-gray-800"><?= $stats['alumni_testi'] ?></h4>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-cyan-50 text-cyan-500 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Mitra Kerjasama</p>
                    <h4 class="text-xl font-bold text-gray-800"><?= $stats['kerjasama'] ?></h4>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-500 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Pesan Masuk</p>
                    <h4 class="text-xl font-bold text-gray-800"><?= $stats['pesan'] ?></h4>
                </div>
            </div>
        </div>

        <!-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center max-w-3xl mx-auto mt-4">
            <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Sistem Database PHP Native Telah Aktif</h3>
            <p class="text-gray-500">Anda sekarang menggunakan sistem server-side (Backend). Data yang Anda simpan akan masuk ke dalam database MySQL dan bersifat permanen.</p>
        </div> -->

    </div>

<?php require_once 'includes/footer.php'; ?>
