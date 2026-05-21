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
<title>Portofolio - Brilliant Beauty</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>

/* ================= GLOBAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter', sans-serif;
}

body{
    background: linear-gradient(135deg,#faf7f2,#f3ede6,#faf7f2);
    color:#3d3029;
    overflow-x:hidden;
}

/* glow background seperti index */
body::before{
    content:'';
    position:fixed;
    top:-200px;
    left:-200px;
    width:500px;
    height:500px;
    background:radial-gradient(circle, rgba(196,149,106,0.18), transparent 60%);
    z-index:-1;
}

body::after{
    content:'';
    position:fixed;
    bottom:-200px;
    right:-200px;
    width:500px;
    height:500px;
    background:radial-gradient(circle, rgba(139,111,92,0.15), transparent 60%);
    z-index:-1;
}

/* ================= SIDEBAR ================= */
.sidebar{
    position:fixed;
    left:-280px;
    top:0;
    width:280px;
    height:100%;
    background:rgba(42,31,23,0.97);
    backdrop-filter: blur(12px);
    padding:30px 20px;
    transition:0.35s ease;
    z-index:1000;
    box-shadow:10px 0 30px rgba(0,0,0,0.2);
}

.sidebar.active{ left:0; }

.profile{
    text-align:center;
    margin-bottom:25px;
}

.avatar{
    width:70px;
    height:70px;
    background:linear-gradient(135deg,#c4956a,#8b6f5c);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    color:white;
    font-size:28px;
    margin-bottom:10px;
}

.profile h3{ color:#fff; font-size:15px; }
.profile p{ color:#cfcfcf; font-size:12px; }

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 14px;
    margin:10px 0;
    text-decoration:none;
    color:#f5e9df;
    border-radius:10px;
    transition:0.3s;
    font-size:14px;
}

.menu a:hover{
    background:#c4956a;
    color:#2a1f17;
}

/* ================= OVERLAY ================= */
.overlay{
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.3);
    display:none;
    z-index:999;
}

.overlay.active{ display:block; }

/* ================= TOPBAR ================= */
.topbar{
    height:70px;
    background:rgba(250,247,242,0.85);
    backdrop-filter: blur(12px);
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 25px;
    border-bottom:1px solid rgba(196,149,106,0.2);
    position:sticky;
    top:0;
    z-index:999;
}

.left-top{
    display:flex;
    align-items:center;
    gap:15px;
}

.menu-btn{
    font-size:26px;
    cursor:pointer;
    color:#3d3029;
}

.title{
    font-family:'Playfair Display';
    font-size:18px;
    font-weight:600;
    color:#3d3029;
}

/* ================= HEADER ================= */
.header{
    padding:80px 20px 30px;
    text-align:center;
    position:relative;
}

.header::before{
    content:'';
    position:absolute;
    top:0;
    left:50%;
    transform:translateX(-50%);
    width:120px;
    height:2px;
    background:#c4956a;
    opacity:0.6;
}

.header h1{
    font-family:'Playfair Display';
    font-size:42px;
}

.header p{
    color:#8b6f5c;
    font-size:12px;
    letter-spacing:2px;
    text-transform:uppercase;
}

/* ================= FILTER ================= */
.filter{
    text-align:center;
    margin-bottom:25px;
}

.filter button{
    margin:5px;
    padding:10px 18px;
    border:none;
    border-radius:30px;
    background:rgba(196,149,106,0.15);
    color:#3d3029;
    cursor:pointer;
    transition:0.3s;
    font-size:12px;
    border:1px solid rgba(196,149,106,0.3);
}

.filter button:hover{
    background:#c4956a;
    color:#fff;
}

/* ================= GALLERY ================= */
.gallery{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:18px;
    padding:20px 40px 60px;
}

.item{
    position:relative;
    overflow:hidden;
    border-radius:18px;
    cursor:pointer;
    box-shadow:0 10px 30px rgba(61,48,41,0.08);
    transition:0.4s;
}

.item:hover{
    transform:translateY(-5px);
    box-shadow:0 20px 40px rgba(61,48,41,0.15);
}

.item img{
    width:100%;
    height:300px;
    object-fit:cover;
    transition:0.4s;
}

.item:hover img{
    transform:scale(1.05);
}

.overlay-text{
    position:absolute;
    bottom:0;
    width:100%;
    padding:12px;
    color:white;
    background:linear-gradient(transparent,rgba(0,0,0,0.7));
}

/* ================= LIGHTBOX ================= */
.lightbox{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(42,31,23,0.92);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:9999;
}

.lightbox.active{ display:flex; }

.lightbox img{
    max-width:90%;
    max-height:85%;
    border-radius:12px;
}

.close{
    position:absolute;
    top:20px;
    right:25px;
    font-size:30px;
    color:white;
    cursor:pointer;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="profile">
        <div class="avatar">
            <i class='bx bx-user'></i>
        </div>
        <h3><?php echo $_SESSION['nama']; ?></h3>
        <p>Customer</p>
    </div>

    <div class="menu">
        <a href="index.php"><i class='bx bx-home'></i> Beranda</a>
        <a href="portofolio.php"><i class='bx bx-image'></i> Portofolio</a>
        <a href="pricelist.php"><i class='bx bx-wallet'></i> Price List</a>
        <a href="jadwal.php"><i class='bx bx-calendar'></i> Jadwal</a>
        <a href="booking.php"><i class='bx bx-edit'></i> Booking</a>
        <a href="contact.php"><i class='bx bx-phone'></i> Contact</a>
    </div>

</div>

<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<!-- TOPBAR -->
<div class="topbar">
    <div class="left-top">
        <div class="menu-btn" onclick="openMenu()">
            <i class='bx bx-menu'></i>
        </div>
        <div class="title">Portofolio</div>
    </div>

    <div><?php echo $_SESSION['nama']; ?></div>
</div>

<!-- HEADER -->
<div class="header">
    <h1>Our Makeup Gallery</h1>
    <p>Elegant transformation by Brilliant Beauty</p>
</div>

<!-- FILTER -->
<div class="filter">
    <button onclick="filterSelection('all')">All</button>
    <button onclick="filterSelection('wedding')">Wedding</button>
    <button onclick="filterSelection('graduation')">Graduation</button>
    <button onclick="filterSelection('request')">Request</button>
</div>

<?php
include "../koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM portofolio ORDER BY id DESC");
?>

<!-- GALLERY -->
<div class="gallery">

<?php while($row = mysqli_fetch_assoc($data)) { ?>

    <div class="item <?= $row['kategori']; ?>">
        <img src="../customer/images/<?= $row['foto']; ?>">
    </div>

<?php } ?>

</div>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox">
    <span class="close" onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img">
</div>

<script>

const sidebar=document.getElementById("sidebar");
const overlay=document.getElementById("overlay");

function openMenu(){
    sidebar.classList.add("active");
    overlay.classList.add("active");
}

function closeMenu(){
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
}

/* FILTER */
function filterSelection(category){
    let items=document.querySelectorAll(".item");

    items.forEach(item=>{
        item.style.display="none";
        if(category==="all" || item.classList.contains(category)){
            item.style.display="block";
        }
    });
}

/* LIGHTBOX */
document.querySelectorAll(".item img").forEach(img=>{
    img.onclick=()=>{
        document.getElementById("lightbox").classList.add("active");
        document.getElementById("lightbox-img").src=img.src;
    }
});

function closeLightbox(){
    document.getElementById("lightbox").classList.remove("active");
}

</script>

</body>
</html> 
