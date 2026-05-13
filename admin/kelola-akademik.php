<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola Program Studi';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-akademik.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $nama_prodi = trim($_POST['nama_prodi']);
        $slug = strtolower(str_replace(' ', '-', $nama_prodi));
        $jenjang = trim($_POST['jenjang']);
        $akreditasi = trim($_POST['akreditasi']);
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $visi = trim($_POST['visi'] ?? '');
        $misi = trim($_POST['misi'] ?? '');
        $status = trim($_POST['status']);

        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO program_studi (nama_prodi, slug, jenjang, akreditasi, deskripsi, visi, misi, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $nama_prodi, $slug, $jenjang, $akreditasi, $deskripsi, $visi, $misi, $status);
            $stmt->execute();
            set_flash_message('success', 'Program Studi ditambahkan.');
        } else {
            $stmt = $conn->prepare("UPDATE program_studi SET nama_prodi=?, slug=?, jenjang=?, akreditasi=?, deskripsi=?, visi=?, misi=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssssi", $nama_prodi, $slug, $jenjang, $akreditasi, $deskripsi, $visi, $misi, $status, $id);
            $stmt->execute();
            set_flash_message('success', 'Program Studi diperbarui.');
        }
        header("Location: kelola-akademik.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM program_studi WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        set_flash_message('success', 'Program Studi dihapus.');
        header("Location: kelola-akademik.php");
        exit;
    }
}

$query = "SELECT * FROM program_studi ORDER BY id ASC";
$prodiList = [];
if ($result = $conn->query($query)) {
    $prodiList = $result->fetch_all(MYSQLI_ASSOC);
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Program Studi Akademik</h2>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg shadow text-sm font-medium">Tambah Prodi</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                    <tr><th class="px-6 py-4">Nama Prodi</th><th class="px-6 py-4">Jenjang</th><th class="px-6 py-4">Akreditasi</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($prodiList)): ?>
                        <tr><td colspan="5" class="text-center p-4">Tidak ada data</td></tr>
                    <?php else: foreach($prodiList as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-brand-navy"><?= htmlspecialchars($item['nama_prodi']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['jenjang']) ?></td>
                            <td class="px-6 py-4 text-sm font-bold text-brand-teal"><?= htmlspecialchars($item['akreditasi']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded font-semibold <?= $item['status']=='Publish'?'bg-green-100 text-green-700':'bg-amber-100 text-amber-700' ?>">
                                    <?= htmlspecialchars($item['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500">Edit</button>
                                <form method="POST" onsubmit="return confirm('Hapus prodi ini?');">
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
        <div class="modal-content bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex justify-between sticky top-0 bg-white z-10"><h3 class="font-bold text-xl" id="modal-title">Form Prodi</h3><button onclick="closeModal()">X</button></div>
            <div class="p-6">
                <form id="crud-form" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">

                    <div><label class="block text-sm mb-1 font-medium">Nama Prodi</label><input type="text" name="nama_prodi" id="input-nama" required class="w-full px-4 py-2 border rounded-lg"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1 font-medium">Jenjang</label><input type="text" name="jenjang" id="input-jenjang" required class="w-full px-4 py-2 border rounded-lg" placeholder="S1, D3, Profesi"></div>
                        <div><label class="block text-sm mb-1 font-medium">Akreditasi</label><input type="text" name="akreditasi" id="input-akreditasi" class="w-full px-4 py-2 border rounded-lg"></div>
                    </div>
                    <div><label class="block text-sm mb-1 font-medium">Deskripsi</label><textarea name="deskripsi" id="input-deskripsi" rows="3" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Deskripsi singkat program studi..."></textarea></div>
                    <div><label class="block text-sm mb-1 font-medium">Visi</label><textarea name="visi" id="input-visi" rows="2" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Visi program studi..."></textarea></div>
                    <div><label class="block text-sm mb-1 font-medium">Misi</label><textarea name="misi" id="input-misi" rows="4" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Satu poin misi per baris..."></textarea><p class="text-xs text-gray-400 mt-1">Pisahkan setiap poin misi dengan baris baru (Enter).</p></div>
                    <div><label class="block text-sm mb-1 font-medium">Status</label><select name="status" id="input-status" class="w-full px-4 py-2 border rounded-lg"><option value="Publish">Publish</option><option value="Draft">Draft</option></select></div>

                    <div class="flex justify-end gap-3 pt-4 border-t"><button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('crud-form').reset(); document.getElementById('input-id').value=''; document.getElementById('modal-title').textContent='Tambah Prodi'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function editModal(data) { document.getElementById('input-id').value=data.id; document.getElementById('input-nama').value=data.nama_prodi; document.getElementById('input-jenjang').value=data.jenjang; document.getElementById('input-akreditasi').value=data.akreditasi; document.getElementById('input-deskripsi').value=data.deskripsi||''; document.getElementById('input-visi').value=data.visi||''; document.getElementById('input-misi').value=data.misi||''; document.getElementById('input-status').value=data.status; document.getElementById('modal-title').textContent='Edit Prodi'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function closeModal() { document.getElementById('crud-modal').classList.remove('active'); setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300); }
    </script>
<?php require_once 'includes/footer.php'; ?>