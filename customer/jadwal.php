<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = 2026;

$query = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE tanggal='$tanggal' ORDER BY jam_mulai ASC");

$data = [];
while($row = mysqli_fetch_assoc($query)){
    $data[] = $row;
}

$months = [
"01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr",
"05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug",
"09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Dec"
];

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Jadwal - Brilliant Beauty</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>

:root{
    --cream:#faf7f2;
    --warm:#3d3029;
    --deep:#2a1f17;
    --bronze:#c4956a;
    --soft:#e8ddd0;
}

/* GLOBAL */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter', sans-serif;
}

body{
    background: var(--cream);
    color: var(--warm);
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 22px;
    min-height:72px;

    background: rgba(250,247,242,0.85);
    backdrop-filter: blur(16px);

    border-bottom:1px solid rgba(196,149,106,0.15);
    box-shadow: 0 10px 30px rgba(42,31,23,0.08);

    position:sticky;
    top:0;
    z-index:999;
}

.brand{
    font-weight:600;
    color:var(--warm);
    letter-spacing:1px;
}

.menu-btn{
    font-size:24px;
    cursor:pointer;
    color:var(--warm);
}

.user{
    font-size:13px;
    color:#6b5b52;
}

/* HERO */
.hero{
    text-align:center;
    padding:30px 20px;
}

.hero h1{
    font-family:'Playfair Display';
    font-size:34px;
    color:var(--warm);
}

.hero p{
    color:#7a6a5f;
    font-size:13px;
    margin-top:6px;
}

/* SIDEBAR */
.sidebar{
    position:fixed;
    top:0;
    left:-280px;
    width:260px;
    height:100%;
    background:linear-gradient(180deg,var(--warm),var(--deep));
    padding:20px;
    transition:0.4s ease;
    z-index:2000;
    box-shadow:10px 0 40px rgba(0,0,0,0.25);
}

.sidebar.active{left:0;}

.profile{
    text-align:center;
    margin-bottom:20px;
}

.avatar{
    width:65px;
    height:65px;
    margin:auto;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,var(--bronze),#e0b98f);
    color:white;
    font-size:28px;
}

.profile h3{
    color:#fff;
    font-size:15px;
    margin-top:8px;
}

.profile p{
    color:rgba(255,255,255,0.6);
    font-size:12px;
}

.sidebar a{
    display:flex;
    gap:10px;
    padding:12px;
    margin:6px 0;
    text-decoration:none;
    color:#faf7f2;
    border-radius:10px;
    transition:0.3s;
}

.sidebar a i{
    color:var(--bronze);
}

.sidebar a:hover{
    background:rgba(196,149,106,0.2);
    transform:translateX(5px);
}

/* OVERLAY */
.overlay{
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    display:none;
}

.overlay.active{display:block;}

/* LAYOUT */
.container{
    max-width:1200px;
    margin:25px auto 50px;
    padding:0 15px;
    display:grid;
    grid-template-columns:350px 1fr;
    gap:30px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(10px);
    border:1px solid rgba(196,149,106,0.2);
    border-radius:16px;
    padding:18px;
    box-shadow:0 10px 25px rgba(42,31,23,0.08);
}

/* MONTH */
.month-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:8px;
}

.month{
    padding:10px;
    border-radius:12px;
    text-align:center;
    text-decoration:none;
    font-size:12px;
    background:#fff;
    border:1px solid #e8ddd0;
    color:var(--warm);
    transition:0.3s;
}

.month:hover{
    background:var(--soft);
}

.month.active{
    background:linear-gradient(135deg,var(--warm),var(--bronze));
    color:#fff;
}

/* CALENDAR */
.calendar{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:6px;
    margin-top:15px;
}

.day{
    text-align:center;
    padding:10px;
    border-radius:10px;
    background:#fff;
    border:1px solid #eee;
    font-size:12px;
}

.day.active{
    background:var(--warm);
    color:#fff;
}

/* SLOT */
.slot{
    display:flex;
    justify-content:space-between;
    padding:12px;
    border-radius:12px;
    border:1px solid #eee;
    background:#fff;
    margin-bottom:10px;
}

.available{
    background:#e9f7ef;
    color:#1f7a4d;
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
}

.full{
    background:#ffe5e5;
    color:#b42318;
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
}

/* MOBILE */
@media(max-width:768px){
    .container{
        grid-template-columns:1fr;
    }
}

</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="menu-btn" onclick="toggleMenu()">
        <i class='bx bx-menu'></i>
    </div>

    <div class="brand">Brilliant Beauty</div>

    <div class="user"><?= $_SESSION['nama']; ?></div>
</div>

<!-- HERO -->
<div class="hero">
    <h1>Jadwal Booking</h1>
    <p>Cek ketersediaan makeup artist untuk hari spesialmu</p>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="profile">
        <div class="avatar"><i class='bx bx-user'></i></div>
        <h3><?= $_SESSION['nama']; ?></h3>
        <p>Customer</p>
    </div>

    <a href="index.php"><i class='bx bx-home'></i> Beranda</a>
    <a href="portofolio.php"><i class='bx bx-image'></i> Portofolio</a>
    <a href="pricelist.php"><i class='bx bx-wallet'></i> Price List</a>
    <a href="jadwal.php"><i class='bx bx-calendar'></i> Jadwal</a>
    <a href="booking.php"><i class='bx bx-edit'></i> Booking</a>
     <a href="status_booking.php"><i class='fas fa-wallet'></i>Pembayaran</a>
    <a href="contact.php"><i class='bx bx-phone'></i> Contact</a>
</div>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<!-- CONTENT -->
<div class="container">

    <!-- LEFT -->
    <div class="card">

        <h3>Select Month</h3>

        <div class="month-grid">
            <?php foreach($months as $num=>$name): ?>
                <a class="month <?= ($bulan==$num?'active':'') ?>"
                   href="?bulan=<?= $num ?>&tanggal=<?= date('Y-m-01') ?>">
                    <?= $name ?>
                </a>
            <?php endforeach; ?>
        </div>

        <h3 style="margin-top:15px;">Calendar</h3>

        <div class="calendar">
            <?php for($d=1;$d<=$daysInMonth;$d++):
                $tgl = "$tahun-$bulan-".str_pad($d,2,'0',STR_PAD_LEFT);
                $active = ($tgl==$tanggal)?"active":"";
            ?>
            <a href="?tanggal=<?= $tgl ?>&bulan=<?= $bulan ?>">
                <div class="day <?= $active ?>"><?= $d ?></div>
            </a>
            <?php endfor; ?>
        </div>

    </div>

    <!-- RIGHT -->
    <div class="card">
        <h3>Schedule - <?= date('d M Y', strtotime($tanggal)) ?></h3>

        <div style="margin-top:15px;">
        <?php if(empty($data)) echo "<p style='color:#888'>Tidak ada jadwal</p>"; ?>

        <?php foreach($data as $d){ ?>
        <div class="slot">
            <div><?= substr($d['jam_mulai'],0,5) ?> - <?= substr($d['jam_selesai'],0,5) ?></div>
            <div class="<?= $d['status']=='penuh'?'full':'available' ?>">
                <?= ucfirst($d['status']) ?>
            </div>
        </div>
        <?php } ?>
        </div>

    </div>

</div>

<script>
function toggleMenu(){
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("overlay").classList.toggle("active");
}
</script>

</body>
</html>