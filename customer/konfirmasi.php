<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['id']; // FIX DISINI

$tanggal      = $_POST['tanggal'] ?? '';
$jadwal_id    = $_POST['jadwal_id'] ?? null;
$jam          = $_POST['jam'] ?? '';
$nama         = $_POST['nama'] ?? '';
$no_hp        = $_POST['no_hp'] ?? '';
$catatan      = $_POST['catatan'] ?? '';
$paket_id     = $_POST['paket_id'] ?? '';
$paket_name   = $_POST['paket_name'] ?? '';
$paket_price  = (int)($_POST['paket_price'] ?? 0);
$addons_json  = $_POST['addons_json'] ?? '[]';
$total        = (int)($_POST['total'] ?? $paket_price);
$dp           = (int)($_POST['dp'] ?? round($total * 0.1));
$metode_bayar = $_POST['metode_bayar'] ?? 'Transfer Bank';

$no_invoice = 'INV' . time();

// SIMPAN KE DATABASE
$query = mysqli_query($koneksi, "
    INSERT INTO booking (
        no_invoice, customer_id, tanggal, jadwal_id, jam,
        paket, paket_name, paket_price, total, dp,
        nama, no_hp, catatan, metode_bayar, status
    ) VALUES (
        '$no_invoice', '$customer_id', '$tanggal', '$jadwal_id', '$jam',
        '$paket_id', '$paket_name', '$paket_price', '$total', '$dp',
        '$nama', '$no_hp', '$catatan', '$metode_bayar', 'menunggu'
    )
");

if (!$query) {
    die("Gagal simpan booking: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Booking Terkirim</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family:Inter;
    background:#f8f4ef;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:#fff;
    padding:30px;
    border-radius:16px;
    width:420px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,.1);
}

h2{
    color:#3f3025;
}

.status{
    margin-top:10px;
    padding:10px;
    background:#fff3cd;
    color:#856404;
    border-radius:10px;
    font-size:14px;
}

.btn{
    display:inline-block;
    margin-top:20px;
    padding:10px 18px;
    background:#3f3025;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
}
</style>
</head>
<body>

<div class="card">

    <h2>✅ Booking Berhasil</h2>

    <p>Invoice: <b><?= $no_invoice ?></b></p>

    <div class="status">
        ⏳ Menunggu konfirmasi admin
    </div>

    <p style="font-size:13px;margin-top:10px;">
        Silakan tunggu admin menerima booking kamu sebelum lanjut pembayaran
    </p>

<a href="status_booking.php" class="btn-outline">Lihat Status</a>
</div>

</body>
</html>