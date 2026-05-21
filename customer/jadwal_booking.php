<?php
// TEMPORER: Tampilkan error (hapus 3 baris ini setelah berhasil)
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ==========================================

session_start();

// Cek file koneksi.php ada atau tidak
 $koneksiPath = dirname(__FILE__) . '/../koneksi.php';
if (!file_exists($koneksiPath)) {
    die("<div style='padding:40px;font-family:monospace;background:#faf6f1;min-height:100vh;'>
        <h3 style='color:#c47070;'>File koneksi.php tidak ditemukan</h3>
        <p style='color:#7d6144;'>Diharapkan di: <strong>$koneksiPath</strong></p>
        <p style='color:#9c7d5a;'>Cek struktur folder:</p>
        <pre style='background:#f3ece3;padding:12px;border-radius:8px;margin-top:10px;color:#5e4832;'>" .
        print_r(scandir(dirname(__FILE__) . '/..')) . "</pre>
    </div>");
}

include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}

// Cek koneksi database
if (!$koneksi || mysqli_connect_errno()) {
    die("<div style='padding:40px;font-family:monospace;background:#faf6f1;min-height:100vh;'>
        <h3 style='color:#c47070;'>Database gagal terhubung</h3>
        <p style='color:#7d6144;'>" . mysqli_connect_error() . "</p>
    </div>");
}

// Cek tabel jadwal ada atau tidak
 $qCheck = mysqli_query($koneksi, "SELECT 1 FROM jadwal LIMIT 1");
if (!$qCheck) {
    die("<div style='padding:40px;font-family:monospace;background:#faf6f1;min-height:100vh;'>
        <h3 style='color:#c47070;'>Tabel belum ada</h3>
        <p style='color:#7d6144;'>Tabel <strong>jadwal</strong> belum dibuat di database.</p>
        <p style='color:#9c7d5a;'>Jalankan SQL pembuatan tabel di phpMyAdmin terlebih dahulu.</p>
    </div>");
}

 $bulan = $_GET['bulan'] ?? date('m');
 $tahun = date('Y');
 $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
 $bulan = str_pad($bulan, 2, '0', '0');
if (!isset($_GET['tanggal'])) {
    $tanggal = "$tahun-$bulan-" . str_pad(date('d'), 2, '0', '0');
}

 $months = [
    "01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr",
    "05"=>"Mei","06"=>"Jun","07"=>"Jul","08"=>"Agu",
    "09"=>"Sep","10"=>"Okt","11"=>"Nov","12"=>"Des"
];

 $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
 $firstDayOfWeek = date('N', strtotime("$tahun-$bulan-01"));
 $dayNames = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
 $today = date('Y-m-d');

// Cek kolom yang ada di tabel jadwal
 $colCheck = mysqli_query($koneksi, "SHOW COLUMNS FROM jadwal LIKE '%status%'");
 $hasStatus = ($colCheck && mysqli_num_rows($colCheck) > 0);

 $query = mysqli_query($koneksi, "SELECT * FROM jadwal WHERE tanggal='$tanggal' ORDER BY jam_mulai ASC");
if (!$query) {
    die("<div style='padding:40px;font-family:monospace;background:#faf6f1;min-height:100vh;'>
        <h3 style='color:#c47070;'>Query error</h3>
        <p style='color:#7d6144;'>" . mysqli_error($koneksi) . "</p>
    </div>");
}
 $slots = [];
while ($r = mysqli_fetch_assoc($query)) $slots[] = $r;

 $fullDates = [];
if ($hasStatus) {
    $qF = mysqli_query($koneksi, "SELECT tanggal, COUNT(*) as c FROM jadwal WHERE status='penuh' GROUP BY tanggal");
    while ($r = mysqli_fetch_assoc($qF)) { if ($r['c'] >= 3) $fullDates[] = $r['tanggal']; }
}

 $datesWithSlots = [];
 $qD = mysqli_query($koneksi, "SELECT DISTINCT tanggal FROM jadwal");
while ($r = mysqli_fetch_assoc($qD)) $datesWithSlots[] = $r['tanggal'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Booking - Brilliant Beauty</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
:root{--cream:#faf6f1;--cream-soft:#f3ece3;--brown-100:#ede5d8;--brown-200:#d4c4ae;--brown-300:#b89e7e;--brown-400:#9c7d5a;--brown-500:#7d6144;--brown-600:#5e4832;--brown-700:#3f3025;--brown-800:#2a1f17;--brown-900:#1a130d;--glass:rgba(255,255,255,0.55);--glass-border:rgba(184,158,126,0.18);--green:#6b8f5e;--green-bg:#eef3ec;--green-border:#c5d9be;--red-soft:#c47070;--red-bg:#f5eded;}
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
.step-label{font-size:11px;color:var(--brown-400);margin-left:8px;font-weight:400;white-space:nowrap;transition:color .3s;}
.step-label.active{color:var(--brown-700);font-weight:500;}
.step-label.done{color:var(--green);}
.step-line{width:50px;height:2px;background:var(--brown-200);margin:0 12px;transition:background .4s;border-radius:1px;}
.step-line.done{background:var(--green);}

.main{max-width:1100px;margin:24px auto 60px;padding:0 24px;}

.cal-card{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:18px;padding:22px;}
.cal-month-label{font-family:'Playfair Display',serif;font-size:18px;color:var(--brown-800);font-weight:500;margin-bottom:16px;text-align:center;}
.month-pills{display:grid;grid-template-columns:repeat(6,1fr);gap:5px;margin-bottom:18px;}
.month-pill{padding:8px 4px;text-align:center;border-radius:10px;font-size:12px;text-decoration:none;color:var(--brown-500);transition:all .25s;border:1px solid transparent;}
.month-pill:hover{background:var(--cream-soft);color:var(--brown-700);}
.month-pill.active{background:var(--brown-700);color:#f3ece3;}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;}
.cal-dayname{text-align:center;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--brown-400);padding:8px 0 6px;}
.cal-day{text-align:center;padding:9px 4px;border-radius:10px;font-size:12.5px;text-decoration:none;color:var(--brown-600);transition:all .2s;position:relative;border:1px solid transparent;}
.cal-day:hover:not(.empty):not(.full-date){background:var(--cream-soft);border-color:var(--brown-200);}
.cal-day.empty{pointer-events:none;}
.cal-day.today{border-color:var(--brown-300);font-weight:600;}
.cal-day.selected{background:var(--brown-700);color:#f3ece3;border-color:transparent;font-weight:500;}
.cal-day.has-slots::after{content:'';position:absolute;bottom:3px;left:50%;transform:translateX(-50%);width:4px;height:4px;border-radius:50%;background:var(--brown-300);}
.cal-day.selected.has-slots::after{background:var(--brown-200);}
.cal-day.full-date{background:var(--red-bg);color:var(--red-soft);opacity:.7;cursor:default;}

.legend{display:flex;justify-content:center;gap:20px;margin:18px 0 24px;}
.legend-item{display:flex;align-items:center;gap:6px;font-size:11px;color:var(--brown-500);}
.legend-dot{width:7px;height:7px;border-radius:50%;}

.slots-wrap{margin-top:24px;display:grid;gap:20px;}
.slots-card{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:18px;padding:22px;}
.slots-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;}
.slots-date{font-family:'Playfair Display',serif;font-size:18px;color:var(--brown-800);font-weight:500;}
.slots-count{font-size:11px;color:var(--brown-400);}
.slots-desc{font-size:12px;color:var(--brown-400);font-weight:300;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--glass-border);}
.slot-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;margin-bottom:20px;}
.slot-item{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:10px;border:1px solid var(--glass-border);transition:all .25s;cursor:pointer;position:relative;}
.slot-item::before{content:'';position:absolute;left:0;top:0;width:3px;height:100%;border-radius:0 2px 2px 0;opacity:0;transition:opacity .25s;}
.slot-item.available::before{background:var(--green);}
.slot-item.available:hover{border-color:var(--green-border);background:var(--green-bg);transform:translateX(2px);}
.slot-item.available:hover::before{opacity:1;}
.slot-item.selected{border-color:var(--brown-300);background:var(--cream-soft);}
.slot-item.selected::before{background:var(--brown-400);opacity:1;}
.slot-item.full{opacity:.4;cursor:default;background:var(--red-bg);}
.slot-item.full::before{background:var(--red-soft);opacity:1;}
.slot-left{display:flex;align-items:center;gap:10px;}
.slot-radio{width:15px;height:15px;border-radius:50%;border:2px solid var(--brown-200);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;}
.slot-item.selected .slot-radio{border-color:var(--brown-500);}
.slot-item.selected .slot-radio::after{content:'';width:7px;height:7px;border-radius:50%;background:var(--brown-600);}
.slot-item.full .slot-radio{border-color:var(--brown-200);background:var(--brown-100);}
.slot-time{font-size:13px;font-weight:500;color:var(--brown-700);}
.slot-item.full .slot-time{color:var(--brown-500);text-decoration:line-through;}
.slot-badge{font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:3px 8px;border-radius:20px;}
.slot-badge.avail{background:var(--green-bg);color:var(--green);border:1px solid var(--green-border);}
.slot-badge.penuh{background:var(--red-bg);color:var(--red-soft);border:1px solid #e0c5c5;}
.no-slots{text-align:center;padding:30px;color:var(--brown-400);font-size:13px;}
.no-slots i{font-size:28px;display:block;margin-bottom:8px;opacity:.4;}

.form-section{border-top:1px solid var(--glass-border);padding-top:18px;}
.form-label{font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--brown-400);margin-bottom:8px;display:block;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;}
.form-input{width:100%;padding:10px 14px;border:1px solid var(--glass-border);border-radius:10px;background:var(--cream);font-size:13px;color:var(--brown-700);font-family:'Inter',sans-serif;outline:none;transition:border-color .2s;}
.form-input::placeholder{color:var(--brown-300);}
.form-input:focus{border-color:var(--brown-300);}
.form-textarea{width:100%;padding:10px 14px;border:1px solid var(--glass-border);border-radius:10px;background:var(--cream);font-size:13px;color:var(--brown-700);font-family:'Inter',sans-serif;resize:none;min-height:60px;outline:none;transition:border-color .2s;}
.form-textarea::placeholder{color:var(--brown-300);}
.form-textarea:focus{border-color:var(--brown-300);}

.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 28px;border-radius:10px;background:var(--brown-700);color:#f3ece3;border:none;font-size:13px;font-weight:500;cursor:pointer;transition:all .25s;font-family:'Inter',sans-serif;text-decoration:none;}
.btn-primary:hover{background:var(--brown-800);transform:translateY(-1px);}
.btn-primary:disabled{opacity:.35;cursor:not-allowed;transform:none;}
.btn-primary i{font-size:16px;}
.btn-row{display:flex;gap:10px;margin-top:20px;justify-content:flex-end;}

.toast{position:fixed;bottom:30px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--brown-800);color:#f3ece3;padding:12px 24px;border-radius:12px;font-size:13px;z-index:9998;transition:transform .4s cubic-bezier(.4,0,.2,1);display:flex;align-items:center;gap:8px;box-shadow:0 12px 40px rgba(26,19,13,0.3);}
.toast.show{transform:translateX(-50%) translateY(0);}
.toast i{font-size:16px;color:var(--brown-300);}

@media(max-width:640px){.month-pills{grid-template-columns:repeat(4,1fr);}.slot-list{grid-template-columns:1fr;}.form-row{grid-template-columns:1fr;}.step-label{display:none;}.step-line{width:36px;}}
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
       <a href="jadwal.php"><i class='bx bx-calendar'></i> Jadwal</a>
<a href="booking.php" class="current"><i class='bx bx-edit'></i> Booking</a>
        <a href="contact.php"><i class='bx bx-phone'></i> Contact</a>
    </nav>
</div>
<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<div class="topbar">
    <div class="topbar-left">
        <div class="menu-btn" onclick="openMenu()"><i class='bx bx-menu'></i></div>
        <div class="topbar-title">Booking</div>
    </div>
    <div class="topbar-user"><i class='bx bx-user-circle'></i> <?php echo $_SESSION['nama']; ?></div>
</div>

<div class="step-bar">
    <div class="step-item"><div class="step-num active">1</div><span class="step-label active">Jadwal</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num">2</div><span class="step-label">Paket</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num">3</div><span class="step-label">Detail</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num">4</div><span class="step-label">Transaksi</span></div>
</div>

<div class="main">

    <div class="cal-card">
        <div class="cal-month-label"><?= $months[$bulan] ?> <?= $tahun ?></div>
        <div class="month-pills">
            <?php foreach($months as $num => $name): ?>
            <a href="?bulan=<?= $num ?>&tanggal=<?= $tahun.'-'.$num.'-01' ?>" class="month-pill <?= ($bulan==$num?'active':'') ?>"><?= $name ?></a>
            <?php endforeach; ?>
        </div>
        <div class="cal-grid">
            <?php foreach($dayNames as $dn): ?><div class="cal-dayname"><?= $dn ?></div><?php endforeach; ?>
            <?php for($i=1;$i<$firstDayOfWeek;$i++): ?><div class="cal-day empty"></div><?php endfor; ?>
            <?php for($d=1;$d<=$daysInMonth;$d++):
                $tgl="$tahun-$bulan-".str_pad($d,2,'0','0');
                $cls=[];
                if($tgl===$today)$cls[]='today';
                if($tgl===$tanggal)$cls[]='selected';
                if(in_array($tgl,$fullDates))$cls[]='full-date';
                if(in_array($tgl,$datesWithSlots))$cls[]='has-slots';
            ?>
            <?php if(in_array($tgl,$fullDates)): ?>
            <div class="cal-day <?= implode(' ',$cls) ?>"><?= $d ?></div>
            <?php else: ?>
            <a href="?tanggal=<?= $tgl ?>&bulan=<?= $bulan ?>" class="cal-day <?= implode(' ',$cls) ?>"><?= $d ?></a>
            <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>

    <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div> Tersedia</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--red-soft);opacity:.6"></div> Penuh</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--brown-200);opacity:.5"></div> Tanpa slot</div>
    </div>

    <div class="slots-wrap">
        <div class="slots-card">
            <div class="slots-header">
                <div class="slots-date"><?= date('d M Y', strtotime($tanggal)) ?></div>
                <div class="slots-count"><?= count($slots) ?> slot</div>
            </div>
            <div class="slots-desc">Pilih waktu yang cocok untuk kamu</div>

            <?php if(empty($slots)): ?>
            <div class="no-slots"><i class='bx bx-calendar-x'></i>Belum ada jadwal untuk tanggal ini</div>
            <?php else: ?>
            <div class="slot-list">
            <?php foreach($slots as $s):
                $isFull = ($hasStatus && $s['status'] === 'penuh');
                $jamMulai = date('H:i', strtotime($s['jam_mulai']));
                $jamSelesai = date('H:i', strtotime($s['jam_selesai']));
                $jamText = $jamMulai . " — " . $jamSelesai;
            ?>
                <?php if($isFull): ?>
                <div class="slot-item full">
                    <div class="slot-left">
                        <div class="slot-radio"></div>
                        <span class="slot-time"><?= $jamText ?></span>
                    </div>
                    <span class="slot-badge penuh">Penuh</span>
                </div>
                <?php else: ?>
                <div class="slot-item available" onclick="selectSlot(this,<?= $s['id'] ?>,'<?= $jamText ?>')">
                    <div class="slot-left">
                        <div class="slot-radio"></div>
                        <span class="slot-time"><?= $jamText ?></span>
                    </div>
                    <span class="slot-badge avail">Tersedia</span>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>

<div class="form-section">
    <span class="form-label">Pilihan Waktu</span>

    <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
        
        <label class="slot-item available selected" 
               style="cursor:pointer;flex:1;min-width:220px;"
               id="modeSlot">
            <div class="slot-left">
                <div class="slot-radio"></div>
                <span class="slot-time">Gunakan Jam Slot</span>
            </div>
            <input type="radio" name="mode_jam" value="slot" checked hidden>
        </label>

        <label class="slot-item available"
               style="cursor:pointer;flex:1;min-width:220px;"
               id="modeRequest">
            <div class="slot-left">
                <div class="slot-radio"></div>
                <span class="slot-time">Request Jam Sendiri</span>
            </div>
            <input type="radio" name="mode_jam" value="request" hidden>
        </label>

    </div>

    <div id="requestJamWrap" style="display:none;">
    
    <div class="form-row">
        <input type="time" id="jamMulaiRequest" class="form-input">
        <input type="time" id="jamSelesaiRequest" class="form-input">
    </div>

    <small style="display:block;margin-top:6px;color:var(--brown-400);font-size:11px;">
        Pilih rentang jam booking
    </small>

</div>
    
           

    <small style="display:block;margin-top:8px;color:var(--brown-400);font-size:11px;">
    Jam yang bentrok dengan booking lain tidak dapat digunakan.
</small>
</div>


<div class="form-section">
    <span class="form-label">Data Diri</span>

    <div class="form-row">
        <input type="text" id="inputNama" class="form-input" placeholder="Nama lengkap">
        <input type="email" id="inputEmail" class="form-input" placeholder="Email">
        
    </div>

    <textarea type="text" id="inputHp" class="form-input" placeholder="No. HP"></textarea>

    <textarea id="inputAlamat" class="form-textarea" placeholder="Alamat lengkap"></textarea>

    <input type="url" id="inputMaps" class="form-input" 
           placeholder="Link Google Maps / Share Location">

    <div style="margin-top:14px;">
    <span class="form-label">Lokasi Makeup</span>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">

        <label class="slot-item available selected"
               style="cursor:pointer;flex:1;min-width:200px;"
               id="lokasiSalon">

            <div class="slot-left">
                <div class="slot-radio"></div>
                <span class="slot-time">Datang ke Salon</span>
            </div>

            <input type="radio" name="lokasi_makeup" value="salon" checked hidden>
        </label>

        <label class="slot-item available"
               style="cursor:pointer;flex:1;min-width:200px;"
               id="lokasiRumah">

            <div class="slot-left">
                <div class="slot-radio"></div>
                <span class="slot-time">Home Service</span>
            </div>

            <input type="radio" name="lokasi_makeup" value="rumah" hidden>
        </label>

    </div>
</div>

    <textarea id="inputCatatan" class="form-textarea" placeholder="Catatan tambahan (opsional)"></textarea>
</div>
            
            
            <div class="btn-row">
    <button class="btn-primary" id="btnNext" onclick="goNext()">
        Pilih Paket <i class='bx bx-right-arrow-alt'></i>
    </button>
</div>
        </div>
    </div>
</div>

<div class="toast" id="toast"><i class='bx bx-info-circle'></i> <span id="toastMsg"></span></div>

<script>
function openMenu(){document.getElementById('sidebar').classList.add('active');document.getElementById('overlay').classList.add('active');}
function closeMenu(){document.getElementById('sidebar').classList.remove('active');document.getElementById('overlay').classList.remove('active');}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeMenu();});

