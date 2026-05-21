<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}

 $tanggal       = $_GET['tanggal'] ?? '';
 $jadwal_id     = $_GET['jadwal_id'] ?? '';
 $jam           = $_GET['jam'] ?? '';
 $nama          = $_GET['nama'] ?? '';
 $no_hp         = $_GET['no_hp'] ?? '';
 $catatan       = $_GET['catatan'] ?? '';
 $paket_id      = $_GET['paket_id'] ?? '';
 $paket_name    = $_GET['paket_name'] ?? '';
 $paket_price   = (int)($_GET['paket_price'] ?? 0);
 $paket_services= $_GET['paket_services'] ?? '';

if (!$tanggal || !$paket_id) {
    header("Location: jadwal_booking.php");
    exit;
}
 $servicesList = explode('|', $paket_services);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Paket - Brilliant Beauty</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
:root{--cream:#faf6f1;--cream-soft:#f3ece3;--brown-100:#ede5d8;--brown-200:#d4c4ae;--brown-300:#b89e7e;--brown-400:#9c7d5a;--brown-500:#7d6144;--brown-600:#5e4832;--brown-700:#3f3025;--brown-800:#2a1f17;--brown-900:#1a130d;--glass:rgba(255,255,255,0.55);--glass-border:rgba(184,158,126,0.18);--green:#6b8f5e;--green-bg:#eef3ec;--green-border:#c5d9be;}
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
.step-label{font-size:11px;color:var(--brown-400);margin-left:8px;font-weight:400;white-space:nowrap;}
.step-label.active{color:var(--brown-700);font-weight:500;}
.step-label.done{color:var(--green);}
.step-line{width:50px;height:2px;background:var(--brown-200);margin:0 12px;border-radius:1px;}
.step-line.done{background:var(--green);}

.main{max-width:1060px;margin:24px auto 60px;padding:0 24px;}

.summary-bar{display:flex;align-items:center;gap:10px;background:var(--cream-soft);border:1px solid var(--glass-border);border-radius:12px;padding:12px 18px;margin-bottom:24px;font-size:12.5px;color:var(--brown-500);flex-wrap:wrap;}
.summary-bar i{font-size:18px;color:var(--brown-400);}
.summary-bar strong{color:var(--brown-700);font-weight:500;}
.summary-sep{color:var(--brown-200);margin:0 2px;}

.custom-grid{display:grid;grid-template-columns:1fr 320px;gap:20px;}
.custom-left{display:flex;flex-direction:column;gap:16px;}
.custom-right{position:sticky;top:88px;align-self:start;display:flex;flex-direction:column;gap:12px;}

.section-card{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:16px;padding:20px;}
.section-title{font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--brown-400);margin-bottom:14px;display:flex;align-items:center;gap:6px;}
.section-title i{font-size:14px;color:var(--brown-300);}

