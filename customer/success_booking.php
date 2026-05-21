<?php
session_start();
include '../koneksi.php';

$data = json_decode($_POST['all_data'], true);

$total = $data['total_hidden'];
$dp    = $data['dp_hidden'];

?>

<!DOCTYPE html>
<html>
<head>
<title>Struk Booking</title>
<style>
body{
    font-family:Inter;
    background:#eee;
    display:flex;
    justify-content:center;
    padding:40px;
}

.card{
    background:white;
    width:400px;
    padding:25px;
    border-radius:14px;
}

h2{color:#3f3025;}
.row{display:flex;justify-content:space-between;margin:8px 0;}
hr{border:none;border-top:1px solid #ddd;margin:10px 0;}
</style>
</head>
<body>

<div class="card">
    <h2>✅ Booking Berhasil</h2>

    <div class="row"><span>Total</span><span>Rp <?= number_format($total,0,',','.') ?></span></div>
    <div class="row"><span>DP</span><span>Rp <?= number_format($dp,0,',','.') ?></span></div>

    <hr>

    <p style="font-size:12px;color:gray;">
        Simpan struk ini sebagai bukti booking
    </p>
</div>

</body>
</html>