function showToast(msg){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2500);}

let selectedSlotId=null, selectedJam='';
function selectSlot(el,id,jam){
    document.querySelectorAll('.slot-item.available').forEach(s=>s.classList.remove('selected'));
    el.classList.add('selected');
    selectedSlotId=id; selectedJam=jam;
    document.getElementById('btnNext').disabled=false;
}

const modeSlot=document.getElementById('modeSlot');
const modeRequest=document.getElementById('modeRequest');
const lokasiSalon = document.getElementById('lokasiSalon');
const lokasiRumah = document.getElementById('lokasiRumah');

lokasiSalon.onclick = () => {

    lokasiSalon.classList.add('selected');
    lokasiRumah.classList.remove('selected');

    lokasiSalon.querySelector('input').checked = true;
}

lokasiRumah.onclick = () => {

    lokasiRumah.classList.add('selected');
    lokasiSalon.classList.remove('selected');

    lokasiRumah.querySelector('input').checked = true;
}

modeSlot.onclick=()=>{

    modeSlot.classList.add('selected');
    modeRequest.classList.remove('selected');

    modeSlot.querySelector('input').checked=true;

    document.getElementById('requestJamWrap').style.display='none';

    // kalau belum pilih slot, tombol disable
    if(!selectedSlotId){
        document.getElementById('btnNext').disabled=true;
    }

}

