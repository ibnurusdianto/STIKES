<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Pendataan Alumni';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Token!');
        header("Location: kelola-alumni-form.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = intval($_POST['id']);
        $status = trim($_POST['status']);
        $stmt = $conn->prepare("UPDATE alumni_form SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) set_flash_message('success', 'Status diperbarui.');
        $stmt->close();
        header("Location: kelola-alumni-form.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM alumni_form WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) set_flash_message('success', 'Data dihapus.');
        $stmt->close();
        header("Location: kelola-alumni-form.php");
        exit;
    }
}

$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT af.*, p.nama_prodi, p.jenjang FROM alumni_form af LEFT JOIN program_studi p ON af.prodi_id = p.id WHERE 1=1";
$params = [];
$types = "";

if ($statusFilter !== '') {
    $query .= " AND af.status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}
if ($search !== '') {
    $query .= " AND (af.nama_lengkap LIKE ? OR af.nim LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}
$query .= " ORDER BY af.created_at DESC";

$items = [];
try {
    $stmt = $conn->prepare($query);
    if (!empty($types)) $stmt->bind_param($types, ...$params);

    if ($stmt && $stmt->execute()) {
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    // Table may not exist yet - run migration first
}

// Stats
$statPending = 0; $statVerified = 0; $statRejected = 0;
foreach ($items as $i) {
    if ($i['status'] === 'Pending') $statPending++;
    elseif ($i['status'] === 'Verified') $statVerified++;
    elseif ($i['status'] === 'Rejected') $statRejected++;
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data Pendaftaran Alumni</h2>
            <p class="text-sm text-gray-500">Kelola dan verifikasi data alumni yang mendaftar melalui formulir website.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 font-bold text-lg"><?= $statPending ?></div>
                <div><p class="text-sm font-bold text-amber-700">Menunggu Verifikasi</p><p class="text-xs text-amber-500">Perlu ditinjau</p></div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-lg"><?= $statVerified ?></div>
                <div><p class="text-sm font-bold text-green-700">Terverifikasi</p><p class="text-xs text-green-500">Alumni valid</p></div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-lg"><?= $statRejected ?></div>
                <div><p class="text-sm font-bold text-red-700">Ditolak</p><p class="text-xs text-red-500">Data tidak valid</p></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Search & Filter -->
            <div class="p-4 border-b border-gray-100 bg-gray-50/30">
                <form method="GET" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama/NIM..." class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none">
                    <select name="status" class="w-full sm:w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white">
                        <option value="">Semua Status</option>
                        <option value="Pending" <?= $statusFilter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Verified" <?= $statusFilter === 'Verified' ? 'selected' : '' ?>>Verified</option>
                        <option value="Rejected" <?= $statusFilter === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-brand-navy text-white rounded-lg text-sm font-medium">Filter</button>
                    <?php if ($search || $statusFilter): ?>
                        <a href="kelola-alumni-form.php" class="px-4 py-2 text-gray-500 hover:text-red-500 text-sm flex items-center">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                        <tr>
                            <th class="px-4 py-3">Nama Lengkap</th>
                            <th class="px-4 py-3">NIM</th>
                            <th class="px-4 py-3">Program Studi</th>
                            <th class="px-4 py-3">Tahun Lulus</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal Daftar</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($items)): ?>
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data pendaftaran alumni.</td></tr>
                        <?php else: foreach($items as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-brand-navy"><?= htmlspecialchars($item['nama_lengkap']) ?></div>
                                    <div class="text-[10px] text-gray-400"><?= htmlspecialchars($item['email']) ?> | <?= htmlspecialchars($item['no_hp']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono"><?= htmlspecialchars($item['nim']) ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars(($item['jenjang'] ?? '') . ' ' . ($item['nama_prodi'] ?? '-')) ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($item['tahun_lulus']) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded font-semibold <?php
                                        if ($item['status'] === 'Verified') echo 'bg-green-100 text-green-700';
                                        elseif ($item['status'] === 'Rejected') echo 'bg-red-100 text-red-700';
                                        else echo 'bg-amber-100 text-amber-700';
                                    ?>">
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500"><?= date('d M Y', strtotime($item['created_at'])) ?></td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1 items-center">
                                        <button onclick='viewDetail(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500 hover:underline text-sm font-medium">Detail</button>
                                        <?php if ($item['status'] === 'Pending'): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="status" value="Verified">
                                            <button type="submit" class="text-green-500 hover:underline text-sm font-medium ml-2" title="Verifikasi">✓</button>
                                        </form>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="status" value="Rejected">
                                            <button type="submit" class="text-red-500 hover:underline text-sm font-medium ml-1" title="Tolak">✗</button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" onsubmit="return confirm('Hapus data ini?');" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="text-red-400 hover:underline text-xs ml-2">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 text-sm text-gray-500">
                Menampilkan <?= count($items) ?> data.
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detail-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 backdrop-blur-sm p-4 hidden">
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-6 border-b flex justify-between bg-gray-50 rounded-t-2xl">
                <h3 class="font-bold text-xl text-gray-800">Detail Data Alumni</h3>
                <button onclick="closeDetail()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto" id="detail-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <script>
    function viewDetail(d) {
        let html = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Nama Lengkap</p><p class="font-semibold text-brand-navy">${d.nama_lengkap}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">NIM</p><p class="font-mono font-semibold">${d.nim}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Program Studi</p><p>${(d.jenjang||'') + ' ' + (d.nama_prodi||'-')}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">No. Ijazah</p><p>${d.no_ijazah||'-'}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Tahun Masuk</p><p>${d.tahun_masuk}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Tahun Lulus</p><p>${d.tahun_lulus}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Jenis Kelamin</p><p>${d.jenis_kelamin}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">TTL</p><p>${(d.tempat_lahir||'-') + ', ' + (d.tanggal_lahir||'-')}</p></div>
                </div>
                <div class="border-t border-gray-100 pt-4 grid grid-cols-2 gap-4">
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Email</p><p>${d.email}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">No. HP</p><p>${d.no_hp}</p></div>
                    <div class="col-span-2"><p class="text-xs text-gray-400 uppercase font-bold">Alamat</p><p>${d.alamat_sekarang||'-'}</p></div>
                </div>
                <div class="border-t border-gray-100 pt-4 grid grid-cols-3 gap-4">
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Pekerjaan</p><p>${d.pekerjaan_sekarang||'-'}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Instansi</p><p>${d.instansi_kerja||'-'}</p></div>
                    <div><p class="text-xs text-gray-400 uppercase font-bold">Jabatan</p><p>${d.jabatan||'-'}</p></div>
                </div>
                ${d.pesan ? '<div class="border-t border-gray-100 pt-4"><p class="text-xs text-gray-400 uppercase font-bold mb-1">Pesan / Kesan</p><p class="text-sm text-gray-600 italic">"' + d.pesan + '"</p></div>' : ''}
                <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                    <span class="text-xs text-gray-400">Didaftarkan: ${d.created_at}</span>
                    <span class="px-3 py-1 text-xs rounded font-bold ${d.status==='Verified'?'bg-green-100 text-green-700':d.status==='Rejected'?'bg-red-100 text-red-700':'bg-amber-100 text-amber-700'}">${d.status}</span>
                </div>
            </div>
        `;
        document.getElementById('detail-content').innerHTML = html;
        document.getElementById('detail-modal').classList.remove('hidden');
        setTimeout(() => document.getElementById('detail-modal').classList.add('active'), 10);
    }
    function closeDetail() {
        document.getElementById('detail-modal').classList.remove('active');
        setTimeout(() => document.getElementById('detail-modal').classList.add('hidden'), 300);
    }
    </script>
<?php require_once 'includes/footer.php'; ?>
