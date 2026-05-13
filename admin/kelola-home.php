<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola Home';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-home.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $judul = trim($_POST['judul']);
        $subjudul = trim($_POST['subjudul']);
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $teks_tombol = trim($_POST['teks_tombol']);
        $link_tombol = trim($_POST['link_tombol']);
        $urutan = intval($_POST['urutan']);

        $gambar = $_POST['old_gambar'] ?? '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/home/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['gambar']['name']);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $fileName)) {
                $gambar = $fileName;
            }
        }

        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO konten_home (bagian, judul, subjudul, deskripsi, gambar_background, teks_tombol, link_tombol, urutan) VALUES ('hero_slider', ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $judul, $subjudul, $deskripsi, $gambar, $teks_tombol, $link_tombol, $urutan);
            if ($stmt->execute()) set_flash_message('success', 'Slide berhasil ditambahkan.');
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE konten_home SET judul=?, subjudul=?, deskripsi=?, gambar_background=?, teks_tombol=?, link_tombol=?, urutan=? WHERE id=?");
            $stmt->bind_param("ssssssii", $judul, $subjudul, $deskripsi, $gambar, $teks_tombol, $link_tombol, $urutan, $id);
            if ($stmt->execute()) set_flash_message('success', 'Slide berhasil diperbarui.');
            $stmt->close();
        }
        header("Location: kelola-home.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM konten_home WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) set_flash_message('success', 'Slide dihapus.');
        $stmt->close();
        header("Location: kelola-home.php");
        exit;
    }

    if ($action === 'save_sekilas') {
        $judul_sekilas = trim($_POST['judul_sekilas']);
        $konten_sekilas = trim($_POST['konten_sekilas']);

        $cek = $conn->query("SELECT id FROM konten_home WHERE bagian='sekilas_tentang' LIMIT 1");
        if ($cek && $cek->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE konten_home SET judul=?, subjudul=? WHERE bagian='sekilas_tentang'");
            $stmt->bind_param("ss", $judul_sekilas, $konten_sekilas);
            if ($stmt->execute()) set_flash_message('success', 'Sekilas Tentang Kami berhasil diperbarui.');
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO konten_home (bagian, judul, subjudul, status) VALUES ('sekilas_tentang', ?, ?, 'Publish')");
            $stmt->bind_param("ss", $judul_sekilas, $konten_sekilas);
            if ($stmt->execute()) set_flash_message('success', 'Sekilas Tentang Kami berhasil disimpan.');
            $stmt->close();
        }
        header("Location: kelola-home.php");
        exit;
    }
}

$query = "SELECT * FROM konten_home WHERE bagian='hero_slider' ORDER BY urutan ASC, id DESC";
$slides = [];
if ($result = $conn->query($query)) {
    $slides = $result->fetch_all(MYSQLI_ASSOC);
}