modeRequest.onclick=()=>{

    modeRequest.classList.add('selected');
    modeSlot.classList.remove('selected');

    modeRequest.querySelector('input').checked=true;

    document.getElementById('requestJamWrap').style.display='block';

    // aktifkan tombol next
    document.getElementById('btnNext').disabled=false;

}


function isTimeConflict(start, end){

    // kalau tidak ada booked slot
    if(!bookedSlots || bookedSlots.length === 0){
        return false;
    }

    for(let slot of bookedSlots){

        let bookedStart = slot.jam_mulai.substring(0,5);
        let bookedEnd   = slot.jam_selesai.substring(0,5);

        // cek bentrok
        if(start < bookedEnd && end > bookedStart){
            return true;
        }
    }

    return false;
}
function goNext(){

    const modeJam = document.querySelector('input[name="mode_jam"]:checked').value;
    const lokasiMakeup = document.querySelector('input[name="lokasi_makeup"]:checked').value;
    const nama = document.getElementById('inputNama').value.trim();
    const email = document.getElementById('inputEmail').value.trim();
    const hp = document.getElementById('inputHp').value.trim();
    const pasangan = document.getElementById('inputPasangan').value.trim();
    const alamat = document.getElementById('inputAlamat').value.trim();
    const maps = document.getElementById('inputMaps').value.trim();
    const catatan = document.getElementById('inputCatatan').value.trim();

    if(!nama || !hp){
        showToast('Lengkapi nama dan no. HP');
        return;
    }

    const tanggal = '<?= $tanggal ?>';

    let finalJam = '';
    let jadwalId = '';

    // ======================
    // MODE SLOT BIASA
    // ======================
    if(modeJam === 'slot'){

        if(!selectedSlotId){
            showToast('Pilih slot waktu terlebih dahulu');
            return;
        }

        jadwalId = selectedSlotId;
        finalJam = selectedJam;

    }

    // ======================
    // MODE REQUEST JAM
    // ======================
    else{

        const mulai = document.getElementById('jamMulaiRequest').value;
        const selesai = document.getElementById('jamSelesaiRequest').value;

        if(!mulai || !selesai){
            showToast('Lengkapi jam booking');
            return;
        }

        if(selesai <= mulai){
            showToast('Jam selesai harus lebih besar');
            return;
        }

        if(isTimeConflict(mulai, selesai)){
            showToast('Jam bentrok dengan booking lain');
            return;
        }

        finalJam = mulai + ' - ' + selesai;

        // request jam tidak pakai slot
        jadwalId = 0;
    }

    // ======================
    // PINDAH HALAMAN
    // ======================

    const params = new URLSearchParams({
        tanggal: tanggal,
        jadwal_id: jadwalId,
        jam: finalJam,
        mode_jam: modeJam,
        nama: nama,
        email: email,
        no_hp: hp,
        pasangan: pasangan,
        alamat: alamat,
        maps: maps,
        lokasi_makeup: lokasiMakeup,
        catatan: catatan
    });

    window.location.href = 'pilih_paket.php?' + params.toString();
}
const bookedSlots = <?= json_encode($slots) ?>;
</script>
</body>
</html>