<?php
// ============================================
// KONFIGURASI DATABASE
// ============================================
 $DB_HOST = 'localhost';
 $DB_NAME = 'db_mua';
 $DB_USER = 'root';
 $DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function generateInvoice($pdo) {
    $prefix = 'INV-' . date('Ymd') . '-';
    do {
        $no = $prefix . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $cek = $pdo->prepare("SELECT id FROM booking WHERE no_invoice = ?");
        $cek->execute([$no]);
    } while ($cek->fetch());
    return $no;
}

function uploadBukti($file, $subdir = 'bukti_transfer') {
    $uploadDir = __DIR__ . '/uploads/' . $subdir . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;

    $newName = uniqid('bukti_') . '.' . $ext;
    $fullPath = $uploadDir . $newName;
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return 'uploads/' . $subdir . '/' . $newName;
    }
    return null;
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    if (!$tanggal) return '-';
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $t = explode('-', $tanggal);
    return (int)$t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
}

function redirect($url, $msg = '', $tipe = 'success') {
    if ($msg) {
        $_SESSION['flash_msg'] = $msg;
        $_SESSION['flash_tipe'] = $tipe;
    }
    header("Location: $url");
    exit;
}

session_start();