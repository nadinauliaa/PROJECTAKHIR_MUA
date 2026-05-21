<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['id'];

/* DATA DARI FORM */
$paket_id = $_POST['paket_id'];
$jadwal_id = $_POST['jadwal_id'];
$layanan = isset($_POST['layanan']) ? $_POST['layanan'] : [];

/* AMBIL HARGA PAKET */
$qP = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM paket WHERE id='$paket_id'"));
$total = $qP['harga'];

/* TAMBAH HARGA LAYANAN */
foreach($layanan as $l){
    $qL = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT harga FROM layanan WHERE id='$l'"));
    $total += $qL['harga'];
}

/* SUBMIT */
if(isset($_POST['bayar'])){

    $metode = $_POST['metode'];
    $jumlah = $_POST['jumlah'];

    /* UPLOAD BUKTI */
    $bukti = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];

    $folder = "../upload/";
    move_uploaded_file($tmp, $folder.$bukti);

    /* ================= SIMPAN ================= */

    // 1. BOOKING
    mysqli_query($koneksi,"INSERT INTO booking(user_id,paket_id,jadwal_id,status)
    VALUES('$user_id','$paket_id','$jadwal_id','pending')");

    $booking_id = mysqli_insert_id($koneksi);

    // 2. DETAIL BOOKING (LAYANAN)
    foreach($layanan as $l){
        $qL = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT harga FROM layanan WHERE id='$l'"));

        mysqli_query($koneksi,"INSERT INTO detail_booking(booking_id,layanan_id,harga)
        VALUES('$booking_id','$l','".$qL['harga']."')");
    }

    // 3. TRANSAKSI
    $dp = $jumlah;
    $sisa = $total - $dp;

    mysqli_query($koneksi,"INSERT INTO transaksi(booking_id,total_harga,dp,sisa_pembayaran,metode_pembayaran,status_pembayaran)
    VALUES('$booking_id','$total','$dp','$sisa','$metode','dp')");

    $transaksi_id = mysqli_insert_id($koneksi);

    // 4. PEMBAYARAN
    mysqli_query($koneksi,"INSERT INTO pembayaran(transaksi_id,jumlah,tanggal_bayar,bukti,status)
    VALUES('$transaksi_id','$jumlah',NOW(),'$bukti','menunggu')");

    /* UPDATE JADWAL JADI PENUH */
    mysqli_query($koneksi,"UPDATE jadwal SET status='penuh' WHERE id='$jadwal_id'");

    echo "<script>alert('Booking berhasil!');window.location='index.php';</script>";
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Pembayaran</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Inter:wght@300;400&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter';}

body{
    background:linear-gradient(135deg,#fff0f5,#fce4ec,#f8bbd0);
}

/* WRAP */
.wrap{
    max-width:600px;
    margin:60px auto;
    padding:20px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(12px);
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

/* TITLE */
h2{
    text-align:center;
    font-family:'Playfair Display';
    color:#b76e79;
    margin-bottom:20px;
}

/* INPUT */
.input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #eee;
    margin-bottom:15px;
}

/* TOTAL */
.total{
    text-align:center;
    font-size:20px;
    color:#ec407a;
    margin-bottom:20px;
}

/* BTN */
.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:30px;
    background:linear-gradient(135deg,#ec407a,#f8bbd0);
    color:white;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="wrap">
<div class="card">

<h2>Payment</h2>

<div class="total">
Total: Rp <?= number_format($total) ?>
</div>

<form method="POST" enctype="multipart/form-data">

<!-- hidden kirim ulang -->
<input type="hidden" name="paket_id" value="<?= $paket_id ?>">
<input type="hidden" name="jadwal_id" value="<?= $jadwal_id ?>">

<?php foreach($layanan as $l){ ?>
<input type="hidden" name="layanan[]" value="<?= $l ?>">
<?php } ?>

<select name="metode" class="input">
    <option value="transfer_bank">Transfer Bank</option>
    <option value="e_wallet">E-Wallet</option>
</select>

<input type="number" name="jumlah" class="input" placeholder="Masukkan jumlah bayar (DP / Lunas)" required>

<input type="file" name="bukti" class="input" required>

<button name="bayar" class="btn">Bayar Sekarang</button>

</form>

</div>
</div>

</body>
</html>