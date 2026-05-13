<?php
/**
 * Database Migration Script
 * Run this once to add new tables for: kerjasama, alumni_form, kontak_pmb
 * Visit: http://localhost/stikes/admin/config/migrate.php
 */

require_once 'database.php';

$queries = [];

// Table: kerjasama (university partnerships)
$queries['Kerjasama Table'] = "CREATE TABLE IF NOT EXISTS kerjasama (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_institusi VARCHAR(255) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    deskripsi TEXT DEFAULT NULL,
    website VARCHAR(500) DEFAULT NULL,
    jenis_kerjasama VARCHAR(100) DEFAULT 'Umum',
    status ENUM('Publish','Draft') DEFAULT 'Publish',
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Table: alumni_testimoni (testimonials from alumni data)
$queries['Alumni Testimoni Table'] = "CREATE TABLE IF NOT EXISTS alumni_testimoni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_alumni VARCHAR(255) NOT NULL,
    tahun_lulus YEAR NOT NULL,
    prodi_id INT DEFAULT NULL,
    pesan_testimoni TEXT DEFAULT NULL,
    pekerjaan_sekarang VARCHAR(255) DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('Publish','Draft') DEFAULT 'Publish',
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$queries['Alumni Form Table'] = "CREATE TABLE IF NOT EXISTS alumni_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(255) NOT NULL,
    nim VARCHAR(50) NOT NULL,
    prodi_id INT DEFAULT NULL,
    tahun_masuk YEAR NOT NULL,
    tahun_lulus YEAR NOT NULL,
    no_ijazah VARCHAR(100) DEFAULT NULL,
    tempat_lahir VARCHAR(100) DEFAULT NULL,
    tanggal_lahir DATE DEFAULT NULL,
    jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL,
    email VARCHAR(255) NOT NULL,
    no_hp VARCHAR(30) NOT NULL,
    alamat_sekarang TEXT DEFAULT NULL,
    pekerjaan_sekarang VARCHAR(255) DEFAULT NULL,
    instansi_kerja VARCHAR(255) DEFAULT NULL,
    jabatan VARCHAR(255) DEFAULT NULL,
    pesan TEXT DEFAULT NULL,
    status ENUM('Pending','Verified','Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Table: kontak_pmb (PMB contact info managed from admin)
$queries['Kontak PMB Table'] = "CREATE TABLE IF NOT EXISTS kontak_pmb (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pic VARCHAR(255) NOT NULL DEFAULT 'Panitia PMB',
    no_telp VARCHAR(50) DEFAULT NULL,
    no_whatsapp VARCHAR(50) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    alamat_sekretariat TEXT DEFAULT NULL,
    jam_operasional VARCHAR(255) DEFAULT NULL,
    link_pendaftaran VARCHAR(500) DEFAULT NULL,
    pesan_tambahan TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Insert default PMB contact row
$queries['Default PMB Contact'] = "INSERT IGNORE INTO kontak_pmb (id, nama_pic, no_telp, no_whatsapp, email, alamat_sekretariat, jam_operasional)
VALUES (1, 'Panitia PMB STIKES MRHJ', '(021) 1234-5678', '0812-3456-7890', 'pmb@smrhj.ac.id', 'Kampus STIKES Mitra Ria Husada Jakarta, Jl. Karya Bhakti Cibubur, Jakarta Timur', 'Senin - Jumat, 08:00 - 16:00 WIB')";

// Table: pesan_kontak (contact form messages)
$queries['Pesan Kontak Table'] = "CREATE TABLE IF NOT EXISTS pesan_kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengirim VARCHAR(255) NOT NULL,
    email_pengirim VARCHAR(255) NOT NULL,
    subjek VARCHAR(255) DEFAULT NULL,
    pesan TEXT NOT NULL,
    status ENUM('Belum Dibaca','Dibaca','Dibalas') DEFAULT 'Belum Dibaca',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Ensure berita table has slug column (use try-catch since IF NOT EXISTS not supported for columns in older MySQL)
$queries['Berita Slug Column'] = "SELECT 1"; // placeholder, handled separately below

echo "<h2>STIKES MRHJ - Database Migration</h2><pre>";
$success = 0;
$errors = 0;

foreach ($queries as $label => $sql) {
    if ($label === 'Berita Slug Column') continue; // skip placeholder
    try {
        if ($conn->query($sql)) {
            echo "✅ {$label}: OK\n";
            $success++;
        } else {
            echo "❌ {$label}: " . $conn->error . "\n";
            $errors++;
        }
    } catch (Exception $e) {
        echo "❌ {$label}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Handle slug column separately with existence check
try {
    $result = $conn->query("SHOW COLUMNS FROM berita LIKE 'slug'");
    if ($result && $result->num_rows === 0) {
        $conn->query("ALTER TABLE berita ADD COLUMN slug VARCHAR(500) DEFAULT NULL AFTER judul");
        echo "✅ Berita Slug Column: Added\n";
        $success++;
    } else {
        echo "✅ Berita Slug Column: Already exists\n";
        $success++;
    }
} catch (Exception $e) {
    echo "❌ Berita Slug Column: " . $e->getMessage() . "\n";
    $errors++;
}

// Create uploads/kerjasama directory
$kerjaDir = '../../uploads/kerjasama/';
if (!is_dir($kerjaDir)) {
    mkdir($kerjaDir, 0777, true);
    echo "✅ Uploads/kerjasama directory: Created\n";
} else {
    echo "✅ Uploads/kerjasama directory: Already exists\n";
}

// Ensure pesan_kontak has all required columns
$pesanColumns = [
    'status' => "ENUM('Belum Dibaca','Dibaca','Dibalas') DEFAULT 'Belum Dibaca'",
    'nama_pengirim' => "VARCHAR(255) NOT NULL DEFAULT ''",
    'email_pengirim' => "VARCHAR(255) NOT NULL DEFAULT ''",
    'subjek' => "VARCHAR(255) DEFAULT NULL",
    'pesan' => "TEXT DEFAULT NULL",
];
try {
    $existing = [];
    $res = $conn->query("SHOW COLUMNS FROM pesan_kontak");
    if ($res) while ($row = $res->fetch_assoc()) $existing[] = $row['Field'];

    foreach ($pesanColumns as $col => $def) {
        if (!in_array($col, $existing)) {
            $conn->query("ALTER TABLE pesan_kontak ADD COLUMN $col $def");
            echo "✅ pesan_kontak.$col: Added\n";
            $success++;
        }
    }
} catch (Exception $e) {
    echo "❌ pesan_kontak columns: " . $e->getMessage() . "\n";
    $errors++;
}

// Ensure alumni_testimoni has urutan column for auto-ordering
try {
    $existing = [];
    $res = $conn->query("SHOW COLUMNS FROM alumni_testimoni");
    if ($res) while ($row = $res->fetch_assoc()) $existing[] = $row['Field'];
    if (!in_array('urutan', $existing)) {
        $conn->query("ALTER TABLE alumni_testimoni ADD COLUMN urutan INT DEFAULT 0 AFTER status");
        echo "✅ alumni_testimoni.urutan: Added\n";
        $success++;
    } else {
        echo "✅ alumni_testimoni.urutan: Already exists\n";
        $success++;
    }
} catch (Exception $e) {
    echo "❌ alumni_testimoni.urutan: " . $e->getMessage() . "\n";
    $errors++;
}

echo "\n\nDone! Success: {$success}, Errors: {$errors}\n";
echo "</pre>";
echo "<p><a href='../dashboard.php'>← Kembali ke Dashboard</a></p>";
?>