$sekilas = [];
$res_sekilas = $conn->query("SELECT * FROM konten_home WHERE bagian='sekilas_tentang' LIMIT 1");
if ($res_sekilas && $res_sekilas->num_rows > 0) $sekilas = $res_sekilas->fetch_assoc();

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Hero Carousel Homepage</h2>
                <p class="text-sm text-gray-500">Kelola slider gambar utama pada beranda website.</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg h-fit text-sm font-medium">Tambah Slide</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                    <tr><th class="px-6 py-4">Urutan</th><th class="px-6 py-4">Judul Utama</th><th class="px-6 py-4">Sub Judul</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($slides)): ?>
                        <tr><td colspan="4" class="text-center p-4">Belum ada slide</td></tr>
                    <?php else: foreach($slides as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold"><?= $item['urutan'] ?></td>
                            <td class="px-6 py-4 font-bold text-brand-navy"><?= htmlspecialchars($item['judul']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($item['subjudul']) ?></td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500">Edit</button>
                                <form method="POST" onsubmit="return confirm('Hapus slide?');">
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

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8">
            <div class="p-4 bg-gray-50 border-b">
                <h3 class="font-bold text-gray-700">Sekilas Tentang Kami (Teks di Beranda)</h3>
                <p class="text-xs text-gray-500 mt-1">Teks ini akan muncul di bagian "Sekilas Tentang Kami" pada halaman Beranda.</p>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save_sekilas">
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul Bagian</label>
                        <input type="text" name="judul_sekilas" value="<?= htmlspecialchars($sekilas['judul'] ?? 'Sekilas Tentang Kami') ?>" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Konten Singkat</label>
                        <textarea name="konten_sekilas" rows="5" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Masukkan deskripsi singkat tentang STIKES Mitra Ria Husada Jakarta..."><?= htmlspecialchars($sekilas['subjudul'] ?? '') ?></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-brand-navy hover:bg-[#113a60] text-white rounded-lg font-medium transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="crud-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 p-4 hidden">
        <div class="modal-content bg-white rounded-xl w-full max-w-lg">
            <div class="p-6 border-b flex justify-between"><h3 class="font-bold text-xl" id="modal-title">Form Slide</h3><button onclick="closeModal()">X</button></div>
            <div class="p-6">
                <form id="crud-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">
                    <input type="hidden" name="old_gambar" id="input-old-gambar">

                    <div><label class="block text-sm mb-1">Judul Besar</label><input type="text" name="judul" id="input-judul" required class="w-full px-4 py-2 border rounded-lg"></div>
                    <div><label class="block text-sm mb-1">Sub Judul</label><input type="text" name="subjudul" id="input-subjudul" required class="w-full px-4 py-2 border rounded-lg"></div>
                    <div><label class="block text-sm mb-1">Deskripsi</label><textarea name="deskripsi" id="input-deskripsi" rows="3" class="w-full px-4 py-2 border rounded-lg text-sm" placeholder="Deskripsi tambahan untuk slide ini (opsional)..."></textarea></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm mb-1">Teks Tombol</label><input type="text" name="teks_tombol" id="input-teks-tombol" class="w-full px-4 py-2 border rounded-lg" placeholder="cth: Jelajahi"></div>
                        <div><label class="block text-sm mb-1">Link Tombol (URL)</label><input type="text" name="link_tombol" id="input-link-tombol" class="w-full px-4 py-2 border rounded-lg" placeholder="cth: akademik.php" list="url-suggestions">
                        <datalist id="url-suggestions">
                            <option value="home.php">Beranda</option>
                            <option value="akademik.php">Akademik</option>
                            <option value="berita.php">Berita</option>
                            <option value="tentang-kami.php">Tentang Kami</option>
                            <option value="pmb.php">Pendaftaran Mahasiswa Baru</option>
                            <option value="fasilitas.php">Fasilitas</option>
                            <option value="alumni.php">Alumni</option>
                            <option value="kontak.php">Kontak</option>
                        </datalist>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Urutan</label><input type="number" name="urutan" id="input-urutan" value="1" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div><label class="block text-sm mb-1">Gambar Slide</label><input type="file" name="gambar" class="w-full px-4 py-2 border rounded-lg"></div>

                    <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Auto-fill mapping: button text keywords -> suggested URLs
        const autoFillMap = {
            'pmb': 'pmb.php',
            'pendaftaran': 'pmb.php',
            'daftar': 'pmb.php',
            'akademik': 'akademik.php',
            'prodi': 'akademik.php',
            'program studi': 'akademik.php',
            'berita': 'berita.php',
            'informasi': 'berita.php',
            'fasilitas': 'fasilitas.php',
            'sarana': 'fasilitas.php',
            'kontak': 'kontak.php',
            'hubungi': 'kontak.php',
            'tentang': 'tentang-kami.php',
            'profil': 'tentang-kami.php',
            'alumni': 'alumni.php',
            'beranda': 'home.php',
            'jelajahi': 'akademik.php',
            'selengkapnya': 'tentang-kami.php'
        };

        function autoFillLink() {
            const teksEl = document.getElementById('input-teks-tombol');
            const linkEl = document.getElementById('input-link-tombol');
            if (!teksEl || !linkEl || linkEl.value.trim() !== '') return;

            const teks = teksEl.value.toLowerCase().trim();
            for (const [keyword, url] of Object.entries(autoFillMap)) {
                if (teks.includes(keyword)) {
                    linkEl.value = url;
                    linkEl.classList.add('ring-2', 'ring-brand-teal/50');
                    setTimeout(() => linkEl.classList.remove('ring-2', 'ring-brand-teal/50'), 1500);
                    break;
                }
            }
        }

        // Attach auto-fill on teks_tombol change
        document.getElementById('input-teks-tombol')?.addEventListener('input', function() {
            const linkEl = document.getElementById('input-link-tombol');
            if (linkEl && linkEl.value.trim() === '') autoFillLink();
        });
        document.getElementById('input-teks-tombol')?.addEventListener('blur', autoFillLink);

        function openModal() {
            document.getElementById('crud-form').reset();
            document.getElementById('input-id').value='';
            document.getElementById('modal-title').textContent='Tambah Slide';
            document.getElementById('crud-modal').classList.remove('hidden');
            document.getElementById('crud-modal').classList.add('active');
        }
        function editModal(data) {
            document.getElementById('input-id').value=data.id;
            document.getElementById('input-judul').value=data.judul;
            document.getElementById('input-subjudul').value=data.subjudul;
            document.getElementById('input-deskripsi').value=data.deskripsi||'';
            document.getElementById('input-teks-tombol').value=data.teks_tombol||'';
            document.getElementById('input-link-tombol').value=data.link_tombol||'';
            document.getElementById('input-urutan').value=data.urutan;
            document.getElementById('input-old-gambar').value=data.gambar_background || '';
            document.getElementById('modal-title').textContent='Edit Slide';
            document.getElementById('crud-modal').classList.remove('hidden');
            document.getElementById('crud-modal').classList.add('active');
        }
        function closeModal() {
            document.getElementById('crud-modal').classList.remove('active');
            setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300);
        }
    </script>
<?php require_once 'includes/footer.php'; ?>
