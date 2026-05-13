<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola Fasilitas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-fasilitas.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $nama = trim($_POST['nama']);
        $kategori = trim($_POST['kategori']);
        $status = trim($_POST['status']);
        $deskripsi = trim($_POST['deskripsi']);

        $gambar = $_POST['old_gambar'] ?? '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/fasilitas/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['gambar']['name']);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $fileName)) {
                $gambar = $fileName;
            }
        }

        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO fasilitas (nama_fasilitas, deskripsi_singkat, icon_class, gambar, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nama, $deskripsi, $kategori, $gambar, $status);
            if ($stmt->execute()) set_flash_message('success', 'Fasilitas ditambahkan.');
            else set_flash_message('error', 'Gagal menambah fasilitas.');
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE fasilitas SET nama_fasilitas=?, deskripsi_singkat=?, icon_class=?, gambar=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $nama, $deskripsi, $kategori, $gambar, $status, $id);
            if ($stmt->execute()) set_flash_message('success', 'Fasilitas diperbarui.');
            else set_flash_message('error', 'Gagal memperbarui fasilitas.');
            $stmt->close();
        }
        header("Location: kelola-fasilitas.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM fasilitas WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) set_flash_message('success', 'Fasilitas dihapus.');
        else set_flash_message('error', 'Gagal menghapus fasilitas.');
        $stmt->close();
        header("Location: kelola-fasilitas.php");
        exit;
    }
}

$query = "SELECT * FROM fasilitas ORDER BY id DESC";
$fasilitasList = [];
if ($result = $conn->query($query)) {
    $fasilitasList = $result->fetch_all(MYSQLI_ASSOC);
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Katalog Sarana Prasarana</h2>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg shadow text-sm font-medium">Tambah Fasilitas</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                    <tr><th class="px-6 py-4">Nama Fasilitas</th><th class="px-6 py-4">Kategori</th><th class="px-6 py-4">Kondisi</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($fasilitasList)): ?>
                        <tr><td colspan="4" class="text-center p-4">Tidak ada data</td></tr>
                    <?php else: foreach($fasilitasList as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-brand-navy"><?= htmlspecialchars($item['nama_fasilitas']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['icon_class']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['status']) ?></td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500">Edit</button>
                                <form method="POST" onsubmit="return confirm('Hapus?');">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="text-red-500">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="crud-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 p-4 hidden">
        <div class="modal-content bg-white rounded-xl w-full max-w-lg">
            <div class="p-6 border-b flex justify-between"><h3 class="font-bold text-xl" id="modal-title">Form Fasilitas</h3><button onclick="closeModal()">X</button></div>
            <div class="p-6">
                <form id="crud-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">
                    <input type="hidden" name="old_gambar" id="input-old-gambar">

                    <div><label class="block text-sm mb-1">Nama Fasilitas</label>
                    <input type="text" name="nama" id="input-nama" required class="w-full px-4 py-2 border rounded-lg"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1">Kategori</label>
                        <select name="kategori" id="input-kategori" class="w-full px-4 py-2 border rounded-lg"><option value="Laboratorium">Laboratorium</option><option value="Ruang Kuliah">Ruang Kuliah</option><option value="Perpustakaan">Perpustakaan</option><option value="Lainnya">Lainnya</option></select></div>

                        <div><label class="block text-sm mb-1">Kondisi</label>
                        <select name="status" id="input-status" class="w-full px-4 py-2 border rounded-lg"><option value="Publish">Publish</option><option value="Draft">Draft</option></select></div>
                    </div>

                    <div><label class="block text-sm mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="input-deskripsi" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea></div>

                    <div><label class="block text-sm mb-1">Gambar</label>
                    <input type="file" name="gambar" class="w-full px-4 py-2 border rounded-lg"></div>

                    <div class="flex justify-end gap-3 pt-4 border-t"><button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('crud-form').reset(); document.getElementById('input-id').value=''; document.getElementById('modal-title').textContent='Tambah Fasilitas'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function editModal(data) { document.getElementById('input-id').value=data.id; document.getElementById('input-nama').value=data.nama_fasilitas; document.getElementById('input-kategori').value=data.icon_class; document.getElementById('input-status').value=data.status; document.getElementById('input-deskripsi').value=data.deskripsi_singkat; document.getElementById('modal-title').textContent='Edit Fasilitas'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function closeModal() { document.getElementById('crud-modal').classList.remove('active'); setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300); }
    </script>
<?php require_once 'includes/footer.php'; ?>
