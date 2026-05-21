<?php
session_start();

include "../koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM pricelist ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pricelist - Brilliant Beauty</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<style>

:root {
    --cream: #faf6f1;
    --cream-soft: #f3ece3;
    --brown-100: #ede5d8;
    --brown-200: #d4c4ae;
    --brown-300: #b89e7e;
    --brown-400: #9c7d5a;
    --brown-500: #7d6144;
    --brown-600: #5e4832;
    --brown-700: #3f3025;
    --brown-800: #2a1f17;
    --brown-900: #1a130d;
    --glass: rgba(255, 255, 255, 0.55);
    --glass-border: rgba(184, 158, 126, 0.18);
    --shadow-soft: 0 4px 24px rgba(42, 31, 23, 0.06);
    --shadow-hover: 0 12px 40px rgba(42, 31, 23, 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--cream);
    color: var(--brown-700);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ========== SIDEBAR ========== */
.sidebar {
    position: fixed;
    left: -280px;
    top: 0;
    width: 280px;
    height: 100%;
    background: var(--brown-800);
    padding: 0;
    transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1100;
    display: flex;
    flex-direction: column;
}

.sidebar.active { left: 0; }

.sidebar-header {
    padding: 32px 24px 24px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--brown-400), var(--brown-300));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 14px;
}

.sidebar-header h3 {
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    color: #f3ece3;
    font-weight: 500;
}

.sidebar-header p {
    font-size: 11px;
    color: var(--brown-300);
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 2px;
}

.sidebar-nav {
    flex: 1;
    padding: 16px 14px;
    overflow-y: auto;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    margin-bottom: 2px;
    text-decoration: none;
    color: rgba(243, 236, 227, 0.55);
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 400;
    transition: all 0.25s ease;
}

.sidebar-nav a i {
    font-size: 18px;
    opacity: 0.7;
    transition: opacity 0.25s;
}

.sidebar-nav a:hover {
    color: #f3ece3;
    background: rgba(255,255,255,0.05);
}

.sidebar-nav a:hover i { opacity: 1; }

.sidebar-nav a.current {
    color: #f3ece3;
    background: rgba(184, 158, 126, 0.12);
}

.sidebar-nav a.current i {
    opacity: 1;
    color: var(--brown-300);
}

.sidebar-close {
    position: absolute;
    top: 20px;
    right: 16px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(243,236,227,0.4);
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;
    font-size: 20px;
    background: none;
    border: none;
}

.sidebar-close:hover {
    color: #f3ece3;
    background: rgba(255,255,255,0.06);
}

/* OVERLAY */
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(26, 19, 13, 0.4);
    backdrop-filter: blur(2px);
    opacity: 0;
    visibility: hidden;
    transition: all 0.35s ease;
    z-index: 1050;
}

.overlay.active {
    opacity: 1;
    visibility: visible;
}

/* ========== TOPBAR ========== */
.topbar {
    height: 64px;
    background: rgba(250, 246, 241, 0.82);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 24px;
    border-bottom: 1px solid var(--glass-border);
    position: sticky;
    top: 0;
    z-index: 900;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.menu-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;
    color: var(--brown-600);
    font-size: 20px;
}

.menu-btn:hover {
    background: var(--cream-soft);
}

.topbar-title {
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    color: var(--brown-800);
    font-weight: 500;
    letter-spacing: 0.3px;
}

.topbar-user {
    font-size: 12.5px;
    color: var(--brown-500);
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 6px;
}

.topbar-user i {
    font-size: 15px;
    color: var(--brown-400);
}

/* ========== PAGE HEADER ========== */
.page-header {
    text-align: center;
    padding: 48px 24px 12px;
}

.page-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 34px;
    font-weight: 600;
    color: var(--brown-800);
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.page-header .line {
    width: 40px;
    height: 2px;
    background: var(--brown-300);
    margin: 14px auto;
    border-radius: 1px;
}

.page-header p {
    font-size: 13.5px;
    color: var(--brown-400);
    font-weight: 300;
    letter-spacing: 0.5px;
}

/* ========== MAIN LAYOUT ========== */
.wrapper {
    max-width: 1100px;
    margin: 28px auto 60px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 24px;
    padding: 0 24px;
}

/* ========== FILTER CARD ========== */
.filter-card {
    background: var(--glass);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 20px 16px;
    height: fit-content;
    position: sticky;
    top: 88px;
}

.filter-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: var(--brown-400);
    margin-bottom: 14px;
    padding-left: 4px;
}

