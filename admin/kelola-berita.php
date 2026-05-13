<?php

require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'Manajemen Berita';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-berita.php");
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id       = $_POST['id'] ?? '';
        $judul    = trim($_POST['judul']);
        $kategori = trim($_POST['kategori']);
        $status   = trim($_POST['status']);
        $konten   = trim($_POST['konten']);
        $slug     = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul), '-')) . (empty($id) ? '-' . time() : '');
        $penulis_id = $_SESSION['admin_id'] ?? 1;

        $thumbnail = $_POST['old_thumbnail'] ?? '';
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/berita/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['thumbnail']['name']));

            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadDir . $fileName)) {
                $thumbnail = $fileName;
            }
        }

        if (empty($id)) {

            $stmt = $conn->prepare("INSERT INTO berita (judul, slug, kategori, status, konten, gambar_thumbnail, penulis_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssssi", $judul, $slug, $kategori, $status, $konten, $thumbnail, $penulis_id);

            if ($stmt->execute()) {
                set_flash_message('success', 'Berita baru berhasil ditambahkan.');
            } else {
                set_flash_message('error', 'Gagal menambah berita: ' . $stmt->error);
            }
            $stmt->close();
        } else {

            $stmt = $conn->prepare("UPDATE berita SET judul=?, slug=?, kategori=?, status=?, konten=?, gambar_thumbnail=? WHERE id=?");
            $stmt->bind_param("ssssssi", $judul, $slug, $kategori, $status, $konten, $thumbnail, $id);

            if ($stmt->execute()) {
                set_flash_message('success', 'Data berita berhasil diperbarui.');
            } else {
                set_flash_message('error', 'Gagal memperbarui berita.');
            }
            $stmt->close();
        }

        header("Location: kelola-berita.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM berita WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            set_flash_message('success', 'Berita berhasil dihapus.');
        } else {
            set_flash_message('error', 'Gagal menghapus berita.');
        }
        $stmt->close();

        header("Location: kelola-berita.php");
        exit;
    }
}

$search = $_GET['search'] ?? '';
$kategoriFilter = $_GET['kategori'] ?? '';

$query = "SELECT * FROM berita WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND judul LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}
if ($kategoriFilter !== '') {
    $query .= " AND kategori = ?";
    $params[] = $kategoriFilter;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($types)) {

    $stmt->bind_param($types, ...$params);
}

$beritaList = [];
if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    $beritaList = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {

    set_flash_message('error', 'Tabel berita belum ada atau query gagal.');
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Berita</h2>
                <p class="text-sm text-gray-500">Kelola artikel, pengumuman, dan berita kampus.</p>
            </div>
            <button onclick="openModal()" class="inline-flex items-center px-4 py-2 bg-brand-teal hover:bg-brand-teal-dark text-white text-sm font-medium rounded-lg shadow transition-colors">
                + Tambah Berita Baru
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="p-4 border-b border-gray-100 bg-gray-50/30">
                <form method="GET" action="kelola-berita.php" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul..." class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-brand-teal outline-none">

                    <select name="kategori" class="w-full sm:w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="Akademik" <?= $kategoriFilter === 'Akademik' ? 'selected' : '' ?>>Akademik</option>
                        <option value="Prestasi" <?= $kategoriFilter === 'Prestasi' ? 'selected' : '' ?>>Prestasi</option>
                        <option value="Pengumuman" <?= $kategoriFilter === 'Pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-brand-navy text-white rounded-lg text-sm font-medium hover:bg-[#113a60]">Filter</button>

                    <?php if ($search || $kategoriFilter): ?>
                        <a href="kelola-berita.php" class="px-4 py-2 text-gray-500 hover:text-red-500 text-sm flex items-center">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-semibold">Judul Berita</th>
                            <th class="px-6 py-4 font-semibold">Kategori</th>
                            <th class="px-6 py-4 font-semibold">Tgl Publikasi</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($beritaList)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                    Tidak ada data berita yang ditemukan.
                                </td>
                            </tr>
                        <?php else: foreach($beritaList as $item): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900"><?= htmlspecialchars($item['judul']) ?></div>
                                    <?php if($item['gambar_thumbnail']): ?>
                                        <div class="text-[10px] text-gray-400 mt-1">Img: <?= htmlspecialchars($item['gambar_thumbnail']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 px-2.5 py-1 rounded-full text-xs font-medium text-gray-600">
                                        <?= htmlspecialchars($item['kategori']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?= date('d M Y', strtotime($item['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded text-xs font-semibold <?= $item['status'] === 'Publish' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-3">

                                        <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</button>

                                        <form method="POST" action="kelola-berita.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini secara permanen?');" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 text-sm text-gray-500">
                Menampilkan <?= count($beritaList) ?> data.
            </div>
        </div>
    </div>

    <div id="crud-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 backdrop-blur-sm p-4 hidden">
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-800" id="modal-title">Tambah Berita</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto">

                <form id="crud-form" method="POST" action="kelola-berita.php" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">
                    <input type="hidden" name="old_thumbnail" id="input-old-thumbnail">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="input-judul" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-teal focus:border-brand-teal outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori" id="input-kategori" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-brand-teal outline-none">
                                <option value="Akademik">Akademik</option>
                                <option value="Prestasi">Prestasi</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Kemahasiswaan">Kemahasiswaan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi</label>
                            <select name="status" id="input-status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-brand-teal outline-none">
                                <option value="Publish">Publish (Tampil)</option>
                                <option value="Draft">Draft (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail Image (Opsional)</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-teal/10 file:text-brand-teal hover:file:bg-brand-teal/20 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1" id="thumbnail-helper">Format: JPG, PNG. Maksimal 2MB.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Konten Berita</label>
                        <textarea name="konten" id="input-konten" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-brand-teal outline-none" placeholder="Tuliskan isi berita lengkap di sini..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t mt-4">
                        <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white font-medium rounded-lg shadow-md transition-colors">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('crud-modal');
        const form = document.getElementById('crud-form');

        function openModal() {
            form.reset();
            document.getElementById('input-id').value = '';
            document.getElementById('input-old-thumbnail').value = '';
            document.getElementById('thumbnail-helper').textContent = 'Format: JPG, PNG. Maksimal 2MB.';
            document.getElementById('modal-title').textContent = 'Tambah Berita Baru';

            modal.classList.remove('hidden');

            setTimeout(() => modal.classList.add('active'), 10);
        }

        function editModal(data) {
            document.getElementById('input-id').value = data.id;
            document.getElementById('input-judul').value = data.judul;
            document.getElementById('input-kategori').value = data.kategori;
            document.getElementById('input-status').value = data.status;
            document.getElementById('input-konten').value = data.konten;
            document.getElementById('input-old-thumbnail').value = data.gambar_thumbnail || '';

            if(data.gambar_thumbnail) {
                document.getElementById('thumbnail-helper').textContent = 'Gambar saat ini: ' + data.gambar_thumbnail + ' (Upload gambar baru untuk menggantinya)';
            }

            document.getElementById('modal-title').textContent = 'Edit Berita';

            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeModal() {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    </script>

<?php

require_once 'includes/footer.php';
?>