.addon-item{display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid rgba(184,158,126,0.08);}
.addon-item:last-child{border-bottom:none;}
.addon-left{display:flex;align-items:center;gap:10px;cursor:pointer;}
.addon-check{width:18px;height:18px;border-radius:5px;border:2px solid var(--brown-200);display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0;}
.addon-check.checked{border-color:var(--brown-600);background:var(--brown-700);}
.addon-check.checked::after{content:'\2713';color:#f3ece3;font-size:11px;font-weight:600;}
.addon-name{font-size:13px;color:var(--brown-700);}
.addon-price{font-size:12.5px;color:var(--brown-400);white-space:nowrap;}

.pay-option{display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;border:1px solid var(--glass-border);cursor:pointer;transition:all .25s;margin-bottom:8px;}
.pay-option:hover{border-color:var(--brown-200);}
.pay-option.selected{border-color:var(--brown-300);background:var(--cream-soft);}
.pay-radio{width:15px;height:15px;border-radius:50%;border:2px solid var(--brown-200);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;}
.pay-option.selected .pay-radio{border-color:var(--brown-600);}
.pay-option.selected .pay-radio::after{content:'';width:7px;height:7px;border-radius:50%;background:var(--brown-700);}
.pay-icon{font-size:20px;color:var(--brown-400);}
.pay-text{font-size:13px;color:var(--brown-700);}
.pay-sub{font-size:11px;color:var(--brown-400);font-weight:300;}

.cost-card{background:var(--brown-800);border-radius:16px;padding:22px;color:#f3ece3;}
.cost-title{font-family:'Playfair Display',serif;font-size:16px;font-weight:500;margin-bottom:16px;}
.cost-row{display:flex;justify-content:space-between;padding:8px 0;font-size:13px;}
.cost-row-label{color:rgba(243,236,227,0.55);font-weight:300;}
.cost-row-value{color:#f3ece3;font-weight:400;}
.cost-divider{height:1px;background:rgba(243,236,227,0.08);margin:8px 0;}
.cost-total-label{color:var(--brown-300);font-weight:500;font-size:13px;}
.cost-total-value{font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:#f3ece3;}
.cost-dp{background:rgba(107,143,94,0.15);border-radius:10px;padding:14px;margin-top:12px;}
.cost-dp-label{font-size:11px;color:var(--green);font-weight:500;letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;}
.cost-dp-value{font-family:'Playfair Display',serif;font-size:24px;font-weight:600;color:var(--green);}
.cost-sisa{font-size:11.5px;color:rgba(243,236,227,0.4);margin-top:6px;}
.cost-sisa strong{color:rgba(243,236,227,0.7);}

.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 28px;border-radius:10px;background:var(--brown-700);color:#f3ece3;border:none;font-size:13px;font-weight:500;cursor:pointer;transition:all .25s;font-family:'Inter',sans-serif;text-decoration:none;}
.btn-primary:hover{background:var(--brown-800);transform:translateY(-1px);}
.btn-primary i{font-size:16px;}
.btn-outline{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 28px;border-radius:10px;background:transparent;color:var(--brown-500);border:1px solid var(--brown-200);font-size:13px;font-weight:400;cursor:pointer;transition:all .25s;font-family:'Inter',sans-serif;text-decoration:none;}
.btn-outline:hover{border-color:var(--brown-300);color:var(--brown-700);background:var(--cream-soft);}
.btn-outline i{font-size:16px;}
.btn-full{width:100%;}

.toast{position:fixed;bottom:30px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--brown-800);color:#f3ece3;padding:12px 24px;border-radius:12px;font-size:13px;z-index:9998;transition:transform .4s cubic-bezier(.4,0,.2,1);display:flex;align-items:center;gap:8px;box-shadow:0 12px 40px rgba(26,19,13,0.3);}
.toast.show{transform:translateX(-50%) translateY(0);}
.toast i{font-size:16px;color:var(--brown-300);}

@media(max-width:800px){.custom-grid{grid-template-columns:1fr;}.custom-right{position:static;order:-1;}.step-label{display:none;}.step-line{width:36px;}}
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
        <div class="topbar-title">Detail Paket</div>
    </div>
    <div class="topbar-user"><i class='bx bx-user-circle'></i> <?php echo $_SESSION['nama']; ?></div>
</div>

<!-- STEP: 3 dari 4 -->
<div class="step-bar">
    <div class="step-item"><div class="step-num done">✓</div><span class="step-label done">Jadwal</span></div>
    <div class="step-line done"></div>
    <div class="step-item"><div class="step-num done">✓</div><span class="step-label done">Paket</span></div>
    <div class="step-line done"></div>
    <div class="step-item"><div class="step-num active">3</div><span class="step-label active">Detail</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num">4</div><span class="step-label">Transaksi</span></div>
</div>

<div class="main">

    <div class="summary-bar">
        <i class='bx bx-calendar-check'></i>
        <span><?= date('d F Y', strtotime($tanggal)) ?></span>
        <span class="summary-sep">·</span>
        <strong><?= htmlspecialchars($jam) ?></strong>
        <span class="summary-sep">·</span>
        <strong><?= htmlspecialchars($paket_name) ?></strong>
    </div>

    <form id="detailForm" method="POST" action="transaksi.php">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        <input type="hidden" name="jadwal_id" value="<?= $jadwal_id ?>">
        <input type="hidden" name="jam" value="<?= htmlspecialchars($jam) ?>">
        <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
        <input type="hidden" name="no_hp" value="<?= htmlspecialchars($no_hp) ?>">
        <input type="hidden" name="catatan" value="<?= htmlspecialchars($catatan) ?>">
        <input type="hidden" name="paket_id" value="<?= htmlspecialchars($paket_id) ?>">
        <input type="hidden" name="paket_name" value="<?= htmlspecialchars($paket_name) ?>">
        <input type="hidden" name="paket_price" value="<?= $paket_price ?>">
        <input type="hidden" name="paket_services" value="<?= htmlspecialchars($paket_services) ?>">
        <input type="hidden" name="addons_json" id="addonsJson">
        <input type="hidden" name="pembayaran" id="pembayaranInput" value="Transfer Bank">
        <input type="hidden" name="total_hidden" id="totalInput">
        <input type="hidden" name="dp_hidden" id="dpInput">

        <div class="custom-grid">
            <div class="custom-left">

                <!-- Paket Termasuk -->
                <div class="section-card">
                    <div class="section-title"><i class='bx bx-gift'></i> Termasuk dalam Paket</div>
                    <?php foreach($servicesList as $svc): ?>
                    <div class="addon-item">
                        <div class="addon-left">
                            <div class="addon-check checked" style="pointer-events:none;"><span style="color:#f3ece3;font-size:11px;font-weight:600;">✓</span></div>
                            <span class="addon-name"><?= htmlspecialchars($svc) ?></span>
                        </div>
                        <span class="addon-price" style="color:var(--green);font-size:11px;">Termasuk</span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Makeup Addons -->
                <div class="section-card">
                    <div class="section-title"><i class='bx bx-palette'></i> Makeup & Stylist Tambahan</div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('premium_lashes',150000)">
                            <div class="addon-check" id="chk_premium_lashes"></div>
                            <span class="addon-name">Premium Lashes</span>
                        </div>
                        <span class="addon-price">Rp. 150.000</span>
                    </div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('hairdo_tambahan',200000)">
                            <div class="addon-check" id="chk_hairdo_tambahan"></div>
                            <span class="addon-name">Hairdo Tambahan</span>
                        </div>
                        <span class="addon-price">Rp. 200.000</span>
                    </div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('full_retouch',350000)">
                            <div class="addon-check" id="chk_full_retouch"></div>
                            <span class="addon-name">Full Day Retouch</span>
                        </div>
                        <span class="addon-price">Rp. 350.000</span>
                    </div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('body_glow',300000)">
                            <div class="addon-check" id="chk_body_glow"></div>
                            <span class="addon-name">Body Glow</span>
                        </div>
                        <span class="addon-price">Rp. 300.000</span>
                    </div>
                </div>

                <!-- Extra Addons -->
                <div class="section-card">
                    <div class="section-title"><i class='bx bx-star'></i> Aksesoris & Tambahan</div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('aksesoris_pengantin',500000)">
                            <div class="addon-check" id="chk_aksesoris_pengantin"></div>
                            <span class="addon-name">Aksesoris Pengantin</span>
                        </div>
                        <span class="addon-price">Rp. 500.000</span>
                    </div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('henna_art',400000)">
                            <div class="addon-check" id="chk_henna_art"></div>
                            <span class="addon-name">Henna Art</span>
                        </div>
                        <span class="addon-price">Rp. 400.000</span>
                    </div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('nail_art',200000)">
                            <div class="addon-check" id="chk_nail_art"></div>
                            <span class="addon-name">Nail Art</span>
                        </div>
                        <span class="addon-price">Rp. 200.000</span>
                    </div>
                    <div class="addon-item">
                        <div class="addon-left" onclick="toggleAddon('sewa_busana',800000)">
                            <div class="addon-check" id="chk_sewa_busana"></div>
                            <span class="addon-name">Sewa Busana Tambahan</span>
                        </div>
                        <span class="addon-price">Rp. 800.000</span>
                    </div>
                </div>
                <!--rincian biaya-->

            <div class="custom-right">
                <div class="cost-card">
                    <div class="cost-title">Rincian Biaya</div>
                    <div class="cost-row">
                        <span class="cost-row-label"><?= htmlspecialchars($paket_name) ?></span>
                        <span class="cost-row-value"><?= number_format($paket_price,0,',','.') ?></span>
                    </div>
                    <div id="costAddonRows"></div>
                    <div class="cost-divider"></div>
                    <div class="cost-row">
                        <span class="cost-total-label">Total</span>
                        <span class="cost-total-value" id="costTotal"><?= number_format($paket_price,0,',','.') ?></span>
                    </div>
                    <div class="cost-dp">
                        <div class="cost-dp-label">✦ DP yang harus dibayar</div>
                        <div class="cost-dp-value" id="costDp"><?= number_format(max(round($paket_price*0.1),500000),0,',','.') ?></div>
                        <div class="cost-sisa">Sisa pembayaran: <strong id="costSisa"><?= number_format($paket_price-max(round($paket_price*0.1),500000),0,',','.') ?></strong></div>
                    </div>
                </div>

                <a href="pilih_paket.php?tanggal=<?= urlencode($tanggal) ?>&jadwal_id=<?= $jadwal_id ?>&jam=<?= urlencode($jam) ?>&nama=<?= urlencode($nama) ?>&no_hp=<?= urlencode($no_hp) ?>&catatan=<?= urlencode($catatan) ?>" class="btn-outline btn-full">
                    <i class='bx bx-left-arrow-alt'></i> Kembali
                </a>
                <button type="submit" class="btn-primary btn-full" style="margin-top:0;">
                    <i class='bx bx-check-circle'></i> Lanjutkan Transaksi
                </button>
            </div>
        </div>
    </form>
</div>

<div class="toast" id="toast"><i class='bx bx-info-circle'></i> <span id="toastMsg"></span></div>

<script>
function openMenu(){document.getElementById('sidebar').classList.add('active');document.getElementById('overlay').classList.add('active');}
function closeMenu(){document.getElementById('sidebar').classList.remove('active');document.getElementById('overlay').classList.remove('active');}
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeMenu();});
function showToast(msg){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2500);}

const addonMap={premium_lashes:150000,hairdo_tambahan:200000,full_retouch:350000,body_glow:300000,aksesoris_pengantin:500000,henna_art:400000,nail_art:200000,sewa_busana:800000};
const addonNames={premium_lashes:'Premium Lashes',hairdo_tambahan:'Hairdo Tambahan',full_retouch:'Full Day Retouch',body_glow:'Body Glow',aksesoris_pengantin:'Aksesoris Pengantin',henna_art:'Henna Art',nail_art:'Nail Art',sewa_busana:'Sewa Busana Tambahan'};
let selectedAddons=new Set();
let selectedPayment='Transfer Bank';
const basePrice=<?= $paket_price ?>;

function toggleAddon(id,price){
    if(selectedAddons.has(id))selectedAddons.delete(id);else selectedAddons.add(id);
    const chk=document.getElementById('chk_'+id);
    chk.classList.toggle('checked',selectedAddons.has(id));
    updateCost();
}

function selectPay(el,method){
    document.querySelectorAll('.pay-option').forEach(o=>o.classList.remove('selected'));
    el.classList.add('selected');
    selectedPayment=method;
    document.getElementById('pembayaranInput').value=method;
}

function fmt(n){return n.toLocaleString('id-ID');}

function updateCost(){
    let total=basePrice;
    let addonHtml='';
    const addonArr=[];
    selectedAddons.forEach(id=>{
        const p=addonMap[id];if(!p)return;
        total+=p;
        addonArr.push({id,name:addonNames[id],price:p});
        addonHtml+=`<div class="cost-row"><span class="cost-row-label">${addonNames[id]}</span><span class="cost-row-value">${fmt(p)}</span></div>`;
    });
    document.getElementById('costAddonRows').innerHTML=addonHtml;
    document.getElementById('costTotal').textContent=fmt(total);
    const dp=Math.max(Math.round(total*0.1),500000);
    const sisa=total-dp;
    document.getElementById('costDp').textContent=fmt(dp);
    document.getElementById('costSisa').textContent=fmt(sisa);
    document.getElementById('totalInput').value=total;
    document.getElementById('dpInput').value=dp;
    document.getElementById('addonsJson').value=JSON.stringify(addonArr);
}

// Init cost
updateCost();
</script>
</body>
</html>