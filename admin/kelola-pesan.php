<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Pesan Masuk';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Token!');
        header("Location: kelola-pesan.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'update_status') {
            $id = intval($_POST['id']);
            $status = trim($_POST['status']);
            $stmt = $conn->prepare("UPDATE pesan_kontak SET status=? WHERE id=?");
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) set_flash_message('success', 'Status pesan diperbarui.');
            $stmt->close();
            header("Location: kelola-pesan.php");
            exit;
        }

        if ($action === 'delete') {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("DELETE FROM pesan_kontak WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) set_flash_message('success', 'Pesan dihapus.');
            $stmt->close();
            header("Location: kelola-pesan.php");
            exit;
        }
    } catch (Exception $e) {
        set_flash_message('error', 'Gagal: ' . $e->getMessage() . ' — Jalankan migrasi database terlebih dahulu.');
        header("Location: kelola-pesan.php");
        exit;
    }
}

$statusFilter = $_GET['status'] ?? '';
$items = [];
try {
    $query = "SELECT * FROM pesan_kontak";
    if ($statusFilter !== '') {
        $query .= " WHERE status = '" . $conn->real_escape_string($statusFilter) . "'";
    }
    $query .= " ORDER BY created_at DESC";
    $result = $conn->query($query);
    if ($result) $items = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {}

// Stats
$statUnread = 0; $statRead = 0; $statReplied = 0;
foreach ($items as $i) {
    $st = $i['status'] ?? '';
    if ($st === 'Belum Dibaca') $statUnread++;
    elseif ($st === 'Dibaca') $statRead++;
    elseif ($st === 'Dibalas') $statReplied++;
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pesan Masuk dari Kontak</h2>
            <p class="text-sm text-gray-500">Pesan yang dikirim pengunjung melalui formulir "Tinggalkan Pesan" di halaman Kontak.</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 font-bold text-lg"><?= $statUnread ?></div>
                <div><p class="text-sm font-bold text-amber-700">Belum Dibaca</p><p class="text-xs text-amber-500">Perlu ditinjau</p></div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg"><?= $statRead ?></div>
                <div><p class="text-sm font-bold text-blue-700">Sudah Dibaca</p><p class="text-xs text-blue-500">Menunggu tindak lanjut</p></div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-lg"><?= $statReplied ?></div>
                <div><p class="text-sm font-bold text-green-700">Dibalas</p><p class="text-xs text-green-500">Selesai</p></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Filter -->
            <div class="p-4 border-b border-gray-100 bg-gray-50/30">
                <div class="flex gap-2">
                    <a href="kelola-pesan.php" class="px-3 py-1.5 rounded-lg text-sm font-medium <?= $statusFilter === '' ? 'bg-brand-navy text-white' : 'text-gray-600 hover:bg-gray-100' ?>">Semua</a>
                    <a href="kelola-pesan.php?status=Belum+Dibaca" class="px-3 py-1.5 rounded-lg text-sm font-medium <?= $statusFilter === 'Belum Dibaca' ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">Belum Dibaca</a>
                    <a href="kelola-pesan.php?status=Dibaca" class="px-3 py-1.5 rounded-lg text-sm font-medium <?= $statusFilter === 'Dibaca' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">Dibaca</a>
                    <a href="kelola-pesan.php?status=Dibalas" class="px-3 py-1.5 rounded-lg text-sm font-medium <?= $statusFilter === 'Dibalas' ? 'bg-green-500 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">Dibalas</a>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                <?php if(empty($items)): ?>
                    <div class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Tidak ada pesan masuk.
                    </div>
                <?php else: foreach($items as $item): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors <?= ($item['status'] ?? '') === 'Belum Dibaca' ? 'bg-amber-50/30 border-l-4 border-l-amber-400' : '' ?>">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-9 h-9 rounded-full bg-brand-navy flex items-center justify-center text-white font-bold text-sm shrink-0">
                                        <?= strtoupper(substr($item['nama_pengirim'] ?? '?', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800"><?= htmlspecialchars($item['nama_pengirim'] ?? '') ?></h4>
                                        <p class="text-xs text-gray-400"><?= htmlspecialchars($item['email_pengirim'] ?? '') ?> · <?= date('d M Y, H:i', strtotime($item['created_at'] ?? 'now')) ?></p>
                                    </div>
                                </div>
                                <?php if (!empty($item['subjek'])): ?>
                                    <p class="text-sm font-semibold text-brand-navy mb-1 ml-12"><?= htmlspecialchars($item['subjek']) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-600 leading-relaxed ml-12"><?= nl2br(htmlspecialchars($item['pesan'] ?? '')) ?></p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-12 md:ml-0">
                                <span class="px-2.5 py-1 text-xs rounded-full font-semibold <?php
                                    $itemStatus = $item['status'] ?? '';
                                    if ($itemStatus === 'Belum Dibaca') echo 'bg-amber-100 text-amber-700';
                                    elseif ($itemStatus === 'Dibaca') echo 'bg-blue-100 text-blue-700';
                                    else echo 'bg-green-100 text-green-700';
                                ?>"><?= htmlspecialchars($item['status'] ?? 'Belum Dibaca') ?></span>

                                <!-- Status change dropdown -->
                                <div class="relative group">
                                    <button class="text-gray-400 hover:text-gray-600 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                    </button>
                                    <div class="absolute right-0 top-full mt-1 bg-white rounded-lg shadow-xl border border-gray-100 py-2 w-48 hidden group-hover:block z-20">
                                        <?php if (($item['status'] ?? '') !== 'Dibaca'): ?>
                                        <form method="POST" class="px-2 py-1">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="status" value="Dibaca">
                                            <button type="submit" class="w-full text-left px-3 py-1.5 rounded text-sm text-blue-600 hover:bg-blue-50">✓ Tandai Dibaca</button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if (($item['status'] ?? '') !== 'Dibalas'): ?>
                                        <form method="POST" class="px-2 py-1">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="status" value="Dibalas">
                                            <button type="submit" class="w-full text-left px-3 py-1.5 rounded text-sm text-green-600 hover:bg-green-50">✓ Tandai Dibalas</button>
                                        </form>
                                        <?php endif; ?>
                                        <a href="mailto:<?= htmlspecialchars($item['email_pengirim'] ?? '') ?>?subject=Re: <?= urlencode($item['subjek'] ?? 'Pesan dari STIKES MRHJ') ?>" class="block px-5 py-1.5 text-sm text-gray-600 hover:bg-gray-50">✉ Balas via Email</a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form method="POST" onsubmit="return confirm('Hapus pesan ini?');" class="px-2 py-1">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="w-full text-left px-3 py-1.5 rounded text-sm text-red-500 hover:bg-red-50">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="p-4 border-t border-gray-100 text-sm text-gray-500">
                Menampilkan <?= count($items) ?> pesan.
            </div>
        </div>
    </div>
<?php require_once 'includes/footer.php'; ?>