.cat {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 14px;
    margin-bottom: 3px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13.5px;
    color: var(--brown-600);
    font-weight: 400;
    transition: all 0.25s ease;
    border: 1px solid transparent;
}

.cat i {
    font-size: 16px;
    opacity: 0.4;
    transition: opacity 0.25s;
}

.cat:hover {
    background: var(--cream-soft);
    color: var(--brown-700);
}

.cat:hover i { opacity: 0.7; }

.cat.active {
    background: var(--brown-700);
    color: #f3ece3;
    border-color: transparent;
}

.cat.active i { opacity: 0.8; color: var(--brown-200); }

.cat-count {
    font-size: 11px;
    font-weight: 400;
    opacity: 0.6;
}

.cat.active .cat-count { opacity: 0.7; }

/* ========== PACKAGE GRID ========== */
.pkg-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ========== PACKAGE CARD ========== */
.pkg {
    background: var(--glass);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    padding: 22px 24px;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.pkg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: var(--brown-300);
    opacity: 0;
    transition: opacity 0.35s;
    border-radius: 0 2px 2px 0;
}

.pkg:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
    border-color: rgba(184, 158, 126, 0.3);
}

.pkg:hover::before { opacity: 1; }

.pkg-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    gap: 16px;
}

.pkg-badge {
    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--brown-400);
    margin-bottom: 6px;
}

.pkg-title {
    font-family: 'Playfair Display', serif;
    font-size: 19px;
    font-weight: 600;
    color: var(--brown-800);
    line-height: 1.3;
}

.pkg-price {
    background: var(--brown-700);
    color: #f3ece3;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    letter-spacing: 0.3px;
    flex-shrink: 0;
}

.pkg-desc {
    font-size: 13px;
    color: var(--brown-500);
    font-weight: 300;
    line-height: 1.6;
    margin-bottom: 14px;
}

.pkg-features {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 16px;
}

.pkg-features span {
    font-size: 11.5px;
    padding: 5px 12px;
    background: var(--cream-soft);
    border: 1px solid rgba(184, 158, 126, 0.12);
    border-radius: 20px;
    color: var(--brown-500);
    font-weight: 400;
}

.pkg-actions {
    display: flex;
    gap: 10px;
}

.btn-book {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    border-radius: 10px;
    background: var(--brown-700);
    color: #f3ece3;
    text-decoration: none;
    font-size: 12.5px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.25s ease;
    border: none;
    cursor: pointer;
}

.btn-book:hover {
    background: var(--brown-800);
    transform: translateY(-1px);
}

.btn-book i { font-size: 14px; }

.btn-detail {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 10px;
    background: transparent;
    color: var(--brown-500);
    text-decoration: none;
    font-size: 12.5px;
    font-weight: 400;
    border: 1px solid var(--brown-200);
    transition: all 0.25s ease;
    cursor: pointer;
}

.btn-detail:hover {
    border-color: var(--brown-300);
    color: var(--brown-700);
    background: var(--cream-soft);
}

.btn-detail i { font-size: 14px; }

/* ========== HIDE CLASS ========== */
.hide {
    display: none !important;
}

/* ========== EMPTY STATE ========== */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    display: none;
}

.empty-state.show { display: block; }

.empty-state i {
    font-size: 40px;
    color: var(--brown-200);
    margin-bottom: 12px;
}

.empty-state p {
    font-size: 13.5px;
    color: var(--brown-400);
}

/* ========== MODAL ========== */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(26, 19, 13, 0.55);
    backdrop-filter: blur(6px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding: 20px;
}

.modal.show {
    display: flex;
}

.modal-box {
    width: 100%;
    max-width: 420px;
    background: var(--cream);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(26, 19, 13, 0.25);
    animation: modalIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes modalIn {
    from { transform: scale(0.92) translateY(20px); opacity: 0; }
    to { transform: scale(1) translateY(0); opacity: 1; }
}

.modal-img {
    width: 100%;
    height: 420px;
    object-fit: contain;
    background: #f3ece3;
    display: block;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.modal-body {
    padding: 24px;
    overflow-y: auto;
    max-height: 45vh;
}

.modal-close {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(8px);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--brown-700);
    font-size: 18px;
    transition: all 0.2s;
    z-index: 2;
}

.modal-close:hover {
    background: white;
    transform: scale(1.05);
}

.modal-badge {
    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--brown-400);
    margin-bottom: 6px;
}

