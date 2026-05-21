<?php
include '../koneksi.php';

 $dirs = [
    __DIR__ . '/../uploads/',
    __DIR__ . '/../uploads/bukti/',
];

 $results = [];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            $results[] = '<span style="color:#6b8f5e;">✓ Dibuat:</span> ' . $dir;
        } else {
            $results[] = '<span style="color:#c47070;">✗ Gagal:</span> ' . $dir;
        }
    } else {
        $results[] = '<span style="color:#9c7d5a;">✓ Sudah ada:</span> ' . $dir;
    }
}

if (!$koneksi) {
    $results[] = '<span style="color:#c47070;">✗ Koneksi gagal — cek koneksi.php</span>';
} else {
    $results[] = '<span style="color:#6b8f5e;">✓ DB Terhubung</span>';

    $tables = ['jadwal', 'booking', 'detail_booking', 'transaksi'];
    foreach ($tables as $t) {
        $q = mysqli_query($koneksi, "SELECT 1 FROM $t LIMIT 1");
        if ($q) {
            $results[] = '<span style="color:#6b8f5e;">✓ Tabel:</span> ' . $t;
        } else {
            $results[] = '<span style="color:#c47070;">✗ Tabel belum ada:</span> ' . $t . ' <span style="color:#9c7d5a;font-size:12px;">(jalankan SQL pembuatan tabel)</span>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Setup</title></head>
<body style="font-family:monospace;background:#faf6f1;padding:40px;max-width:600px;margin:40px auto;">
<h2 style="font-family:sans-serif;color:#3f3025;margin-bottom:20px;">Setup Brilliant Beauty</h2>
<?php foreach($results as $r): ?>
<div style="padding:8px 0;border-bottom:1px solid #ede5d8;font-size:14px;"><?= $r ?></div>
<?php endforeach; ?>
<p style="margin-top:20px;color:#9c7d5a;font-size:13px;">Kalau ada "Tabel belum ada", jalankan SQL pembuatan tabel di phpMyAdmin. Lalu refresh halaman ini. Hapus file ini setelah semua hijau.</p>
</body>
</html>