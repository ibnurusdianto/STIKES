<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
$pageTitle = 'Kelola Kontak';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Token!');
        header("Location: kelola-kontak.php");
        exit;
    }

    $alamat = trim($_POST['alamat']);
    $email = trim($_POST['email']);
    $telp = trim($_POST['telp']);
    $wa = trim($_POST['wa']);
    $jam = trim($_POST['jam']);

    $stmt = $conn->prepare("UPDATE kontak SET alamat=?, email=?, telp=?, wa=?, jam_operasional=? WHERE id=1");
    if ($stmt) {
        $stmt->bind_param("sssss", $alamat, $email, $telp, $wa, $jam);
        if ($stmt->execute()) set_flash_message('success', 'Informasi Kontak berhasil diperbarui.');
        $stmt->close();
    } else {

        $conn->query("INSERT INTO kontak (id, alamat, email, telp, wa, jam_operasional) VALUES (1, '$alamat', '$email', '$telp', '$wa', '$jam')");
        set_flash_message('success', 'Informasi Kontak disimpan.');
    }
    header("Location: kelola-kontak.php");
    exit;
}

$kontak = [
    'alamat' => '', 'email' => '', 'telp' => '', 'wa' => '', 'jam_operasional' => ''
];
$res = $conn->query("SELECT * FROM kontak WHERE id=1");
if ($res && $res->num_rows > 0) {
    $kontak = $res->fetch_assoc();
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="max-w-3xl bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Informasi Kontak Kampus</h2>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                <div>
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-teal outline-none"><?= htmlspecialchars($kontak['alamat']) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label class="block text-sm font-semibold mb-2">Email Kampus</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($kontak['email']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></div>

                    <div><label class="block text-sm font-semibold mb-2">Nomor Telepon</label>
                    <input type="text" name="telp" value="<?= htmlspecialchars($kontak['telp']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></div>

                    <div><label class="block text-sm font-semibold mb-2">Nomor WhatsApp PMB</label>
                    <input type="text" name="wa" value="<?= htmlspecialchars($kontak['wa']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></div>

                    <div><label class="block text-sm font-semibold mb-2">Jam Operasional</label>
                    <input type="text" name="jam" value="<?= htmlspecialchars($kontak['jam_operasional']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <button type="submit" class="w-full py-3 bg-brand-teal hover:bg-brand-teal-dark text-white font-bold rounded-lg shadow-md transition-colors">Simpan Perubahan Kontak</button>
                </div>
            </form>
        </div>
    </div>
<?php require_once 'includes/footer.php'; ?>
