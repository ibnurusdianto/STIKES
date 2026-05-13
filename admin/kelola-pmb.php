<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola PMB';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Token!');
        header("Location: kelola-pmb.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $gelombang = trim($_POST['gelombang']);
        $mulai = trim($_POST['tanggal_mulai']); $selesai = trim($_POST['tanggal_selesai']);
        $status = trim($_POST['status']);

        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO pmb_jadwal (nama_gelombang, tanggal_mulai, tanggal_selesai, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $gelombang, $mulai, $selesai, $status);
            $stmt->execute();
            set_flash_message('success', 'Jadwal ditambahkan.');
        } else {
            $stmt = $conn->prepare("UPDATE pmb_jadwal SET nama_gelombang=?, tanggal_mulai=?, tanggal_selesai=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $gelombang, $mulai, $selesai, $status, $id);
            $stmt->execute();
            set_flash_message('success', 'Jadwal diperbarui.');
        }
        header("Location: kelola-pmb.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM pmb_jadwal WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        set_flash_message('success', 'Jadwal dihapus.');
        header("Location: kelola-pmb.php");
        exit;
    }

    if ($action === 'save_kontak_pmb') {
        $nama_pic = trim($_POST['nama_pic']);
        $no_telp = trim($_POST['no_telp']);
        $no_wa = trim($_POST['no_whatsapp']);
        $email = trim($_POST['email']);
        $alamat = trim($_POST['alamat_sekretariat']);
        $jam = trim($_POST['jam_operasional']);
        $link = trim($_POST['link_pendaftaran']);
        $pesan = trim($_POST['pesan_tambahan']);

        $cek = $conn->query("SELECT id FROM kontak_pmb WHERE id=1");
        if ($cek && $cek->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE kontak_pmb SET nama_pic=?, no_telp=?, no_whatsapp=?, email=?, alamat_sekretariat=?, jam_operasional=?, link_pendaftaran=?, pesan_tambahan=? WHERE id=1");
            $stmt->bind_param("ssssssss", $nama_pic, $no_telp, $no_wa, $email, $alamat, $jam, $link, $pesan);
        } else {
            $stmt = $conn->prepare("INSERT INTO kontak_pmb (id, nama_pic, no_telp, no_whatsapp, email, alamat_sekretariat, jam_operasional, link_pendaftaran, pesan_tambahan) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $nama_pic, $no_telp, $no_wa, $email, $alamat, $jam, $link, $pesan);
        }
        if ($stmt->execute()) set_flash_message('success', 'Info Kontak PMB berhasil diperbarui.');
        $stmt->close();
        header("Location: kelola-pmb.php");
        exit;
    }
}

$query = "SELECT * FROM pmb_jadwal ORDER BY id ASC";
$jadwalList = [];
if ($result = $conn->query($query)) {
    $jadwalList = $result->fetch_all(MYSQLI_ASSOC);
}

// Get PMB contact info
$kontakPMB = ['nama_pic' => '', 'no_telp' => '', 'no_whatsapp' => '', 'email' => '', 'alamat_sekretariat' => '', 'jam_operasional' => '', 'link_pendaftaran' => '', 'pesan_tambahan' => ''];
try {
    $res_pmb = $conn->query("SELECT * FROM kontak_pmb WHERE id=1");
    if ($res_pmb && $res_pmb->num_rows > 0) {
        $kontakPMB = array_merge($kontakPMB, $res_pmb->fetch_assoc());
    }
} catch (Exception $e) {
    // Table may not exist yet - will be created by migration
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Jadwal Pendaftaran Mahasiswa Baru</h2>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg shadow text-sm font-medium">Tambah Gelombang</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                    <tr><th class="px-6 py-4">Gelombang / Jalur</th><th class="px-6 py-4">Periode Pendaftaran</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($jadwalList)): ?>
                        <tr><td colspan="4" class="text-center p-4">Tidak ada data</td></tr>
                    <?php else: foreach($jadwalList as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-brand-navy"><?= htmlspecialchars($item['nama_gelombang']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['tanggal_mulai'] . " s/d " . $item['tanggal_selesai']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded font-semibold <?= $item['status']=='Buka'?'bg-green-100 text-green-700':'bg-red-100 text-red-700' ?>">
                                    <?= htmlspecialchars($item['status']) ?>
                                </span>
                            </td>
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

        <!-- PMB Contact Info Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8">
            <div class="p-4 bg-gray-50 border-b">
                <h3 class="font-bold text-gray-700">Informasi Kontak Panitia PMB</h3>
                <p class="text-xs text-gray-500 mt-1">Data ini akan muncul pada modal "Hubungi Panitia PMB" di halaman PMB publik.</p>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save_kontak_pmb">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Nama PIC / Panitia</label>
                            <input type="text" name="nama_pic" value="<?= htmlspecialchars($kontakPMB['nama_pic']) ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="cth: Panitia PMB STIKES MRHJ">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Email PMB</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($kontakPMB['email']) ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="pmb@smrhj.ac.id">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">No. Telepon</label>
                            <input type="text" name="no_telp" value="<?= htmlspecialchars($kontakPMB['no_telp']) ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="(021) xxxx-xxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">No. WhatsApp</label>
                            <input type="text" name="no_whatsapp" value="<?= htmlspecialchars($kontakPMB['no_whatsapp']) ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="08xx-xxxx-xxxx">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Alamat Sekretariat Pendaftaran</label>
                        <textarea name="alamat_sekretariat" rows="2" class="w-full px-4 py-2 border rounded-lg text-sm"><?= htmlspecialchars($kontakPMB['alamat_sekretariat']) ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Jam Operasional</label>
                            <input type="text" name="jam_operasional" value="<?= htmlspecialchars($kontakPMB['jam_operasional']) ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="Senin - Jumat, 08:00 - 16:00 WIB">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Link Pendaftaran Online</label>
                            <input type="url" name="link_pendaftaran" value="<?= htmlspecialchars($kontakPMB['link_pendaftaran']) ?>" class="w-full px-4 py-2 border rounded-lg" placeholder="https://...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Pesan Tambahan (opsional)</label>
                        <textarea name="pesan_tambahan" rows="2" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Informasi tambahan yang ingin ditampilkan..."><?= htmlspecialchars($kontakPMB['pesan_tambahan']) ?></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-brand-navy hover:bg-[#113a60] text-white rounded-lg font-medium transition-colors">Simpan Info Kontak PMB</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="crud-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 p-4 hidden">
        <div class="modal-content bg-white rounded-xl w-full max-w-md">
            <div class="p-6 border-b flex justify-between"><h3 class="font-bold text-xl" id="modal-title">Form Jadwal</h3><button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button></div>
            <div class="p-6">
                <form id="crud-form" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">

                    <div><label class="block text-sm mb-1">Nama Gelombang</label>
                    <input type="text" name="gelombang" id="input-gelombang" required class="w-full px-4 py-2 border rounded-lg"></div>

                    <div><label class="block text-sm mb-1">Periode (Tanggal Mulai - Selesai)</label>
                    <div class="flex gap-2"><input type="date" name="tanggal_mulai" id="input-mulai" required class="w-full px-4 py-2 border rounded-lg"><input type="date" name="tanggal_selesai" id="input-selesai" required class="w-full px-4 py-2 border rounded-lg"></div></div>

                    <div><label class="block text-sm mb-1">Status Pendaftaran</label>
                    <select name="status" id="input-status" class="w-full px-4 py-2 border rounded-lg"><option value="Buka">Buka (Aktif)</option><option value="Tutup">Tutup</option></select></div>

                    <div class="flex justify-end gap-3 pt-4 border-t"><button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('crud-form').reset(); document.getElementById('input-id').value=''; document.getElementById('modal-title').textContent='Tambah Gelombang'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function editModal(data) { document.getElementById('input-id').value=data.id; document.getElementById('input-gelombang').value=data.nama_gelombang; document.getElementById('input-mulai').value=data.tanggal_mulai; document.getElementById('input-selesai').value=data.tanggal_selesai; document.getElementById('input-status').value=data.status; document.getElementById('modal-title').textContent='Edit Gelombang'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function closeModal() { document.getElementById('crud-modal').classList.remove('active'); setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300); }
    </script>
<?php require_once 'includes/footer.php'; ?>
