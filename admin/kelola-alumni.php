<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'Kelola Alumni';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-alumni.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $nama_alumni = trim($_POST['nama_alumni']);
        $tahun_lulus = trim($_POST['tahun_lulus']);
        $prodi = trim($_POST['prodi']);
        $pekerjaan = trim($_POST['pekerjaan']);
        $testimoni = trim($_POST['testimoni']);
        $urutan = intval($_POST['urutan'] ?? 0);
        $status = trim($_POST['status'] ?? 'Publish');

        $foto = $_POST['old_foto'] ?? '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/alumni/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($_FILES['foto']['name']));
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fileName)) {
                $foto = $fileName;
            }
        }

        if (empty($id)) {
            // Auto-sequence shifting: if target position exists, shift existing items
            if ($urutan > 0) {
                $conn->begin_transaction();
                try {
                    $conn->query("UPDATE alumni_testimoni SET urutan = urutan + 1 WHERE urutan >= $urutan AND status='Publish'");
                    $stmt = $conn->prepare("INSERT INTO alumni_testimoni (nama_alumni, tahun_lulus, prodi_id, pesan_testimoni, pekerjaan_sekarang, foto, status, urutan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("siissssi", $nama_alumni, $tahun_lulus, $prodi, $testimoni, $pekerjaan, $foto, $status, $urutan);
                    if ($stmt->execute()) {
                        $conn->commit();
                        set_flash_message('success', 'Data alumni berhasil ditambahkan di urutan ' . $urutan . '.');
                    } else {
                        $conn->rollback();
                        set_flash_message('error', 'Gagal menambah data.');
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    $conn->rollback();
                    set_flash_message('error', 'Terjadi kesalahan: ' . $e->getMessage());
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO alumni_testimoni (nama_alumni, tahun_lulus, prodi_id, pesan_testimoni, pekerjaan_sekarang, foto, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("siissss", $nama_alumni, $tahun_lulus, $prodi, $testimoni, $pekerjaan, $foto, $status);
                if ($stmt->execute()) set_flash_message('success', 'Data alumni berhasil ditambahkan.');
                else set_flash_message('error', 'Gagal menambah data.');
                $stmt->close();
            }
        } else {
            // For updates, handle sequence shifting
            $old = $conn->query("SELECT urutan FROM alumni_testimoni WHERE id=$id")->fetch_assoc();
            if ($old && $old['urutan'] != $urutan) {
                $conn->begin_transaction();
                try {
                    if ($old['urutan'] < $urutan) {
                        $conn->query("UPDATE alumni_testimoni SET urutan = urutan - 1 WHERE urutan > {$old['urutan']} AND urutan <= $urutan AND id != $id");
                    } else {
                        $conn->query("UPDATE alumni_testimoni SET urutan = urutan + 1 WHERE urutan >= $urutan AND urutan < {$old['urutan']} AND id != $id");
                    }
                    $conn->commit();
                } catch (Exception $e) {
                    $conn->rollback();
                }
            }
            $stmt = $conn->prepare("UPDATE alumni_testimoni SET nama_alumni=?, tahun_lulus=?, prodi_id=?, pesan_testimoni=?, pekerjaan_sekarang=?, foto=?, status=?, urutan=? WHERE id=?");
            $stmt->bind_param("siisssssi", $nama_alumni, $tahun_lulus, $prodi, $testimoni, $pekerjaan, $foto, $status, $urutan, $id);
            if ($stmt->execute()) set_flash_message('success', 'Data alumni berhasil diperbarui.');
            else set_flash_message('error', 'Gagal memperbarui data.');
            $stmt->close();
        }
        header("Location: kelola-alumni.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $result = $conn->query("SELECT urutan FROM alumni_testimoni WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()) {
            $conn->query("UPDATE alumni_testimoni SET urutan = urutan - 1 WHERE urutan > {$row['urutan']}");
        }
        $stmt = $conn->prepare("DELETE FROM alumni_testimoni WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) set_flash_message('success', 'Data berhasil dihapus.');
        else set_flash_message('error', 'Gagal menghapus data.');
        $stmt->close();
        header("Location: kelola-alumni.php");
        exit;
    }
}

$search = $_GET['search'] ?? '';
$query = "SELECT a.*, p.nama_prodi as prodi FROM alumni_testimoni a LEFT JOIN program_studi p ON a.prodi_id = p.id WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND nama_alumni LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}
$query .= " ORDER BY urutan ASC, id DESC";

$stmt = $conn->prepare($query);
if (!empty($types)) $stmt->bind_param($types, ...$params);

$alumniList = [];
if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    $alumniList = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$prodi_list = [];
$res_prodi = $conn->query("SELECT id, nama_prodi, jenjang FROM program_studi ORDER BY jenjang, nama_prodi");
if ($res_prodi) $prodi_list = $res_prodi->fetch_all(MYSQLI_ASSOC);

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Daftar Testimoni Alumni</h2>
                <p class="text-sm text-gray-500">Kelola data lulusan dan testimoni mereka.</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg shadow font-medium text-sm">
                + Tambah Alumni
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <form method="GET" class="flex">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama alumni..." class="w-full md:w-64 px-4 py-2 border rounded-lg text-sm">
                    <button type="submit" class="ml-2 px-4 py-2 bg-brand-navy text-white rounded-lg text-sm">Cari</button>
                </form>
            </div>
<div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                        <tr>
                            <th class="px-6 py-4">Urutan</th>
                            <th class="px-6 py-4">Nama Alumni</th>
                            <th class="px-6 py-4">Prodi & Angkatan</th>
                            <th class="px-6 py-4">Instansi Pekerjaan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($alumniList)): ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data.</td></tr>
                        <?php else: foreach($alumniList as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-400"><?= $item['urutan'] ?: '-' ?></td>
                                <td class="px-6 py-4 font-semibold text-brand-navy"><?= htmlspecialchars($item['nama_alumni']) ?></td>
                                <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['prodi']) ?> (<?= htmlspecialchars($item['tahun_lulus']) ?>)</td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($item['pekerjaan_sekarang']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded font-semibold <?= $item['status']=='Publish'?'bg-green-100 text-green-700':'bg-amber-100 text-amber-700' ?>"><?= htmlspecialchars($item['status']) ?></span>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500 hover:underline">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Hapus data alumni ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:underline">Hapus</button>
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
        <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-lg">
            <div class="p-6 border-b flex justify-between">
                <h3 class="font-bold text-xl" id="modal-title">Form Testimoni</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <form id="crud-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">
                    <input type="hidden" name="old_foto" id="input-old-foto">

                    <div><label class="block text-sm mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_alumni" id="input-nama" required class="w-full px-4 py-2 border rounded-lg"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1">Tahun Lulus</label>
                        <input type="number" name="tahun_lulus" id="input-tahun" required class="w-full px-4 py-2 border rounded-lg"></div>
                        <div><label class="block text-sm mb-1">Program Studi</label>
                        <select name="prodi" id="input-prodi" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Pilih Program Studi</option>
                            <?php foreach($prodi_list as $pl): ?>
                                <option value="<?= $pl['id'] ?>"><?= htmlspecialchars($pl['jenjang'] . ' - ' . $pl['nama_prodi']) ?></option>
                            <?php endforeach; ?>
                        </select></div>
                    </div>

                    <div><label class="block text-sm mb-1">Pekerjaan Saat Ini</label>
                    <input type="text" name="pekerjaan" id="input-kerja" required class="w-full px-4 py-2 border rounded-lg"></div>

<div><label class="block text-sm mb-1">Foto (Opsional)</label>
                        <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 border rounded-lg"></div>

                    <div><label class="block text-sm mb-1">Testimoni</label>
                        <textarea name="testimoni" id="input-testi" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1">Urutan</label>
                            <input type="number" name="urutan" id="input-urutan" value="0" min="0" class="w-full px-4 py-2 border rounded-lg" placeholder="0 = otomatis di akhir">
                        </div>
                        <div><label class="block text-sm mb-1">Status</label>
                            <select name="status" id="input-status" class="w-full px-4 py-2 border rounded-lg">
                                <option value="Publish">Publish</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('crud-form').reset();
            document.getElementById('input-id').value = '';
            document.getElementById('modal-title').textContent = 'Tambah Alumni';
            document.getElementById('crud-modal').classList.remove('hidden');
            document.getElementById('crud-modal').classList.add('active');
        }
function editModal(data) {
            document.getElementById('input-id').value = data.id;
            document.getElementById('input-nama').value = data.nama_alumni;
            document.getElementById('input-tahun').value = data.tahun_lulus;
            document.getElementById('input-prodi').value = data.prodi_id;
            document.getElementById('input-kerja').value = data.pekerjaan_sekarang;
            document.getElementById('input-testi').value = data.pesan_testimoni;
            document.getElementById('input-old-foto').value = data.foto || '';
            document.getElementById('input-urutan').value = data.urutan || '';
            document.getElementById('input-status').value = data.status || 'Publish';
            document.getElementById('modal-title').textContent = 'Edit Alumni';
            document.getElementById('crud-modal').classList.remove('hidden');
            document.getElementById('crud-modal').classList.add('active');
        }
        function closeModal() {
            document.getElementById('crud-modal').classList.remove('active');
            setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300);
        }
    </script>
<?php require_once 'includes/footer.php'; ?>