.modal-title {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 600;
    color: var(--brown-800);
    margin-bottom: 4px;
}

.modal-price {
    font-size: 15px;
    font-weight: 500;
    color: var(--brown-500);
    margin-bottom: 12px;
}

.modal-desc {
    font-size: 13px;
    color: var(--brown-500);
    font-weight: 300;
    line-height: 1.7;
    margin-bottom: 16px;
}

.modal-includes-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--brown-400);
    margin-bottom: 10px;
}

.modal-includes ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 20px;
}

.modal-includes li {
    font-size: 13px;
    color: var(--brown-600);
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-includes li::before {
    content: '';
    width: 5px;
    height: 5px;
    background: var(--brown-300);
    border-radius: 50%;
    flex-shrink: 0;
}

.modal-book-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 13px;
    border-radius: 12px;
    background: var(--brown-700);
    color: #f3ece3;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: all 0.25s ease;
    border: none;
    cursor: pointer;
}

.modal-book-btn:hover {
    background: var(--brown-800);
}

.modal-book-btn i { font-size: 16px; }

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
    .wrapper {
        grid-template-columns: 1fr;
    }

    .filter-card {
        position: static;
    }

    .filter-card .cat-list {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .filter-card .cat-list .cat {
        white-space: nowrap;
        margin-bottom: 0;
        flex-shrink: 0;
        padding: 9px 16px;
        font-size: 12.5px;
    }

    .page-header h1 {
        font-size: 28px;
    }

    .pkg-top {
        flex-direction: column;
        gap: 8px;
    }
.modal-box {
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    background: var(--cream);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(26, 19, 13, 0.25);
    display: flex;
    flex-direction: column;
}

    .modal-img {
        height: 160px;
    }
}

.pkg-img {
    width: 100%;
    height: 180px;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 14px;
}

.pkg-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}

/* efek zoom halus */
.pkg:hover .pkg-img img {
    transform: scale(1.05);
}

/* ========== SCROLLBAR ========== */
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--brown-200); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--brown-300); }

</style>
</head>

<body>

<!-- ========== SIDEBAR ========== -->
<div class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeMenu()">
        <i class='bx bx-x'></i>
    </button>
    <div class="sidebar-header">
        <div class="avatar">
            <i class='bx bx-user'></i>
        </div>
        <h3><?php echo $_SESSION['nama']; ?></h3>
        <p>Customer</p>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php"><i class='bx bx-home'></i> Beranda</a>
        <a href="portofolio.php"><i class='bx bx-image'></i> Portofolio</a>
        <a href="pricelist.php" class="current"><i class='bx bx-wallet'></i> Price List</a>
        <a href="jadwal.php"><i class='bx bx-calendar'></i> Jadwal</a>
        <a href="booking.php"><i class='bx bx-edit'></i> Booking</a>
        <a href="contact.php"><i class='bx bx-phone'></i> Contact</a>
    </nav>
</div>

<!-- ========== OVERLAY ========== -->
<div class="overlay" id="overlay" onclick="closeMenu()"></div>

<!-- ========== TOPBAR ========== -->
<div class="topbar">
    <div class="topbar-left">
        <div class="menu-btn" onclick="openMenu()">
            <i class='bx bx-menu'></i>
        </div>
        <div class="topbar-title">Pricelist</div>
    </div>
    <div class="topbar-user">
        <i class='bx bx-user-circle'></i>
        <?php echo $_SESSION['nama']; ?>
    </div>
</div>

<!-- ========== PAGE HEADER ========== -->
<div class="page-header">
    <h1>Our Packages</h1>
    <div class="line"></div>
    <p>Choose the perfect beauty experience for your special day</p>
</div>

<!-- ========== WRAPPER ========== -->
<div class="wrapper">

    <!-- FILTER -->
    <div class="filter-card">
        <div class="filter-label">Category</div>
        <div class="cat-list">
            <div class="cat active" onclick="filter('all', this)">
                <span>All</span>
                <span class="cat-count">4</span>
            </div>
            <div class="cat" onclick="filter('wedding', this)">
                <span>Wedding</span>
                <span class="cat-count">4</span>
            </div>
            <div class="cat" onclick="filter('graduation', this)">
                <span>Graduation</span>
                <span class="cat-count">0</span>
            </div>
            <div class="cat" onclick="filter('photoshoot', this)">
                <span>Photoshoot</span>
                <span class="cat-count">0</span>
            </div>
            <div class="cat" onclick="filter('engagement', this)">
                <span>Engagement</span>
                <span class="cat-count">0</span>
            </div>
        </div>
    </div>

    <!-- PACKAGE LIST -->
