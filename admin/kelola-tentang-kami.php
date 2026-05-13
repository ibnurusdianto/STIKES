<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola Tentang Kami';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-tentang-kami.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $kategori = trim($_POST['kategori']);
        $judul = trim($_POST['judul']);
        $konten = trim($_POST['konten']);
        $urutan = intval($_POST['urutan']);
        $status = trim($_POST['status']);

        $gambar = $_POST['old_gambar'] ?? '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/tentang/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['gambar']['name']);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $fileName)) {
                $gambar = $fileName;
            }
        }

        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO tentang_kami (kategori, judul, konten, gambar, urutan, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssis", $kategori, $judul, $konten, $gambar, $urutan, $status);
            $stmt->execute();
            set_flash_message('success', 'Konten berhasil ditambahkan.');
        } else {
            $stmt = $conn->prepare("UPDATE tentang_kami SET kategori=?, judul=?, konten=?, gambar=?, urutan=?, status=? WHERE id=?");
            $stmt->bind_param("ssssisi", $kategori, $judul, $konten, $gambar, $urutan, $status, $id);
            $stmt->execute();
            set_flash_message('success', 'Konten berhasil diperbarui.');
        }
        header("Location: kelola-tentang-kami.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM tentang_kami WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        set_flash_message('success', 'Konten dihapus.');
        header("Location: kelola-tentang-kami.php");
        exit;
    }
}

$query = "SELECT * FROM tentang_kami ORDER BY urutan ASC, id ASC";
$items = [];
if ($result = $conn->query($query)) {
    $items = $result->fetch_all(MYSQLI_ASSOC);
}

// Group items by kategori for the category tabs
$grouped = [];
foreach ($items as $item) {
    $grouped[$item['kategori']][] = $item;
}

