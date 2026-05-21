<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}

// Terima data dari jadwal_booking.php
 $tanggal   = $_GET['tanggal'] ?? '';
 $jadwal_id = $_GET['jadwal_id'] ?? '';
 $jam       = $_GET['jam'] ?? '';
 $nama      = $_GET['nama'] ?? '';
 $no_hp     = $_GET['no_hp'] ?? '';
 $catatan   = $_GET['catatan'] ?? '';

if (!$tanggal || !$jadwal_id) {
    header("Location: jadwal_booking.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pilih Paket - Brilliant Beauty</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
:root{--cream:#faf6f1;--cream-soft:#f3ece3;--brown-100:#ede5d8;--brown-200:#d4c4ae;--brown-300:#b89e7e;--brown-400:#9c7d5a;--brown-500:#7d6144;--brown-600:#5e4832;--brown-700:#3f3025;--brown-800:#2a1f17;--brown-900:#1a130d;--glass:rgba(255,255,255,0.55);--glass-border:rgba(184,158,126,0.18);--green:#6b8f5e;--green-bg:#eef3ec;--green-border:#c5d9be;--gold:#c4956a;--gold-bg:rgba(196,149,106,0.08);--gold-border:rgba(196,149,106,0.25);}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:var(--cream);color:var(--brown-700);min-height:100vh;overflow-x:hidden;}

.sidebar{position:fixed;left:-280px;top:0;width:280px;height:100%;background:var(--brown-800);transition:left .4s cubic-bezier(.4,0,.2,1);z-index:1100;display:flex;flex-direction:column;}
.sidebar.active{left:0;}
.sidebar-header{padding:32px 24px 24px;border-bottom:1px solid rgba(255,255,255,0.06);}
.avatar{width:56px;height:56px;background:linear-gradient(135deg,var(--brown-400),var(--brown-300));border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;margin-bottom:14px;}
.sidebar-header h3{font-family:'Playfair Display',serif;font-size:16px;color:#f3ece3;font-weight:500;}
.sidebar-header p{font-size:11px;color:var(--brown-300);letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
.sidebar-nav{flex:1;padding:16px 14px;}
.sidebar-nav a{display:flex;align-items:center;gap:12px;padding:12px 14px;margin-bottom:2px;text-decoration:none;color:rgba(243,236,227,0.55);border-radius:10px;font-size:13.5px;transition:all .25s;}
.sidebar-nav a i{font-size:18px;opacity:.7;transition:opacity .25s;}
.sidebar-nav a:hover{color:#f3ece3;background:rgba(255,255,255,0.05);}
.sidebar-nav a:hover i{opacity:1;}
.sidebar-nav a.current{color:#f3ece3;background:rgba(184,158,126,0.12);}
.sidebar-nav a.current i{opacity:1;color:var(--brown-300);}
.sidebar-close{position:absolute;top:20px;right:16px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:rgba(243,236,227,0.4);cursor:pointer;border-radius:8px;transition:all .2s;font-size:20px;background:none;border:none;}
.sidebar-close:hover{color:#f3ece3;background:rgba(255,255,255,0.06);}
.overlay{position:fixed;inset:0;background:rgba(26,19,13,0.4);backdrop-filter:blur(2px);opacity:0;visibility:hidden;transition:all .35s;z-index:1050;}
.overlay.active{opacity:1;visibility:visible;}

.topbar{height:64px;background:rgba(250,246,241,0.82);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);display:flex;justify-content:space-between;align-items:center;padding:0 24px;border-bottom:1px solid var(--glass-border);position:sticky;top:0;z-index:900;}
.topbar-left{display:flex;align-items:center;gap:14px;}
.menu-btn{width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;border-radius:8px;transition:background .2s;color:var(--brown-600);font-size:20px;}
.menu-btn:hover{background:var(--cream-soft);}
.topbar-title{font-family:'Playfair Display',serif;font-size:17px;color:var(--brown-800);font-weight:500;}
.topbar-user{font-size:12.5px;color:var(--brown-500);display:flex;align-items:center;gap:6px;}
.topbar-user i{font-size:15px;color:var(--brown-400);}

.step-bar{display:flex;align-items:center;justify-content:center;gap:0;padding:28px 24px 8px;max-width:520px;margin:0 auto;}
.step-num{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border:2px solid var(--brown-200);color:var(--brown-400);transition:all .4s;flex-shrink:0;background:var(--cream);}
.step-num.active{border-color:var(--brown-700);background:var(--brown-700);color:#f3ece3;}
.step-num.done{border-color:var(--green);background:var(--green);color:white;}
.step-label{font-size:11px;color:var(--brown-400);margin-left:8px;font-weight:400;white-space:nowrap;transition:color .3x;}
.step-label.active{color:var(--brown-700);font-weight:500;}
.step-label.done{color:var(--green);}
.step-line{width:50px;height:2px;background:var(--brown-200);margin:0 12px;transition:background .4s;border-radius:1px;}
.step-line.done{background:var(--green);}

.main{max-width:1000px;margin:24px auto 60px;padding:0 24px;}

.summary-bar{display:flex;align-items:center;gap:10px;background:var(--cream-soft);border:1px solid var(--glass-border);border-radius:12px;padding:12px 18px;margin-bottom:24px;font-size:12.5px;color:var(--brown-500);}
.summary-bar i{font-size:18px;color:var(--brown-400);}
.summary-bar strong{color:var(--brown-700);font-weight:500;}

.hint{text-align:center;font-size:12.5px;color:var(--brown-400);font-weight:300;margin-bottom:24px;}

.pkg-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
.pkg-card{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:18px;padding:24px;transition:all .35s;cursor:pointer;position:relative;overflow:hidden;}
.pkg-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--brown-200);transition:background .3s;}
.pkg-card:hover{transform:translateY(-4px);box-shadow:0 12px 40px rgba(42,31,23,0.08);}
.pkg-card.selected{border-color:var(--brown-300);box-shadow:0 12px 40px rgba(42,31,23,0.1);}
.pkg-card.selected::before{background:var(--brown-700);}
.pkg-card.featured{border-color:var(--gold-border);}
.pkg-card.featured::before{background:linear-gradient(90deg,var(--brown-300),var(--gold));}
.pkg-badge{position:absolute;top:14px;right:14px;font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:4px 10px;border-radius:6px;background:var(--gold-bg);color:var(--brown-400);border:1px solid var(--gold-border);}
.pkg-name{font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:var(--brown-800);margin-bottom:4px;line-height:1.3;}
.pkg-price{font-size:22px;font-weight:600;color:var(--brown-600);margin-bottom:16px;letter-spacing:-.5px;}
.pkg-includes{list-style:none;padding:0;margin-bottom:20px;display:flex;flex-direction:column;gap:8px;}
.pkg-includes li{font-size:12.5px;color:var(--brown-500);display:flex;align-items:center;gap:8px;font-weight:300;}
.pkg-includes li i{font-size:14px;color:var(--green);flex-shrink:0;}
.pkg-indicator{width:20px;height:20px;border-radius:50%;border:2px solid var(--brown-200);display:flex;align-items:center;justify-content:center;transition:all .25s;margin-top:auto;}
.pkg-card.selected .pkg-indicator{border-color:var(--brown-700);background:var(--brown-700);}
.pkg-card.selected .pkg-indicator::after{content:'';width:8px;height:8px;border-radius:50%;background:#f3ece3;}

.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 28px;border-radius:10px;background:var(--brown-700);color:#f3ece3;border:none;font-size:13px;font-weight:500;cursor:pointer;transition:all .25s;font-family:'Inter',sans-serif;text-decoration:none;}
.btn-primary:hover{background:var(--brown-800);transform:translateY(-1px);}
.btn-primary:disabled{opacity:.35;cursor:not-allowed;transform:none;}
.btn-primary i{font-size:16px;}
.btn-outline{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 28px;border-radius:10px;background:transparent;color:var(--brown-500);border:1px solid var(--brown-200);font-size:13px;font-weight:400;cursor:pointer;transition:all .25s;font-family:'Inter',sans-serif;text-decoration:none;}
.btn-outline:hover{border-color:var(--brown-300);color:var(--brown-700);background:var(--cream-soft);}
.btn-outline i{font-size:16px;}
.btn-row{display:flex;gap:10px;margin-top:28px;justify-content:space-between;}

.toast{position:fixed;bottom:30px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--brown-800);color:#f3ece3;padding:12px 24px;border-radius:12px;font-size:13px;z-index:9998;transition:transform .4s cubic-bezier(.4,0,.2,1);display:flex;align-items:center;gap:8px;box-shadow:0 12px 40px rgba(26,19,13,0.3);}
.toast.show{transform:translateX(-50%) translateY(0);}
.toast i{font-size:16px;color:var(--brown-300);}

@media(max-width:768px){.pkg-grid{grid-template-columns:1fr;}.step-label{display:none;}.step-line{width:36px;}}
::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:var(--brown-200);border-radius:3px;}
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeMenu()"><i class='bx bx-x'></i></button>
    <div class="sidebar-header">
        <div class="avatar"><i class='bx bx-user'></i></div>
        <h3><?php echo $_SESSION['nama']; ?></h3>
        <p>Customer</p>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php"><i class='bx bx-home'></i> Beranda</a>
        <a href="portofolio.php"><i class='bx bx-image'></i> Portofolio</a>
        <a href="pricelist.php"><i class='bx bx-wallet'></i> Price List</a>
        <a href="jadwal_booking.php" class="current"><i class='bx bx-calendar'></i> Jadwal</a>
        <a href="booking.php"><i class='bx bx-edit'></i> Booking</a>
        <a href="contact.php"><i class='bx bx-phone'></i> Contact</a>
    </nav>
</div>
<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<div class="topbar">
    <div class="topbar-left">
        <div class="menu-btn" onclick="openMenu()"><i class='bx bx-menu'></i></div>
        <div class="topbar-title">Pilih Paket</div>
    </div>
    <div class="topbar-user"><i class='bx bx-user-circle'></i> <?php echo $_SESSION['nama']; ?></div>
</div>

<!-- STEP: 2 dari 4 -->
<div class="step-bar">
    <div class="step-item"><div class="step-num done">✓</div><span class="step-label done">Jadwal</span></div>
    <div class="step-line done"></div>
    <div class="step-item"><div class="step-num active">2</div><span class="step-label active">Paket</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num">3</div><span class="step-label">Detail</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num">4</div><span class="step-label">Transaksi</span></div>
</div>

<div class="main">

    <div class="summary-bar">
        <i class='bx bx-calendar-check'></i>
        <span><?= date('d F Y', strtotime($tanggal)) ?> &nbsp;·&nbsp; <strong><?= htmlspecialchars($jam) ?></strong></span>
    </div>

    <div class="hint">Pilih paket yang sesuai kebutuhanmu, lalu lanjutkan untuk kustomisasi detail.</div>

    

        

      <div class="pkg-grid">

<?php
$query = mysqli_query($koneksi, "SELECT * FROM pricelist ORDER BY created_at DESC");
$paketData = [];

while($row = mysqli_fetch_assoc($query)) {

    $includes = explode('|', $row['includes']);

    $paketData[] = [
        'id' => $row['id'],
        'name' => $row['judul'],
        'price' => $row['harga'],
        'services' => $row['includes']
    ];
?>

<div class="pkg-card" onclick="selectPkg(this, <?= $row['id'] ?>)">

    <?php if($row['harga'] >= 3000000){ ?>
        <span class="pkg-badge">Best Package</span>
    <?php } ?>

    <div class="pkg-name">
        <?= htmlspecialchars($row['judul']) ?>
    </div>

    <div class="pkg-price">
        Rp. <?= number_format($row['harga'],0,',','.') ?>
    </div>

    <ul class="pkg-includes">

        <?php foreach($includes as $item){ ?>

        <li>
            <i class='bx bx-check'></i>
            <?= htmlspecialchars($item) ?>
        </li>

        <?php } ?>

    </ul>

    <div class="pkg-indicator"></div>

</div>

<?php } ?>

</div>
    <div class="btn-row">
        <a href="jadwal_booking.php?tanggal=<?= urlencode($tanggal) ?>&bulan=<?= date('m', strtotime($tanggal)) ?>" class="btn-outline">
            <i class='bx bx-left-arrow-alt'></i> Kembali
        </a>
        <button class="btn-primary" id="btnNext" disabled onclick="goNext()">
            Lanjut ke Detail Paket <i class='bx bx-right-arrow-alt'></i>
        </button>
    </div>
</div>

<div class="toast" id="toast"><i class='bx bx-info-circle'></i> <span id="toastMsg"></span></div>

<script>
function openMenu(){document.getElementById('sidebar').classList.add('active');document.getElementById('overlay').classList.add('active');}
function closeMenu(){document.getElementById('sidebar').classList.remove('active');document.getElementById('overlay').classList.remove('active');}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeMenu();});
function showToast(msg){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2500);}

const packages = <?= json_encode($paketData); ?>;

let selectedIdx=null;
function selectPkg(el,id){
    document.querySelectorAll('.pkg-card').forEach(c=>c.classList.remove('selected'));

    el.classList.add('selected');

    selectedIdx = packages.findIndex(p => p.id == id);

    document.getElementById('btnNext').disabled = false;
}

function goNext(){

    if(selectedIdx === null){
        showToast('Pilih paket terlebih dahulu');
        return;
    }

    const pkg = packages[selectedIdx];

    const params = new URLSearchParams({
        tanggal:'<?= $tanggal ?>',
        jadwal_id:'<?= $jadwal_id ?>',
        jam:'<?= $jam ?>',
        nama:'<?= addslashes($nama) ?>',
        no_hp:'<?= addslashes($no_hp) ?>',
        catatan:'<?= addslashes($catatan) ?>',

        paket_id: pkg.id,
        paket_name: pkg.name,
        paket_price: pkg.price,
        paket_services: pkg.services
    });

    window.location.href = 'detail_paket.php?' + params.toString();
}
    
</script>
</body>
</html>