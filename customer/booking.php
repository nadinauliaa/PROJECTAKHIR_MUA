<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Start Booking - Brilliant Beauty</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

/* ================= BROWN LUXURY BACKGROUND ================= */
body{
    height:100vh;
    background:linear-gradient(135deg,#f5eee6,#e6d5c3,#d7b89a);
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    position:relative;
}

/* SOFT ORNAMENT */
.blob{
    position:absolute;
    width:300px;
    height:300px;
    border-radius:50%;
    filter:blur(70px);
    opacity:0.3;
    animation:float 8s ease-in-out infinite;
}

.blob1{
    background:#7a4e2d;
    top:-80px;
    left:-80px;
}

.blob2{
    background:#c49a6c;
    bottom:-80px;
    right:-80px;
    animation-delay:2s;
}

@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-30px);}
    100%{transform:translateY(0);}
}

/* ================= CARD ================= */
.card{
    width:420px;
    background:rgba(255,255,255,0.65);
    backdrop-filter:blur(12px);
    padding:40px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 25px 60px rgba(0,0,0,0.15);
    animation:fadeUp 1s ease;
}

/* TITLE */
.card h1{
    font-family:'Playfair Display';
    font-size:32px;
    color:#3b2a22;
    margin-bottom:10px;
}

/* SUBTITLE */
.card p{
    font-size:14px;
    color:#6b5a4d;
    margin-bottom:25px;
}

/* BUTTON */
.btn{
    display:inline-block;
    padding:12px 30px;
    background:#7a4e2d;
    color:#fff;
    border-radius:30px;
    text-decoration:none;
    font-size:14px;
    transition:0.3s;
}

.btn:hover{
    background:#5e3a21;
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

/* SECOND BUTTON */
.btn-outline{
    display:inline-block;
    margin-top:10px;
    padding:12px 30px;
    border:1px solid #7a4e2d;
    color:#7a4e2d;
    border-radius:30px;
    text-decoration:none;
    font-size:14px;
    transition:0.3s;
}

.btn-outline:hover{
    background:#7a4e2d;
    color:#fff;
}

/* ANIMATION */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* FOOTER */
.footer{
    position:absolute;
    bottom:20px;
    font-size:12px;
    color:#6b5a4d;
}
</style>

</head>

<body>

<!-- ORNAMENT -->
<div class="blob blob1"></div>
<div class="blob blob2"></div>

<!-- CARD -->
<div class="card">

    <h1>Start Your Beauty Experience</h1>
    <p>
        Pilih jadwal terbaik dan nikmati layanan makeup profesional dari Brilliant Beauty.
    </p>

    <a href="jadwal_booking.php" class="btn">Mulai Booking</a>
    <br>
    <a href="index.php" class="btn-outline">Kembali</a>

</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 Brilliant Beauty • Luxury Makeup Artist
</div>

</body>
</html>