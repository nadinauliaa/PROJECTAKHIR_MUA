<?php
session_start();
include '../koneksi.php';

$setting = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT * FROM setting_kontak LIMIT 1")
);

$wa_admin = $setting['whatsapp'];

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}

 $submitted = false;
 $struk = null;
 $formData = null;

/*====== BLOK 1: TERIMA DATA DARI DETAIL_PAKET ======*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['pay_now'])) {
    $formData = [
        'tanggal'       => $_POST['tanggal'] ?? '',
        'jadwal_id'     => (int)($_POST['jadwal_id'] ?? 0),
        'jam'           => $_POST['jam'] ?? '',
        'nama'          => $_POST['nama'] ?? '',
        'no_hp'         => $_POST['no_hp'] ?? '',
        'catatan'       => $_POST['catatan'] ?? '',
        'paket_id'      => $_POST['paket_id'] ?? '',
        'paket_name'    => $_POST['paket_name'] ?? '',
        'paket_price'   => (int)($_POST['paket_price'] ?? 0),
        'paket_services'=> $_POST['paket_services'] ?? '',
        'addons_json'   => $_POST['addons_json'] ?? '[]',
        'total'         => (int)($_POST['total_hidden'] ?? 0),
        'dp'            => (int)($_POST['dp_hidden'] ?? 0),
    ];
    if (!$formData['tanggal'] || !$formData['paket_id']) {
        header("Location: jadwal_booking.php");
        exit;
    }
}

/*====== BLOK 2: PROSES BAYAR ======*/
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    $formData = [
        'tanggal'       => $_POST['tanggal'] ?? '',
        'jadwal_id'     => (int)($_POST['jadwal_id'] ?? 0),
        'jam'           => $_POST['jam'] ?? '',
        'nama'          => $_POST['nama'] ?? '',
        'no_hp'         => $_POST['no_hp'] ?? '',
        'catatan'       => $_POST['catatan'] ?? '',
        'paket_id'      => $_POST['paket_id'] ?? '',
        'paket_name'    => $_POST['paket_name'] ?? '',
        'paket_price'   => (int)($_POST['paket_price'] ?? 0),
        'paket_services'=> $_POST['paket_services'] ?? '',
        'addons_json'   => $_POST['addons_json'] ?? '[]',
        'total'         => (int)($_POST['total_hidden'] ?? 0),
        'dp'            => (int)($_POST['dp_hidden'] ?? 0),
        'metode'        => $_POST['metode'] ?? 'Transfer Bank',
        'bank'          => $_POST['bank'] ?? '',
    ];

    $today = date('Ymd');
    $prefix = 'BB';
    $qInv = mysqli_query($koneksi, "SELECT no_invoice FROM booking WHERE no_invoice LIKE '$prefix-$today-%' ORDER BY id DESC LIMIT 1");
    $lastNum = 0;
    if ($row = mysqli_fetch_assoc($qInv)) {
        $parts = explode('-', $row['no_invoice']);
        $lastNum = (int)end($parts);
    }
    $noInvoice = "$prefix-$today-" . str_pad($lastNum + 1, 3, '0', '0');

    $buktiFile = '';
    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($_FILES['bukti_transfer']['tmp_name']);
        if (in_array($fileType, $allowed) && $_FILES['bukti_transfer']['size'] <= 2 * 1024 * 1024) {
            $ext = pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION);
            $buktiFile = 'bukti_' . $noInvoice . '_' . time() . '.' . $ext;
            $uploadDir = '../uploads/bukti/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $uploadDir . $buktiFile);
        }
    }

    $servicesList = explode('|', $formData['paket_services']);
    $addonsArr = json_decode($formData['addons_json'], true) ?: [];

    mysqli_query($koneksi, "
        INSERT INTO booking (
            no_invoice, customer_id, tanggal, jadwal_id, jam,
            paket, paket_name, paket_price,
            total, dp, nama, no_hp, catatan,
            metode_bayar, bank, bukti_transfer,
            status, created_at
        ) VALUES (
            '$noInvoice',
            '" . (int)$_SESSION['id'] . "',
            '" . mysqli_real_escape_string($koneksi, $formData['tanggal']) . "',
            " . ($formData['jadwal_id'] > 0 ? $formData['jadwal_id'] : 'NULL') . ",
            '" . mysqli_real_escape_string($koneksi, $formData['jam']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['paket_id']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['paket_name']) . "',
            " . $formData['paket_price'] . ",
            " . $formData['total'] . ",
            " . $formData['dp'] . ",
            '" . mysqli_real_escape_string($koneksi, $formData['nama']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['no_hp']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['catatan']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['metode']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['bank']) . "',
            '$buktiFile',
            'menunggu',
            NOW()
        )
    ") or die('Error booking: ' . mysqli_error($koneksi));

    $bookingId = mysqli_insert_id($koneksi);

    foreach ($servicesList as $svc) {
        $svc = trim($svc);
        if ($svc === '') continue;
        mysqli_query($koneksi, "INSERT INTO detail_booking (booking_id, jenis, nama_item, harga) VALUES ($bookingId, 'paket', '" . mysqli_real_escape_string($koneksi, $svc) . "', 0)");
    }

    foreach ($addonsArr as $a) {
        if (empty($a['name'])) continue;
        mysqli_query($koneksi, "INSERT INTO detail_booking (booking_id, jenis, nama_item, harga) VALUES ($bookingId, 'addon', '" . mysqli_real_escape_string($koneksi, $a['name']) . "', " . (int)$a['price'] . ")");
    }

    mysqli_query($koneksi, "
        INSERT INTO transaksi (
            booking_id, no_invoice, tipe, jumlah,
            metode_bayar, bank, bukti_transfer,
            status, created_at
        ) VALUES (
            $bookingId, '$noInvoice', 'dp',
            " . $formData['dp'] . ",
            '" . mysqli_real_escape_string($koneksi, $formData['metode']) . "',
            '" . mysqli_real_escape_string($koneksi, $formData['bank']) . "',
            '$buktiFile',
            'menunggu', NOW()
        )
    ");

    if ($formData['jadwal_id'] > 0) {
        mysqli_query($koneksi, "UPDATE jadwal SET status='penuh' WHERE id=" . $formData['jadwal_id']);
    }

    $addonDetails = [];
    foreach ($addonsArr as $a) {
        if (empty($a['name'])) continue;
        $addonDetails[] = $a;
    }

    $struk = [
        'no_invoice'  => $noInvoice,
        'paket'       => $formData['paket_name'],
        'paket_price' => $formData['paket_price'],
        'tanggal'     => $formData['tanggal'],
        'jam'         => $formData['jam'],
        'nama'        => $formData['nama'],
        'no_hp'       => $formData['no_hp'],
        'services'    => $servicesList,
        'addons'      => $addonDetails,
        'total'       => $formData['total'],
        'dp'          => $formData['dp'],
        'sisa'        => $formData['total'] - $formData['dp'],
        'metode'      => $formData['metode'],
        'bank'        => $formData['bank'],
        'bukti'       => $buktiFile,
        'created_at'  => date('d F Y, H:i'),
    ];

    $submitted = true;
}

/*====== BLOK 3: BUKAN POST ======*/
else {
    header("Location: jadwal_booking.php");
    exit;
}

function formatRp($n) { return 'Rp. ' . number_format($n, 0, ',', '.'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $submitted ? 'Struk Pembayaran' : 'Pembayaran' ?> - Brilliant Beauty</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
:root{--cream:#faf6f1;--cream-soft:#f3ece3;--brown-100:#ede5d8;--brown-200:#d4c4ae;--brown-300:#b89e7e;--brown-400:#9c7d5a;--brown-500:#7d6144;--brown-600:#5e4832;--brown-700:#3f3025;--brown-800:#2a1f17;--brown-900:#1a130d;--glass:rgba(255,255,255,0.55);--glass-border:rgba(184,158,126,0.18);--green:#6b8f5e;--green-bg:#eef3ec;--green-border:#c5d9be;--red-soft:#c47070;--red-bg:#f5eded;--orange:#d4913a;--orange-bg:#fdf4e7;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:var(--cream);color:var(--brown-700);min-height:100vh;overflow-x:hidden;}
.topbar{height:64px;background:rgba(250,246,241,0.82);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);display:flex;justify-content:space-between;align-items:center;padding:0 24px;border-bottom:1px solid var(--glass-border);position:sticky;top:0;z-index:900;}
.topbar-left{display:flex;align-items:center;gap:14px;}
.topbar-title{font-family:'Playfair Display',serif;font-size:17px;color:var(--brown-800);font-weight:500;}
.topbar-user{font-size:12.5px;color:var(--brown-500);display:flex;align-items:center;gap:6px;}
.topbar-user i{font-size:15px;color:var(--brown-400);}
.step-bar{display:flex;align-items:center;justify-content:center;gap:0;padding:28px 24px 8px;max-width:520px;margin:0 auto;}
.step-num{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border:2px solid var(--green);background:var(--green);color:white;flex-shrink:0;}
.step-num.active{border-color:var(--brown-700);background:var(--brown-700);color:#f3ece3;}
.step-label{font-size:11px;color:var(--green);margin-left:8px;font-weight:400;white-space:nowrap;}
.step-label.active{color:var(--brown-700);font-weight:500;}
.step-line{width:50px;height:2px;background:var(--green);margin:0 12px;}
.main{max-width:960px;margin:24px auto 60px;padding:0 24px;}
.pay-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;}
.summary-card{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:18px;padding:22px;position:sticky;top:88px;}
.summary-title{font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--brown-400);margin-bottom:16px;display:flex;align-items:center;gap:6px;}
.summary-title i{font-size:14px;color:var(--brown-300);}
.sum-paket{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--glass-border);}
.sum-paket-name{font-family:'Playfair Display',serif;font-size:17px;font-weight:600;color:var(--brown-800);line-height:1.3;max-width:60%;}
.sum-paket-price{font-size:15px;font-weight:600;color:var(--brown-600);white-space:nowrap;}
.sum-svc-list{list-style:none;padding:0;margin-bottom:14px;display:flex;flex-direction:column;gap:5px;}
.sum-svc-list li{font-size:12px;color:var(--brown-500);display:flex;align-items:center;gap:6px;font-weight:300;}
.sum-svc-list li i{font-size:12px;color:var(--green);}
.sum-addon-section{margin-bottom:14px;padding-top:10px;border-top:1px solid var(--glass-border);}
.sum-addon-label{font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--brown-400);margin-bottom:8px;}
.sum-addon-item{display:flex;justify-content:space-between;font-size:12.5px;padding:5px 0;color:var(--brown-500);}
.sum-addon-name{font-weight:300;}
.sum-addon-price{font-weight:400;}
.sum-divider{height:1px;background:var(--glass-border);margin:12px 0;}
.sum-total-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
.sum-total-label{font-size:13px;font-weight:600;color:var(--brown-600);}
.sum-total-value{font-family:'Playfair Display',serif;font-size:22px;font-weight:600;color:var(--brown-800);}
.sum-dp-box{background:var(--green-bg);border:1px solid var(--green-border);border-radius:12px;padding:14px;margin-top:8px;}
.sum-dp-label{font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--green);margin-bottom:4px;}
.sum-dp-value{font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:var(--green);}
.sum-sisa{font-size:11px;color:var(--brown-400);margin-top:4px;}
.sum-sisa strong{color:var(--brown-600);}
.pay-right{display:flex;flex-direction:column;gap:16px;}
.pay-card{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:18px;padding:22px;}
.pay-card-title{font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--brown-400);margin-bottom:16px;display:flex;align-items:center;gap:6px;}
.pay-card-title i{font-size:14px;color:var(--brown-300);}
.metode-option{display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:12px;border:2px solid var(--glass-border);cursor:pointer;transition:all .25s;margin-bottom:10px;}
.metode-option:hover{border-color:var(--brown-200);}
.metode-option.selected{border-color:var(--brown-500);background:var(--cream-soft);}
.metode-radio{width:18px;height:18px;border-radius:50%;border:2px solid var(--brown-200);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;}
.metode-option.selected .metode-radio{border-color:var(--brown-600);}
.metode-option.selected .metode-radio::after{content:'';width:8px;height:8px;border-radius:50%;background:var(--brown-700);}
.metode-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.metode-icon.bank{background:var(--cream-soft);color:var(--brown-500);}
.metode-icon.qris{background:var(--orange-bg);color:var(--orange);}
.metode-icon.cash{background:var(--green-bg);color:var(--green);}
.metode-text{font-size:14px;font-weight:500;color:var(--brown-700);}
.metode-sub{font-size:11px;color:var(--brown-400);font-weight:300;margin-top:1px;}
.bank-options{max-height:0;overflow:hidden;transition:max-height .35s ease,opacity .3s;opacity:0;margin:0;}
.bank-options.show{max-height:300px;opacity:1;margin-top:12px;}
.bank-item{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;border:1px solid var(--glass-border);cursor:pointer;transition:all .2s;margin-bottom:6px;}
.bank-item:hover{border-color:var(--brown-200);}
.bank-item.selected{border-color:var(--brown-400);background:var(--cream-soft);}
.bank-logo{font-size:11px;font-weight:700;letter-spacing:1px;color:var(--brown-600);width:40px;text-align:center;flex-shrink:0;}
.bank-info{flex:1;}
.bank-name{font-size:13px;font-weight:500;color:var(--brown-700);}
.bank-number{font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--brown-500);font-weight:400;letter-spacing:.5px;}
.bank-an{font-size:10.5px;color:var(--brown-400);font-weight:300;}
.bank-copy{width:28px;height:28px;border-radius:6px;border:1px solid var(--brown-100);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--brown-400);font-size:14px;transition:all .2s;flex-shrink:0;background:none;}
.bank-copy:hover{border-color:var(--brown-300);color:var(--brown-600);background:var(--cream-soft);}
.qris-box{max-height:0;overflow:hidden;transition:max-height .35s ease,opacity .3s;opacity:0;margin:0;}
.qris-box.show{max-height:350px;opacity:1;margin-top:12px;}
.qris-content{text-align:center;padding:16px;background:var(--cream-soft);border-radius:12px;border:1px dashed var(--brown-200);}
.qris-img{width:160px;height:160px;margin:0 auto 10px;border-radius:10px;background:white;border:1px solid var(--brown-100);display:flex;align-items:center;justify-content:center;}
.qris-img i{font-size:60px;color:var(--brown-200);}
.qris-hint{font-size:11px;color:var(--brown-400);font-weight:300;}
.upload-section{max-height:0;overflow:hidden;transition:max-height .35s ease,opacity .3s;opacity:0;margin:0;}
.upload-section.show{max-height:400px;opacity:1;margin-top:12px;}
.upload-zone{border:2px dashed var(--brown-200);border-radius:14px;padding:24px;text-align:center;cursor:pointer;transition:all .25s;position:relative;}
.upload-zone:hover{border-color:var(--brown-300);background:var(--cream-soft);}
.upload-zone.has-file{border-color:var(--green);border-style:solid;background:var(--green-bg);}
.upload-zone i{font-size:28px;color:var(--brown-300);margin-bottom:6px;}
.upload-zone.has-file i{color:var(--green);}
.upload-zone p{font-size:12px;color:var(--brown-400);font-weight:300;}
.upload-zone .upload-name{font-size:13px;color:var(--green);font-weight:500;margin-top:4px;}
.upload-zone input{position:absolute;inset:0;opacity:0;cursor:pointer;}
.terms{display:flex;align-items:flex-start;gap:10px;margin-top:16px;}
.terms-checkbox{width:18px;height:18px;border-radius:5px;border:2px solid var(--brown-200);display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;transition:all .2s;margin-top:1px;}
.terms-checkbox.checked{border-color:var(--brown-600);background:var(--brown-700);}
.terms-checkbox.checked::after{content:'\2713';color:#f3ece3;font-size:11px;font-weight:600;}
.terms-text{font-size:12px;color:var(--brown-500);line-height:1.6;}
.terms-text a{color:var(--brown-600);text-decoration:none;font-weight:500;}
.btn-pay{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;border-radius:12px;background:var(--brown-700);color:#f3ece3;border:none;font-size:14px;font-weight:500;cursor:pointer;transition:all .25s;font-family:'Inter',sans-serif;margin-top:16px;}
.btn-pay:hover{background:var(--brown-800);transform:translateY(-1px);box-shadow:0 8px 24px rgba(42,31,23,0.2);}
.btn-pay:disabled{opacity:.35;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-pay i{font-size:18px;}
.back-link{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;color:var(--brown-400);text-decoration:none;transition:color .2s;margin-bottom:20px;}
.back-link:hover{color:var(--brown-700);}
.back-link i{font-size:16px;}
.struk-wrap{max-width:480px;margin:0 auto;}
.struk-success{text-align:center;margin-bottom:24px;}
.struk-check{width:64px;height:64px;border-radius:50%;background:var(--green);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;animation:checkPop .5s .15s cubic-bezier(.34,1.56,.64,1) both;box-shadow:0 8px 24px rgba(107,143,94,0.3);}
@keyframes checkPop{from{transform:scale(0)}to{transform:scale(1)}}
.struk-check i{font-size:30px;color:white;}
.struk-success h2{font-family:'Playfair Display',serif;font-size:24px;color:var(--brown-800);margin-bottom:4px;}
.struk-success p{font-size:13px;color:var(--brown-400);font-weight:300;}
.struk-status{text-align:center;margin-bottom:20px;}
.struk-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;background:var(--orange-bg);color:var(--orange);border:1px solid rgba(212,145,58,0.2);}
.struk-badge i{font-size:14px;}
.struk-receipt{background:white;border-radius:4px;box-shadow:0 4px 24px rgba(42,31,23,0.08);position:relative;padding:28px 24px;}
.struk-receipt::before,.struk-receipt::after{content:'';position:absolute;left:0;right:0;height:8px;background:radial-gradient(circle at 8px,transparent 8px,var(--cream) 8px);background-size:16px 16px;}
.struk-receipt::before{top:-4px;}
.struk-receipt::after{bottom:-4px;transform:rotate(180deg);}
.struk-header{text-align:center;padding-bottom:16px;border-bottom:2px dashed var(--brown-100);}
.struk-brand{font-family:'Playfair Display',serif;font-size:20px;font-weight:600;color:var(--brown-800);letter-spacing:1px;}
.struk-address{font-size:10.5px;color:var(--brown-400);font-weight:300;margin-top:2px;line-height:1.5;}
.struk-invoice-box{margin-top:12px;padding:8px;background:var(--cream-soft);border-radius:8px;display:inline-block;}
.struk-invoice-label{font-size:9px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--brown-400);}
.struk-invoice-num{font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:500;color:var(--brown-800);letter-spacing:.5px;}
.struk-section{padding:14px 0;border-bottom:2px dashed var(--brown-100);}
.struk-section:last-of-type{border-bottom:none;}
.struk-section-label{font-size:9px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--brown-400);margin-bottom:10px;}
.struk-row{display:flex;justify-content:space-between;font-size:12.5px;padding:3px 0;}
.struk-row-label{color:var(--brown-500);font-weight:300;}
.struk-row-value{color:var(--brown-700);font-weight:400;text-align:right;max-width:55%;}
.struk-row-value.mono{font-family:'JetBrains Mono',monospace;font-size:12px;}
.struk-item-row{display:flex;justify-content:space-between;font-size:12px;padding:4px 0;color:var(--brown-600);}
.struk-item-name{font-weight:300;max-width:60%;}
.struk-item-price{font-family:'JetBrains Mono',monospace;font-size:12px;white-space:nowrap;}
.struk-total-section{padding:14px 0;border-top:2px dashed var(--brown-100);}
.struk-total-row{display:flex;justify-content:space-between;align-items:center;}
.struk-total-label{font-size:13px;font-weight:600;color:var(--brown-600);}
.struk-total-value{font-family:'Playfair Display',serif;font-size:24px;font-weight:600;color:var(--brown-800);}
.struk-dp-section{background:var(--green-bg);border-radius:10px;padding:14px;margin-top:10px;}
.struk-dp-row{display:flex;justify-content:space-between;font-size:12.5px;}
.struk-dp-row-label{color:var(--green);font-weight:500;}
.struk-dp-row-value{font-family:'JetBrains Mono',monospace;font-weight:500;color:var(--green);}
.struk-footer{text-align:center;padding-top:14px;}
.struk-footer p{font-size:11px;color:var(--brown-400);font-weight:300;line-height:1.5;}
.struk-footer .thank{font-family:'Playfair Display',serif;font-size:14px;color:var(--brown-600);font-style:italic;margin-bottom:4px;}
.struk-actions{display:flex;gap:10px;margin-top:20px;}
.struk-actions a,.struk-actions button{flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border-radius:10px;font-size:12.5px;font-weight:500;text-decoration:none;transition:all .25s;cursor:pointer;border:none;font-family:'Inter',sans-serif;}
.struk-actions .btn-pdf{background:var(--cream-soft);color:var(--brown-700);border:1px solid var(--glass-border);}
.struk-actions .btn-pdf:hover{background:var(--brown-100);}
.struk-actions .btn-wa{background:#25D366;color:white;}
.struk-actions .btn-wa:hover{background:#1fb855;}
.struk-actions .btn-print{background:transparent;color:var(--brown-500);border:1px solid var(--brown-200);}
.struk-actions .btn-print:hover{background:var(--cream-soft);color:var(--brown-700);}
.struk-actions a i,.struk-actions button i{font-size:16px;}
.struk-home{text-align:center;margin-top:16px;}
.struk-home a{font-size:12.5px;color:var(--brown-400);text-decoration:none;transition:color .2s;}
.struk-home a:hover{color:var(--brown-700);}
@media(max-width:768px){.pay-grid{grid-template-columns:1fr;}.summary-card{position:static;order:-1;}.struk-actions{flex-direction:column;}.step-label{display:none;}.step-line{width:36px;}}
@media print{body{background:white!important;}.topbar,.step-bar,.struk-success,.struk-status,.struk-actions,.struk-home{display:none!important;}.main{margin:0;padding:0;max-width:100%;}.struk-wrap{max-width:100%;}.struk-receipt{box-shadow:none;border:1px solid #ddd;}.struk-receipt::before,.struk-receipt::after{background:radial-gradient(circle at 6px,transparent 6px,white 6px);background-size:12px 12px;}}
::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:var(--brown-200);border-radius:3px;}
.dana-actions{
    display:flex;
    gap:10px;
    justify-content:center;
    margin-top:18px;
    flex-wrap:wrap;
}

.btn-copy,
.btn-open-dana{
    border:none;
    padding:10px 16px;
    border-radius:10px;
    cursor:pointer;
    text-decoration:none;
    font-size:13px;
    display:flex;
    align-items:center;
    gap:6px;
    transition:.3s;
}

.btn-copy{
    background:#f1f1f1;
    color:#333;
}

.btn-open-dana{
    background:#108ee9;
    color:white;
}

.btn-copy:hover,
.btn-open-dana:hover{
    transform:translateY(-2px);
}
</style>
</head>
<body>
<div class="topbar">
    <div class="topbar-left"><div class="topbar-title"><?= $submitted ? 'Struk Pembayaran' : 'Pembayaran' ?></div></div>
    <div class="topbar-user"><i class='bx bx-user-circle'></i> <?php echo $_SESSION['nama']; ?></div>
</div>
<div class="step-bar">
    <div class="step-item"><div class="step-num done">✓</div><span class="step-label done">Jadwal</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num done">✓</div><span class="step-label done">Paket</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num done">✓</div><span class="step-label done">Detail</span></div>
    <div class="step-line"></div>
    <div class="step-item"><div class="step-num active">4</div><span class="step-label active">Transaksi</span></div>
</div>
<div class="main">

<?php if (!$submitted): ?>
<a href="detail_paket.php" class="back-link"><i class='bx bx-left-arrow-alt'></i> Kembali ke Detail Paket</a>
<form method="POST" enctype="multipart/form-data" id="payForm">
    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($formData['tanggal']) ?>">
    <input type="hidden" name="jadwal_id" value="<?= $formData['jadwal_id'] ?>">
    <input type="hidden" name="jam" value="<?= htmlspecialchars($formData['jam']) ?>">
    <input type="hidden" name="nama" value="<?= htmlspecialchars($formData['nama']) ?>">
    <input type="hidden" name="no_hp" value="<?= htmlspecialchars($formData['no_hp']) ?>">
    <input type="hidden" name="catatan" value="<?= htmlspecialchars($formData['catatan']) ?>">
    <input type="hidden" name="paket_id" value="<?= htmlspecialchars($formData['paket_id']) ?>">
    <input type="hidden" name="paket_name" value="<?= htmlspecialchars($formData['paket_name']) ?>">
    <input type="hidden" name="paket_price" value="<?= $formData['paket_price'] ?>">
    <input type="hidden" name="paket_services" value="<?= htmlspecialchars($formData['paket_services']) ?>">
    <input type="hidden" name="addons_json" value="<?= htmlspecialchars($formData['addons_json']) ?>">
    <input type="hidden" name="total_hidden" value="<?= $formData['total'] ?>">
    <input type="hidden" name="dp_hidden" value="<?= $formData['dp'] ?>">
    <input type="hidden" name="metode" id="metodeInput" value="Transfer Bank">
    <input type="hidden" name="bank" id="bankInput" value="">
    <div class="pay-grid">
        <div class="summary-card">
            <div class="summary-title"><i class='bx bx-receipt'></i> Ringkasan Pesanan</div>
            <div class="sum-paket">
                <div class="sum-paket-name"><?= htmlspecialchars($formData['paket_name']) ?></div>
                <div class="sum-paket-price"><?= formatRp($formData['paket_price']) ?></div>
            </div>
            <ul class="sum-svc-list">
                <?php foreach(explode('|', $formData['paket_services']) as $svc): ?>
                <li><i class='bx bx-check'></i> <?= htmlspecialchars($svc) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php $addonsArr = json_decode($formData['addons_json'], true) ?: []; if (!empty($addonsArr)): ?>
            <div class="sum-addon-section">
                <div class="sum-addon-label">Add-on Tambahan</div>
                <?php foreach($addonsArr as $a): ?>
                <div class="sum-addon-item"><span class="sum-addon-name"><?= htmlspecialchars($a['name']) ?></span><span class="sum-addon-price"><?= formatRp($a['price']) ?></span></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="sum-divider"></div>
            <div class="sum-total-row"><span class="sum-total-label">Total</span><span class="sum-total-value"><?= formatRp($formData['total']) ?></span></div>
            <div class="sum-dp-box">
                <div class="sum-dp-label">✦ DP yang harus dibayar</div>
                <div class="sum-dp-value"><?= formatRp($formData['dp']) ?></div>
                <div class="sum-sisa">Sisa pembayaran: <strong><?= formatRp($formData['total'] - $formData['dp']) ?></strong></div>
            </div>
            <div style="margin-top:16px;padding:12px;background:var(--cream-soft);border-radius:10px;">
                <div style="font-size:11px;color:var(--brown-400);margin-bottom:4px;">Jadwal Booking</div>
                <div style="font-size:13px;color:var(--brown-700);font-weight:500;"><?= date('d F Y', strtotime($formData['tanggal'])) ?> · <?= htmlspecialchars($formData['jam']) ?></div>
            </div>
        </div>
        <div class="pay-right">
            <div class="pay-card">
                <div class="pay-card-title"><i class='bx bx-credit-card'></i> Metode Pembayaran</div>
                <div class="metode-option selected" onclick="selectMetode(this,'Transfer Bank')">
                    <div class="metode-radio"></div>
                    <div class="metode-icon bank"><i class='bx bx-building'></i></div>
                    <div><div class="metode-text">Transfer Bank</div><div class="metode-sub">BCA, BNI, Mandiri</div></div>
                </div>
                <div class="bank-options show" id="bankOptions">
                    <div class="bank-item selected" onclick="selectBank(this,'BCA')"><div class="bank-logo">BCA</div><div class="bank-info"><div class="bank-name">Bank BCA</div><div class="bank-number">8120 3456 7890</div><div class="bank-an">a.n. Brilliant Beauty Studio</div></div><button type="button" class="bank-copy" onclick="event.stopPropagation();copyRek('812034567890')" title="Salin"><i class='bx bx-copy'></i></button></div>
                    <div class="bank-item" onclick="selectBank(this,'BNI')"><div class="bank-logo">BNI</div><div class="bank-info"><div class="bank-name">Bank BNI</div><div class="bank-number">0312 4567 8901</div><div class="bank-an">a.n. Brilliant Beauty Studio</div></div><button type="button" class="bank-copy" onclick="event.stopPropagation();copyRek('031245678901')" title="Salin"><i class='bx bx-copy'></i></button></div>
                    <div class="bank-item" onclick="selectBank(this,'Mandiri')"><div class="bank-logo">MDR</div><div class="bank-info"><div class="bank-name">Bank Mandiri</div><div class="bank-number">1280 0098 7654</div><div class="bank-an">a.n. Brilliant Beauty Studio</div></div><button type="button" class="bank-copy" onclick="event.stopPropagation();copyRek('128000987654')" title="Salin"><i class='bx bx-copy'></i></button></div>
                </div>
                <div class="metode-option" onclick="selectMetode(this,'QRIS')"><div class="metode-radio"></div><div class="metode-icon qris"><i class='bx bx-qr'></i></div><div><div class="metode-text">QRIS</div><div class="metode-sub">Scan & bayar via e-wallet</div></div></div>
               <div class="qris-box" id="qrisBox">

    <div class="qris-content">

        <div class="qris-img">
            <i class='bx bx-qr-scan'></i>
        </div>

        <div class="qris-hint">
            Scan QR code menggunakan aplikasi DANA
        </div>

        <div class="dana-actions">

            <button type="button"
                    class="btn-copy"
                    onclick="copyDana()">

                <i class='bx bx-copy'></i>
                Salin Nomor DANA

            </button>

           <a href="https://link.dana.id/"
   target="_blank"
   class="btn-open-dana">

    <i class='bx bx-wallet'></i>
    Buka DANA

</a>

        </div>

    </div>

</div>

<script>
function copyDana(){

    const nomorDana = "08123456789";

    navigator.clipboard.writeText(nomorDana);

    alert("Nomor DANA berhasil disalin");
}
</script>
                <div class="metode-option" onclick="selectMetode(this,'Bayar di Tempat')"><div class="metode-radio"></div><div class="metode-icon cash"><i class='bx bx-wallet'></i></div><div><div class="metode-text">Bayar di Tempat</div><div class="metode-sub">Bayar DP saat datang ke studio</div></div></div>
            </div>
            <div class="pay-card" id="uploadCard">
                <div class="pay-card-title"><i class='bx bx-upload'></i> Bukti Pembayaran DP</div>
                <p style="font-size:12px;color:var(--brown-400);font-weight:300;margin-bottom:12px;">Upload bukti transfer DP sebesar <strong style="color:var(--brown-700);"><?= formatRp($formData['dp']) ?></strong></p>
                <div class="upload-section show" id="uploadSection">
                    <div class="upload-zone" id="uploadZone">
                        <i class='bx bx-cloud-upload'></i>
                        <p>Klik atau seret file ke sini</p>
                        <p style="font-size:10px;margin-top:4px;color:var(--brown-300);">JPG, PNG maks. 2MB</p>
                        <input type="file" name="bukti_transfer" id="buktiInput" accept="image/jpeg,image/png,image/jpg">
                    </div>
                </div>
            </div>
            <div class="terms">
                <div class="terms-checkbox" id="termsCheck" onclick="toggleTerms()"></div>
                <div class="terms-text">Saya menyatakan data yang diisi sudah benar dan menyetujui <a href="javascript:void(0)">syarat & ketentuan</a> yang berlaku. Pembayaran DP bersifat non-refundable.</div>
            </div>
            <button type="submit" name="pay_now" class="btn-pay" id="btnPay" disabled><i class='bx bx-lock-alt'></i> Bayar Sekarang — <?= formatRp($formData['dp']) ?></button>
        </div>
    </div>
</form>
<?php else: ?>
<div class="struk-wrap">
    <div class="struk-success"><div class="struk-check"><i class='bx bx-check'></i></div><h2>Pembayaran Diterima</h2><p>Bukti pembayaran kamu sedang kami verifikasi</p></div>
    <div class="struk-status"><span class="struk-badge"><i class='bx bx-time-five'></i> Menunggu Konfirmasi Admin</span></div>
    <div class="struk-receipt" id="strukPrint">
        <div class="struk-header">
            <div class="struk-brand">BRILLIANT BEAUTY</div>
            <div class="struk-address">Jl. Contoh No. 123, Kota<br>Telp: 0812-3456-7890</div>
            <div class="struk-invoice-box"><div class="struk-invoice-label">No. Invoice</div><div class="struk-invoice-num"><?= $struk['no_invoice'] ?></div></div>
        </div>
        <div class="struk-section">
            <div class="struk-section-label">Informasi Customer</div>
            <div class="struk-row"><span class="struk-row-label">Nama</span><span class="struk-row-value"><?= htmlspecialchars($struk['nama']) ?></span></div>
            <div class="struk-row"><span class="struk-row-label">No. HP</span><span class="struk-row-value mono"><?= htmlspecialchars($struk['no_hp']) ?></span></div>
            <div class="struk-row"><span class="struk-row-label">Tanggal</span><span class="struk-row-value"><?= date('d F Y', strtotime($struk['tanggal'])) ?></span></div>
            <div class="struk-row"><span class="struk-row-label">Waktu</span><span class="struk-row-value"><?= htmlspecialchars($struk['jam']) ?></span></div>
            <div class="struk-row"><span class="struk-row-label">Dibayar pada</span><span class="struk-row-value"><?= $struk['created_at'] ?></span></div>
        </div>
        <div class="struk-section">
            <div class="struk-section-label">Detail Layanan</div>
            <div style="margin-bottom:6px;font-size:11px;color:var(--brown-400);font-weight:500;">Paket: <?= htmlspecialchars($struk['paket']) ?></div>
            <?php foreach($struk['services'] as $svc): ?>
            <div class="struk-item-row"><span class="struk-item-name"><?= htmlspecialchars($svc) ?></span><span class="struk-item-price" style="color:var(--green);">Termasuk</span></div>
            <?php endforeach; ?>
            <?php if (!empty($struk['addons'])): ?>
            <div style="margin:8px 0 6px;font-size:11px;color:var(--brown-400);font-weight:500;">Add-on:</div>
            <?php foreach($struk['addons'] as $a): ?>
            <div class="struk-item-row"><span class="struk-item-name"><?= htmlspecialchars($a['name']) ?></span><span class="struk-item-price"><?= formatRp($a['price']) ?></span></div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="struk-total-section">
            <div class="struk-total-row"><span class="struk-total-label">TOTAL</span><span class="struk-total-value"><?= formatRp($struk['total']) ?></span></div>
            <div class="struk-dp-section">
                <div class="struk-dp-row"><span class="struk-dp-row-label">DP Dibayar</span><span class="struk-dp-row-value"><?= formatRp($struk['dp']) ?></span></div>
                <div class="struk-dp-row" style="margin-top:6px;padding-top:6px;border-top:1px solid rgba(107,143,94,0.15);"><span class="struk-dp-row-label">Sisa Pembayaran</span><span class="struk-dp-row-value" style="color:var(--brown-400);"><?= formatRp($struk['sisa']) ?></span></div>
            </div>
        </div>
        <div class="struk-section" style="border-bottom:none;">
            <div class="struk-section-label">Metode Pembayaran</div>
            <div class="struk-row"><span class="struk-row-label">Metode</span><span class="struk-row-value"><?= htmlspecialchars($struk['metode']) ?></span></div>
            <?php if ($struk['bank']): ?>
            <div class="struk-row"><span class="struk-row-label">Bank</span><span class="struk-row-value"><?= htmlspecialchars($struk['bank']) ?></span></div>
            <?php endif; ?>
            <?php if ($struk['bukti']): ?>
            <div class="struk-row"><span class="struk-row-label">Bukti Transfer</span><span class="struk-row-value" style="color:var(--green);">✓ Terupload</span></div>
            <?php endif; ?>
            <div class="struk-row"><span class="struk-row-label">Status</span><span class="struk-row-value" style="color:var(--orange);font-weight:500;">⏳ Menunggu Konfirmasi</span></div>
        </div>
        <div class="struk-footer"><p class="thank">Terima kasih atas kepercayaan Anda</p><p>Simpan struk ini sebagai bukti booking.<br>Pembayaran sisa dibayarkan pada hari H.</p></div>
    </div>
    <div class="struk-actions">
        <button onclick="window.print()" class="btn-print"><i class='bx bx-printer'></i> Cetak Struk</button>
        <a href="javascript:void(0)" class="btn-pdf" onclick="alert('Fitur PDF akan segera tersedia')"><i class='bx bx-download'></i> Download PDF</a>
      <a href="https://wa.me/<?= $wa_admin ?>?text=<?= urlencode(
    "Halo, saya baru melakukan booking:\n\n".
    "Invoice: ".$struk['no_invoice']."\n".
    "Paket: ".$struk['paket']."\n".
    "Tanggal: ".date('d F Y', strtotime($struk['tanggal']))."\n".
    "Waktu: ".$struk['jam']."\n".
    "Total: ".formatRp($struk['total'])."\n".
    "DP: ".formatRp($struk['dp'])."\n".
    "Sisa: ".formatRp($struk['sisa'])."\n".
    "Metode: ".$struk['metode'].($struk['bank'] ? " (".$struk['bank'].")" : "")."\n".
    "Nama: ".$struk['nama']."\n".
    "No HP: ".$struk['no_hp']."\n\n".
    "Mohon dikonfirmasi. Terima kasih!"
) ?>" target="_blank" class="btn-wa">

    <i class='bx bxl-whatsapp'></i>
    Konfirmasi ke Admin

</a>
    </div>
    <div class="struk-home"><a href="jadwal_booking.php">← Kembali ke Beranda Booking</a></div>
</div>
<?php endif; ?>

</div>
<div class="toast" id="toast" style="position:fixed;bottom:30px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--brown-800);color:#f3ece3;padding:12px 24px;border-radius:12px;font-size:13px;z-index:9998;transition:transform .4s cubic-bezier(.4,0,.2,1);display:flex;align-items:center;gap:8px;box-shadow:0 12px 40px rgba(26,19,13,0.3);"><i class='bx bx-check-circle' style="font-size:16px;color:var(--brown-300);"></i> <span id="toastMsg"></span></div>

<?php if (!$submitted): ?>
<script>
let currentMetode='Transfer Bank',currentBank='BCA',termsChecked=false;
function selectMetode(el,m){document.querySelectorAll('.metode-option').forEach(o=>o.classList.remove('selected'));el.classList.add('selected');currentMetode=m;document.getElementById('metodeInput').value=m;document.getElementById('bankOptions').classList.toggle('show',m==='Transfer Bank');document.getElementById('qrisBox').classList.toggle('show',m==='QRIS');document.getElementById('uploadCard').style.display=m==='Bayar di Tempat'?'none':'';validateForm();}
function selectBank(el,b){document.querySelectorAll('.bank-item').forEach(i=>i.classList.remove('selected'));el.classList.add('selected');currentBank=b;document.getElementById('bankInput').value=b;validateForm();}
function toggleTerms(){termsChecked=!termsChecked;document.getElementById('termsCheck').classList.toggle('checked',termsChecked);validateForm();}
function validateForm(){let v=termsChecked;if(currentMetode==='Transfer Bank'&&!currentBank)v=false;document.getElementById('btnPay').disabled=!v;}
document.getElementById('buktiInput').addEventListener('change',function(){if(this.files&&this.files[0]){const f=this.files[0];const z=document.getElementById('uploadZone');z.classList.add('has-file');z.innerHTML='<i class=\'bx bx-check-circle\'></i><p class="upload-name">'+f.name+'</p><p style="font-size:10px;margin-top:2px;color:var(--green);">Klik untuk ganti</p><input type="file" name="bukti_transfer" id="buktiInput" accept="image/jpeg,image/png,image/jpg">';document.getElementById('buktiInput').addEventListener('change',arguments.callee);}});
function copyRek(n){navigator.clipboard.writeText(n).then(()=>showToast('Nomor rekening berhasil disalin'));}
function showToast(m){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=m;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2500);}
validateForm();
</script>
<?php endif; ?>

</body>
</html>