<!-- PACKAGE LIST -->
<div class="pkg-grid">

<?php while($row = mysqli_fetch_assoc($data)) { ?>

    <div class="pkg <?= strtolower($row['kategori']) ?>" data-cat="<?= strtolower($row['kategori']) ?>">


        <div class="pkg-top">
            <div>
                <div class="pkg-badge"><?= htmlspecialchars($row['kategori']) ?></div>
                <div class="pkg-title"><?= htmlspecialchars($row['judul']) ?></div>
            </div>
            <div class="pkg-price">Rp <?= htmlspecialchars($row['harga']) ?></div>
        </div>

        

        <div class="pkg-desc">
            <?= htmlspecialchars($row['deskripsi']) ?>
        </div>

        <div class="pkg-features">
            <?php 
            $inc = explode('|', $row['includes']);
            foreach($inc as $item) {
                echo "<span>".trim($item)."</span>";
            }
            ?>
        </div>

        <div class="pkg-actions">
    <a href="booking.php" class="btn-book">
        <i class='bx bx-calendar-check'></i> Book Now
    </a>

    <button class="btn-detail"
    onclick="openModal(
        '<?= htmlspecialchars($row['kategori']) ?>',
        '<?= htmlspecialchars($row['judul']) ?>',
        '<?= htmlspecialchars($row['harga']) ?>',
        '<?= $row['gambar'] ?>',
        `<?= htmlspecialchars($row['deskripsi']) ?>`,
        `<?= htmlspecialchars($row['includes']) ?>`
    )">
    <i class='bx bx-show'></i> Detail
</button>
</div>

    </div>

<?php } ?>

<!-- Empty State -->
<div class="empty-state" id="emptyState">
    <i class='bx bx-package'></i>
    <p>Tidak ada package untuk kategori ini</p>
</div>

</div>
<!-- ========== MODAL ========== -->
<div class="modal" id="modal">
    <div class="modal-box" style="position:relative;">
        <button class="modal-close" onclick="closeModal()">
            <i class='bx bx-x'></i>
        </button>
        <div class="pkg-img">
          <img class="modal-img" id="modal-img" src="" alt="">
        </div>
        <div class="modal-body">
            <div class="modal-badge" id="modal-badge"></div>
            <div class="modal-title" id="modal-title"></div>
            <div class="modal-price" id="modal-price"></div>
            <div class="modal-desc" id="modal-desc"></div>
            <div class="modal-includes-label">Includes</div>
            <div class="modal-includes" id="modal-includes"></div>
            <a href="booking.php" class="modal-book-btn">
                <i class='bx bx-calendar-check'></i> Book This Package
            </a>
        </div>
    </div>
</div>

<script>
/* ========== SIDEBAR ========== */
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

function openMenu() {
    sidebar.classList.add('active');
    overlay.classList.add('active');
}

function closeMenu() {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
}

/* ========== FILTER ========== */
function filter(category, el) {
    const items = document.querySelectorAll('.pkg');
    const btns = document.querySelectorAll('.cat');
    const empty = document.getElementById('emptyState');
    let visibleCount = 0;

    btns.forEach(b => b.classList.remove('active'));
    el.classList.add('active');

    items.forEach(item => {
        if (category === 'all' || item.dataset.cat === category) {
            item.classList.remove('hide');
            visibleCount++;
        } else {
            item.classList.add('hide');
        }
    });

    if (visibleCount === 0) {
        empty.classList.add('show');
    } else {
        empty.classList.remove('show');
    }
}


function openModal(badge, title, price, img, desc, includes) {

    document.getElementById('modal-badge').textContent = badge;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-price').textContent =
        'Rp ' + Number(price).toLocaleString('id-ID');

    // PATH GAMBAR
    document.getElementById('modal-img').src =
        './images/' + img;

    document.getElementById('modal-desc').textContent = desc;

    let items = includes.split('|');
    let html = '<ul>';

    items.forEach(item => {
        html += '<li>' + item.trim() + '</li>';
    });

    html += '</ul>';

    document.getElementById('modal-includes').innerHTML = html;

    document.getElementById('modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.remove('show');
    document.body.style.overflow = '';
}

/* Close modal on backdrop click */
document.getElementById('modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

/* Close modal on Escape */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeMenu();
    }
});
</script>

</body>
</html>