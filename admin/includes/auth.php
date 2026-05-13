<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Session timeout: auto-logout after 10 minutes of inactivity
$sessionTimeout = 600; // 10 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionTimeout) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['login_error'] = 'Sesi Anda telah berakhir karena tidak aktif selama 10 menit. Silakan login kembali.';
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time();

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function set_flash_message($type, $message) {
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'message' => $message
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash_msg'])) {
        $type = $_SESSION['flash_msg']['type'];
        $msg = $_SESSION['flash_msg']['message'];
        unset($_SESSION['flash_msg']);

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if(typeof showToast === 'function') showToast(" . json_encode($msg) . ", '$type');
            });
        </script>";
    }
}
?>