// Define category metadata for quick-add buttons
$kategoriMeta = [
    'Deskripsi Utama' => ['icon' => '📝', 'hint' => 'Tuliskan deskripsi umum tentang kampus STIKES MRHJ.', 'judul_default' => 'Tentang STIKES Mitra Ria Husada Jakarta'],
    'Sejarah' => ['icon' => '📜', 'hint' => 'Ceritakan sejarah berdirinya kampus, latar belakang, dan perkembangan hingga saat ini.', 'judul_default' => 'Sejarah STIKES MRHJ'],
    'Visi' => ['icon' => '🎯', 'hint' => 'Tuliskan visi institusi (cukup 1 paragraf ringkas).', 'judul_default' => 'Visi'],
    'Misi' => ['icon' => '🚀', 'hint' => 'Tuliskan butir-butir misi. Satu baris per poin misi.', 'judul_default' => 'Misi'],
    'Tujuan' => ['icon' => '🏆', 'hint' => 'Tuliskan tujuan strategis kampus. Satu baris per poin tujuan.', 'judul_default' => 'Tujuan'],
    'Nilai/Keunggulan' => ['icon' => '⭐', 'hint' => 'Tuliskan nilai inti dan keunggulan kampus.', 'judul_default' => 'Nilai & Keunggulan Kami'],
    'Struktur' => ['icon' => '👤', 'hint' => 'Masukkan nama pimpinan sebagai "Judul" dan jabatan di "Konten".', 'judul_default' => 'Nama Pimpinan'],
];

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Tentang SMRHJ</h2>
                <p class="text-sm text-gray-500">Kelola Sejarah, Visi, Misi, Tujuan, Struktur Pimpinan, dan konten halaman Tentang Kami lainnya.</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal hover:bg-teal-600 text-white rounded-lg shadow text-sm font-medium transition-colors">+ Tambah Konten</button>
        </div>

        <!-- Quick-Add Category Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3 mb-8">
            <?php foreach ($kategoriMeta as $kat => $meta): 
                $count = count($grouped[$kat] ?? []);
                $hasContent = $count > 0;
            ?>
            <button onclick='openModalWithKategori(<?= json_encode($kat) ?>, <?= json_encode($meta) ?>)' 
                    class="relative bg-white rounded-xl p-4 border <?= $hasContent ? 'border-green-200' : 'border-gray-200 border-dashed' ?> hover:shadow-lg hover:border-brand-teal transition-all text-left group">
                <div class="text-2xl mb-2"><?= $meta['icon'] ?></div>
                <div class="text-xs font-bold text-gray-700 mb-1 leading-tight"><?= htmlspecialchars($kat) ?></div>
                <?php if ($hasContent): ?>
                    <span class="inline-flex items-center px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                        <?= $count ?> entri
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">
                        Belum ada
                    </span>
                <?php endif; ?>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                        <tr>
                            <th class="px-4 py-3 w-16">Urutan</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Judul / Nama</th>
                            <th class="px-4 py-3">Konten (Pratinjau)</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($items)): ?>
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada konten. Klik tombol kategori di atas untuk mulai menambahkan.</td></tr>
                        <?php else: foreach($items as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-400"><?= $item['urutan'] ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-semibold text-gray-600">
                                        <?= htmlspecialchars($kategoriMeta[$item['kategori']]['icon'] ?? '📄') ?> <?= htmlspecialchars($item['kategori']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-brand-navy"><?= htmlspecialchars($item['judul']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate"><?= htmlspecialchars(mb_strimwidth(strip_tags($item['konten']), 0, 80, '...')) ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded font-semibold <?= $item['status']=='Publish'?'bg-green-100 text-green-700':'bg-amber-100 text-amber-700' ?>">
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right flex justify-end gap-2">
                                    <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500 hover:underline text-sm font-medium">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Hapus konten ini?');">
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
        <div class="modal-content bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
            <div class="p-6 border-b flex justify-between bg-gray-50 rounded-t-2xl">
                <div>
                    <h3 class="font-bold text-xl text-gray-800" id="modal-title">Form Konten</h3>
                    <p class="text-sm text-gray-500 mt-1" id="modal-hint">Tambahkan atau edit konten halaman Tentang Kami.</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <form id="crud-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">
                    <input type="hidden" name="old_gambar" id="input-old-gambar">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1 font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori" id="input-kategori" required class="w-full px-4 py-2 border rounded-lg focus:ring-brand-teal outline-none">
                                <option value="Deskripsi Utama">📝 Deskripsi Utama</option>
                                <option value="Sejarah">📜 Sejarah & Latar Belakang</option>
                                <option value="Visi">🎯 Visi</option>
                                <option value="Misi">🚀 Misi</option>
                                <option value="Tujuan">🏆 Tujuan</option>
                                <option value="Nilai/Keunggulan">⭐ Nilai & Keunggulan</option>
                                <option value="Struktur">👤 Struktur / Pimpinan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1 font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="input-status" class="w-full px-4 py-2 border rounded-lg focus:ring-brand-teal outline-none">
                                <option value="Publish">Publish</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 font-semibold text-gray-700">Judul / Nama Pimpinan <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="input-judul" required class="w-full px-4 py-2 border rounded-lg focus:ring-brand-teal outline-none">
                    </div>

                    <div>
                        <label class="block text-sm mb-1 font-semibold text-gray-700">Konten (Teks / Jabatan) <span class="text-red-500">*</span></label>
                        <textarea name="konten" id="input-konten" rows="8" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-brand-teal outline-none"></textarea>
                        <p class="text-xs text-gray-500 mt-1" id="konten-helper">Gunakan untuk mengisi deskripsi panjang, sejarah panjang tanpa batas, visi, atau jabatan (jika kategori Struktur).</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1 font-semibold text-gray-700">Urutan Tampil</label>
                            <input type="number" name="urutan" id="input-urutan" value="1" class="w-full px-4 py-2 border rounded-lg focus:ring-brand-teal outline-none">
                        </div>
                        <div>
                            <label class="block text-sm mb-1 font-semibold text-gray-700">Gambar / Banner (Opsional)</label>
                            <input type="file" name="gambar" class="w-full px-3 py-1.5 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t mt-6">
                        <button type="button" onclick="closeModal()" class="px-5 py-2 border rounded-lg text-gray-600 font-medium hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-brand-teal hover:bg-teal-600 text-white rounded-lg font-medium shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('crud-modal');

        // Hint text based on category
        const kategoriHints = {
            'Deskripsi Utama': 'Tuliskan deskripsi umum tentang kampus STIKES MRHJ.',
            'Sejarah': 'Ceritakan sejarah berdirinya kampus, latar belakang, dan perkembangan hingga saat ini.',
            'Visi': 'Tuliskan visi institusi (cukup 1 paragraf ringkas).',
            'Misi': 'Tuliskan butir-butir misi. Satu baris per poin misi.',
            'Tujuan': 'Tuliskan tujuan strategis kampus. Satu baris per poin tujuan.',
            'Nilai/Keunggulan': 'Tuliskan nilai inti dan keunggulan kampus.',
            'Struktur': 'Masukkan nama pimpinan sebagai "Judul" dan jabatan di "Konten".'
        };

        function updateKontenHelper() {
            const kat = document.getElementById('input-kategori').value;
            const helper = document.getElementById('konten-helper');
            if (helper && kategoriHints[kat]) {
                helper.textContent = kategoriHints[kat];
            }
        }

        document.getElementById('input-kategori')?.addEventListener('change', updateKontenHelper);

        function openModal() {
            document.getElementById('crud-form').reset();
            document.getElementById('input-id').value='';
            document.getElementById('input-old-gambar').value='';
            document.getElementById('modal-title').textContent='Tambah Konten';
            document.getElementById('modal-hint').textContent='Tambahkan konten baru ke halaman Tentang Kami.';
            updateKontenHelper();
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function openModalWithKategori(kategori, meta) {
            document.getElementById('crud-form').reset();
            document.getElementById('input-id').value='';
            document.getElementById('input-old-gambar').value='';
            document.getElementById('input-kategori').value = kategori;
            document.getElementById('input-judul').value = meta.judul_default || '';
            document.getElementById('modal-title').textContent = 'Tambah: ' + kategori;
            document.getElementById('modal-hint').textContent = meta.hint || '';
            updateKontenHelper();
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function editModal(data) {
            document.getElementById('input-id').value=data.id;
            document.getElementById('input-kategori').value=data.kategori;
            document.getElementById('input-judul').value=data.judul;
            document.getElementById('input-konten').value=data.konten;
            document.getElementById('input-urutan').value=data.urutan;
            document.getElementById('input-status').value=data.status;
            document.getElementById('input-old-gambar').value=data.gambar || '';
            document.getElementById('modal-title').textContent='Edit: ' + data.kategori;
            document.getElementById('modal-hint').textContent='Mengedit konten "' + data.judul + '"';
            updateKontenHelper();
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeModal() {
            modal.classList.remove('active');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }
    </script>
<?php require_once 'includes/footer.php'; ?>
