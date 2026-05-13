<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

// Only Superadmin can manage admins? We can allow any admin for now or check role.
// Let's assume all logged-in admins can view it, but maybe only edit their own or create new.
// For simplicity, allow full CRUD.

$pageTitle = 'Kelola Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid Security Token!');
        header("Location: kelola-admin.php");
        exit;
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $username = trim($_POST['username']);
        $nama_lengkap = trim($_POST['nama_lengkap']);
        $role = trim($_POST['role']);
        $password = $_POST['password'] ?? '';

        if (empty($id)) {
            // INSERT
            if(empty($password)) $password = 'admin123'; // default fallback
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $hashed, $nama_lengkap, $role);
            
            if ($stmt->execute()) set_flash_message('success', 'Admin berhasil ditambahkan.');
            else set_flash_message('error', 'Gagal menambah admin. Username mungkin sudah ada.');
            $stmt->close();
        } else {
            // UPDATE
            if(!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET username=?, password=?, nama_lengkap=?, role=? WHERE id=?");
                $stmt->bind_param("ssssi", $username, $hashed, $nama_lengkap, $role, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=?, role=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $nama_lengkap, $role, $id);
            }
            if ($stmt->execute()) set_flash_message('success', 'Admin berhasil diperbarui.');
            else set_flash_message('error', 'Gagal memperbarui admin.');
            $stmt->close();
        }
        header("Location: kelola-admin.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        if($id == $_SESSION['admin_id']) {
            set_flash_message('error', 'Anda tidak bisa menghapus akun Anda sendiri yang sedang login!');
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) set_flash_message('success', 'Admin dihapus.');
            $stmt->close();
        }
        header("Location: kelola-admin.php");
        exit;
    }
}

$admins = [];
if ($res = $conn->query("SELECT id, username, nama_lengkap, role, last_login FROM users ORDER BY id ASC")) {
    $admins = $res->fetch_all(MYSQLI_ASSOC);
}

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<main class="pt-16 lg:pl-64 min-h-screen flex flex-col transition-all duration-300">
    <div class="p-4 sm:p-6 lg:p-8 flex-grow">
        <div class="flex justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Admin Terdaftar</h2>
                <p class="text-sm text-gray-500">Kelola pengguna yang memiliki akses ke dashboard ini.</p>
            </div>
            <button onclick="openModal()" class="px-4 py-2 bg-brand-teal text-white rounded-lg h-fit text-sm font-medium">+ Tambah Admin</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                    <tr><th class="px-6 py-4">Nama Lengkap</th><th class="px-6 py-4">Username</th><th class="px-6 py-4">Role</th><th class="px-6 py-4">Terakhir Login</th><th class="px-6 py-4 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($admins)): ?>
                        <tr><td colspan="5" class="text-center p-4">Tidak ada data admin</td></tr>
                    <?php else: foreach($admins as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-brand-navy"><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($item['username']) ?></td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs"><?= htmlspecialchars($item['role']) ?></span></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($item['last_login'] ?? 'Belum pernah') ?></td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button onclick='editModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="text-blue-500">Edit</button>
                                <?php if($item['id'] != $_SESSION['admin_id']): ?>
                                <form method="POST" onsubmit="return confirm('Hapus admin ini?');">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="text-red-500">Hapus</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="crud-modal" class="modal fixed inset-0 z-50 items-center justify-center bg-brand-navy/60 p-4 hidden">
        <div class="modal-content bg-white rounded-xl w-full max-w-md">
            <div class="p-6 border-b flex justify-between"><h3 class="font-bold text-xl" id="modal-title">Form Admin</h3><button onclick="closeModal()">X</button></div>
            <div class="p-6">
                <form id="crud-form" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="input-id">

                    <div><label class="block text-sm mb-1">Nama Lengkap</label><input type="text" name="nama_lengkap" id="input-nama" required class="w-full px-4 py-2 border rounded-lg"></div>
                    <div><label class="block text-sm mb-1">Username</label><input type="text" name="username" id="input-username" required class="w-full px-4 py-2 border rounded-lg"></div>
                    <div><label class="block text-sm mb-1">Role</label>
                        <select name="role" id="input-role" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="Admin">Admin</option>
                            <option value="Superadmin">Superadmin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Password</label>
                        <input type="password" name="password" id="input-password" class="w-full px-4 py-2 border rounded-lg" placeholder="Kosongkan jika tidak ingin mengubah (saat edit)">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan saat mengedit jika tidak ingin mengubah password.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Batal</button><button type="submit" class="px-4 py-2 bg-brand-teal text-white rounded-lg">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openModal() { document.getElementById('crud-form').reset(); document.getElementById('input-id').value=''; document.getElementById('modal-title').textContent='Tambah Admin'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function editModal(data) { document.getElementById('input-id').value=data.id; document.getElementById('input-nama').value=data.nama_lengkap; document.getElementById('input-username').value=data.username; document.getElementById('input-role').value=data.role; document.getElementById('input-password').value=''; document.getElementById('modal-title').textContent='Edit Admin'; document.getElementById('crud-modal').classList.remove('hidden'); document.getElementById('crud-modal').classList.add('active'); }
        function closeModal() { document.getElementById('crud-modal').classList.remove('active'); setTimeout(() => document.getElementById('crud-modal').classList.add('hidden'), 300); }
    </script>
<?php require_once 'includes/footer.php'; ?>
