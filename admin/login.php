<?php

session_start();
require_once 'config/database.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// Show session timeout message if set
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {

        $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {

                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_nama'] = $user['nama_lengkap'];
                    $_SESSION['last_activity'] = time();

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Password salah!";
                }
            } else {
                $error = "Username tidak ditemukan!";
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - STIKES MRHJ</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        @theme {
            --color-brand-navy: #0A2540;
            --color-brand-teal: #00BFA5;
            --font-sans: "Inter", sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans text-gray-800">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-brand-navy">Admin Panel</h1>
            <p class="text-gray-500 text-sm mt-1">Sistem Informasi CMS SMRHJ</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-100 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-teal focus:border-brand-teal outline-none transition-all">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-teal focus:border-brand-teal outline-none transition-all">
            </div>
            <button type="submit" class="w-full py-3 bg-brand-navy hover:bg-[#113a60] text-white rounded-xl font-bold shadow-md transition-all">
                Masuk ke Dashboard
            </button>
        </form>
    </div>
</body>
</html>
