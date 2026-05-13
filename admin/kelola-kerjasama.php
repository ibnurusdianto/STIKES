<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola Kerjasama';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Token!');
        header("Location: kelola-kerjasama.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $nama = trim($_POST['nama_institusi']);
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $jenis = trim($_POST['jenis_kerjasama'] ?? 'Umum');
        $status = trim($_POST['status'] ?? 'Publish');
        $urutan = intval($_POST['urutan'] ?? 0);

        $logo = $_POST['old_logo'] ?? '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/kerjasama/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['logo']['name']));
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName)) {
                $logo = $fileName;
            }
        }

        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO kerjasama (nama_institusi, logo, deskripsi, website, jenis_kerjasama, status, urutan) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $nama, $logo, $deskripsi, $website, $jenis, $status, $urutan);
            if ($stmt->execute()) set_flash_message('success', 'Kerjasama berhasil ditambahkan.');
            else set_flash_message('error', 'Gagal menambah data.');
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE kerjasama SET nama_institusi=?, logo=?, deskripsi=?, website=?, jenis_kerjasama=?, status=?, urutan=? WHERE id=?");
            $stmt->bind_param("ssssssii", $nama, $logo, $deskripsi, $website, $jenis, $status, $urutan, $id);
            if ($stmt->execute()) set_flash_message('success', 'Data kerjasama berhasil diperbarui.');
            else set_flash_message('error', 'Gagal memperbarui data.');
            $stmt->close();
        }
        header("Location: kelola-kerjasama.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM kerjasama WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) set_flash_message('success', 'Data kerjasama dihapus.');
        $stmt->close();
        header("Location: kelola-kerjasama.php");
        exit;
    }
}

$items = [];
if ($result = $conn->query("SELECT * FROM kerjasama ORDER BY urutan ASC, id DESC")) {
    $items = $result->fetch_all(MYSQLI_ASSOC);
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Kerjasama & Kemitraan</h2>
                <p class="text-sm text-gray-500">Kelola data universitas dan institusi mitra kerjasama.</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg shadow font-medium text-sm">+ Tambah Mitra</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                        <tr>
                            <th class="px-6 py-4">Urutan</th>
                            <th class="px-6 py-4">Nama Institusi</th>
                            <th class="px-6 py-4">Jenis Kerjasama</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($items)): ?>
                            <tr><td colspan="5" class="px-6 py-6 text-center text-gray-500">Tidak ada data mitra.</td></tr>
                        <?php else: foreach($items as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-400"><?= $item['urutan'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if($item['logo']): ?>
                                            <img src="../uploads/kerjasama/<?= htmlspecialchars($item['logo']) ?>" class="w-10 h-10 rounded object-contain bg-gray-50 border p-0.5">
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-semibold text-brand-navy"><?= htmlspecialchars($item['nama_institusi']) ?></div>
                                            <?php if($item['website']): ?>
                                                <div class="text-[10px] text-gray-400 truncate max-w-[200px]"><?= htmlspecialchars($item['website']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600"><?= htmlspecialchars($item['jenis_kerjasama']) ?></span></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded font-semibold <?= $item['status']=='Publish'?'bg-green-100 text-green-700':'bg-amber-100 text-amber-700' ?>"><?= htmlspecialchars($item['status']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500 hover:underline text-sm font-medium">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Hapus mitra ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:underline text-sm font-medium">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="crud-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 backdrop-blur-sm p-4 hidden">
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex justify-between bg-gray-50 rounded-t-2xl sticky top-0 z-10">
                <h3 class="font-bold text-xl" id="modal-title">Form Kerjasama</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>
            <div class="p-6">
                <form id="crud-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">
                    <input type="hidden" name="old_logo" id="input-old-logo">

                    <div><label class="block text-sm mb-1 font-semibold text-gray-700">Nama Institusi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_institusi" id="input-nama" required class="w-full px-4 py-2 border rounded-lg" placeholder="cth: Universitas Indonesia"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1 font-semibold text-gray-700">Jenis Kerjasama</label>
                        <select name="jenis_kerjasama" id="input-jenis" class="w-full px-4 py-2 border rounded-lg">
                            <option value="Pendidikan">Pendidikan</option>
                            <option value="Penelitian">Penelitian</option>
                            <option value="Pengabdian">Pengabdian Masyarakat</option>
                            <option value="Magang/Praktik">Magang / Praktik Klinik</option>
                            <option value="Umum">Umum / MoU</option>
                        </select></div>
                        <div><label class="block text-sm mb-1 font-semibold text-gray-700">Status</label>
                        <select name="status" id="input-status" class="w-full px-4 py-2 border rounded-lg">
                            <option value="Publish">Publish</option>
                            <option value="Draft">Draft</option>
                        </select></div>
                    </div>

                    <div><label class="block text-sm mb-1 font-semibold text-gray-700">Website</label>
                    <input type="url" name="website" id="input-website" class="w-full px-4 py-2 border rounded-lg" placeholder="https://www.example.ac.id"></div>

                    <div><label class="block text-sm mb-1 font-semibold text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" id="input-deskripsi" rows="3" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Deskripsi singkat kerjasama..."></textarea></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1 font-semibold text-gray-700">Urutan</label>
                        <input type="number" name="urutan" id="input-urutan" value="0" class="w-full px-4 py-2 border rounded-lg"></div>
                        <div><label class="block text-sm mb-1 font-semibold text-gray-700">Logo Institusi</label>
                        <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border rounded-lg text-sm"></div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t"><button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('crud-form').reset(); document.getElementById('input-id').value=''; document.getElementById('input-old-logo').value=''; document.getElementById('modal-title').textContent='Tambah Mitra Kerjasama'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function editModal(data) { document.getElementById('input-id').value=data.id; document.getElementById('input-nama').value=data.nama_institusi; document.getElementById('input-jenis').value=data.jenis_kerjasama; document.getElementById('input-status').value=data.status; document.getElementById('input-website').value=data.website||''; document.getElementById('input-deskripsi').value=data.deskripsi||''; document.getElementById('input-urutan').value=data.urutan; document.getElementById('input-old-logo').value=data.logo||''; document.getElementById('modal-title').textContent='Edit Kerjasama'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function closeModal() { document.getElementById('crud-modal').classList.remove('active'); setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300); }
    </script>
<?php require_once 'includes/footer.php'; ?>
