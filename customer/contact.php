<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - MUA</title>

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<!-- Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', sans-serif;
  color: white;
  min-height: 100vh;
  background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)),
              url('https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9');
  background-size: cover;
  background-position: center;
}

/* HEADER */
.header {
  text-align: center;
  padding: 60px 20px 30px;
}

.header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 42px;
}

.header p {
  letter-spacing: 4px;
  font-size: 14px;
  opacity: 0.8;
}

/* GRID */
.container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 25px;
  padding: 40px;
}

/* CARD */
.card {
  backdrop-filter: blur(15px);
  background: rgba(255,255,255,0.1);
  border-radius: 15px;
  padding: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: 0.3s;
  cursor: pointer;
}

.card:hover {
  transform: translateY(-8px) scale(1.02);
  background: rgba(255,255,255,0.2);
}

.card-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.card i {
  font-size: 28px;
}

.card h3 {
  font-size: 18px;
}

.card p {
  font-size: 13px;
  opacity: 0.7;
}

/* COLORS */
.instagram { color: #E1306C; }
.tiktok { color: #fff; }
.facebook { color: #1877F2; }
.twitter { color: #1DA1F2; }

/* EXTRA SECTION */
.extra {
  margin: 40px;
  padding: 30px;
  border-radius: 15px;
  background: rgba(0,0,0,0.5);
  text-align: center;
}

.extra h2 {
  font-family: 'Playfair Display';
  margin-bottom: 10px;
}

.extra p {
  opacity: 0.8;
  margin-bottom: 20px;
}

.extra button {
  padding: 12px 25px;
  border: none;
  border-radius: 25px;
  background: gold;
  color: black;
  font-weight: 500;
  cursor: pointer;
  transition: 0.3s;
}

.extra button:hover {
  background: orange;
}

/* FOOTER */
.footer {
  margin-top: 40px;
  padding: 20px;
  text-align: center;
  background: rgba(0,0,0,0.8);
}

.footer p {
  font-size: 14px;
  opacity: 0.7;
}

.footer .social {
  margin-top: 10px;
}

.footer i {
  margin: 0 10px;
  cursor: pointer;
  transition: 0.3s;
}

.footer i:hover {
  color: gold;
}
/* TOPBAR */
.topbar{
  position:fixed;
  top:0;
  left:0;
  width:100%;
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:15px 20px;
  background:rgba(0,0,0,0.6);
  backdrop-filter:blur(10px);
  z-index:1000;
}

.brand{
  font-weight:600;
  color:white;
}

.menu-toggle{
  font-size:20px;
  cursor:pointer;
  color:white;
}

/* SIDEBAR */
.sidebar{
  position:fixed;
  top:0;
  left:-260px;
  width:250px;
  height:100%;
  background:rgba(0,0,0,0.9);
  padding:60px 20px;
  transition:0.3s;
  z-index:2000;
}

.sidebar.active{
  left:0;
}

.sidebar .menu a{
  display:block;
  color:white;
  padding:12px;
  text-decoration:none;
  margin:8px 0;
  border-radius:8px;
  transition:0.3s;
}

.sidebar .menu a:hover{
  background:gold;
  color:black;
}
</style>
</head>

<body>
<!-- TOPBAR -->
<div class="topbar">

  <div class="menu-toggle" onclick="toggleSidebar()">
    <i class='fas fa-ellipsis-v'></i>
  </div>

  <div class="brand">
    <i class="fas fa-sparkles"></i>
    Brilliant Beauty
  </div>

</div>

<div class="sidebar" id="sidebar">

    <div class="menu">
        <a href="index.php"><i class='fas fa-home'></i> Beranda</a>
        <a href="portofolio.php"><i class='fas fa-image'></i> Portofolio</a>
        <a href="pricelist.php"><i class='fas fa-wallet'></i> Price List</a>
        <a href="jadwal.php"><i class='fas fa-calendar'></i> Jadwal</a>
        <a href="booking.php"><i class='fas fa-pen'></i> Booking</a>
          <a href="status_booking.php"><i class='bx bx-edit'></i>Pembayaran</a>
        <a href="contact.php"><i class='fas fa-phone'></i> Contact</a>
    </div>

</div>
<div class="header">
  <h1>Contact Us</h1>
  <p>OFFICIAL MUA ACCOUNT</p>
</div>

<div class="container">

  <div class="card" onclick="window.open('https://www.instagram.com/nymas_makeup?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==', '_blank')">
  <div class="card-left">
    <i class="fab fa-instagram instagram"></i>
    <div>
      <h3>Instagram</h3>
      <p>@nymas_makeup</p>
    </div>
  </div>
  <i class="fas fa-chevron-right"></i>
</div>

  <div class="card">
    <div class="card-left">
      <i class="fab fa-tiktok tiktok"></i>
      <div>
        <h3>TikTok</h3>
        <p>@BrilliantBeauty</p>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <div class="card" onclick="window.open('https://wa.me/6285601644685', '_blank')">
  <div class="card-left">
    <i class="fab fa-whatsapp" style="color:#25D366;"></i>
    <div>
      <h3>WhatsApp</h3>
      <p>Chat langsung dengan kami</p>
    </div>
  </div>
  <i class="fas fa-chevron-right"></i>
</div>

  <div class="card">
    <div class="card-left">
      <i class="fab fa-x-twitter twitter"></i>
      <div>
        <h3>Twitter</h3>
        <p>@Brilliant_Beauty</p>
      </div>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

</div>

<div class="extra">
  <h2>Book Your Makeup Session</h2>
  <p>Available for wedding, graduation, party & photoshoot</p>
  <a href="booking.php" class="btn-book">
    <button>Book Now</button>
  </a>
</div>

<div class="footer">
  <p>© 2026 Brilliant Beauty MUA | All Rights Reserved</p>
  <div class="social">
    <i class="fab fa-instagram"></i>
    <i class="fab fa-tiktok"></i>
    <i class="fab fa-facebook"></i>
  </div>
</div>

<script>
function toggleSidebar(){
  document.getElementById("sidebar").classList.toggle("active");
}
</script>



</body>
</html>