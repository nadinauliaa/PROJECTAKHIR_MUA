<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRILLIANT BEAUTY</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Lato:wght@300;400;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#faf7f2',
                        warmBrown: '#3d3029',
                        bronze: '#c4956a',
                        mutedBrown: '#8b6f5c',
                        lightTan: '#e8ddd0',
                        softPink: '#f0e4da',
                        deepBrown: '#2a1f17',
                        rose: '#d4a59a',
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        body: ['Lato', 'sans-serif'],
                        script: ['Great Vibes', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Lato', sans-serif; background: #faf7f2; color: #3d3029; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #faf7f2; }
        ::-webkit-scrollbar-thumb { background: #c4956a; border-radius: 3px; }

        /* Fade In Up Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(-60px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(60px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-on-scroll {
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .animate-on-scroll.visible {
            opacity: 1;
        }
        .fade-up.visible { animation: fadeInUp 0.8s ease forwards; }
        .fade-in.visible { animation: fadeIn 1s ease forwards; }
        .scale-in.visible { animation: scaleIn 0.8s ease forwards; }
        .slide-left.visible { animation: slideLeft 0.8s ease forwards; }
        .slide-right.visible { animation: slideRight 0.8s ease forwards; }

        /* Parallax Image */
        .parallax-img {
            transition: transform 0.3s ease;
        }
        .parallax-img:hover {
            transform: scale(1.03);
        }

        /* Divider */
        .ornament-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: center;
        }
        .ornament-divider::before,
        .ornament-divider::after {
            content: '';
            width: 60px;
            height: 1px;
            background: #c4956a;
        }

        /* Image Frame */
        .vintage-frame {
            border: 8px solid #e8ddd0;
            box-shadow: 0 8px 40px rgba(61,48,41,0.12);
        }

        /* Nav link hover */
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: #c4956a;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }

        /* Gallery hover overlay */
        .gallery-item .overlay {
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .gallery-item:hover .overlay {
            opacity: 1;
        }

        /* Button style */
        .btn-vintage {
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        .btn-vintage::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }
        .btn-vintage:hover::before {
            left: 100%;
        }

        /* Mobile menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .mobile-menu.open {
            transform: translateX(0);
        }


        /* Stagger children */
        .stagger-children > *:nth-child(1) { transition-delay: 0.1s; }
        .stagger-children > *:nth-child(2) { transition-delay: 0.2s; }
        .stagger-children > *:nth-child(3) { transition-delay: 0.3s; }
        .stagger-children > *:nth-child(4) { transition-delay: 0.4s; }
        .stagger-children > *:nth-child(5) { transition-delay: 0.5s; }
        .stagger-children > *:nth-child(6) { transition-delay: 0.6s; }

        /* Marquee */
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .marquee-track {
            animation: marquee 25s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }
        .menu-toggle{
    position: fixed;
    top: 20px;
    left: 20px;
    width: 45px;
    height: 45px;
    border: none;
    border-radius: 12px;
    background: #ffffff;
    color: #333;
    font-size: 22px;
    cursor: pointer;
    z-index: 1001;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.menu-toggle{
    background: #c4956a;
    color: white;
    box-shadow: 0 8px 20px rgba(196,149,106,0.3);
}
.sidebar{
    position: fixed;
    top: 0;
    left: -280px;
    width: 260px;
    height: 100vh;
    
    background: #ffffff !important;
    opacity: 1 !important;

    z-index: 3000;

    box-shadow: 8px 0 25px rgba(0,0,0,0.15);

    transition: left 0.4s ease;

    padding-top: 80px;

    overflow-y: auto;

    backface-visibility: hidden;
    transform: translateZ(0);
}
.sidebar.active{
    left: 0;
}
.sidebar::before{
    content:'';
    position:absolute;
    inset:0;
    background:#ffffff;
    z-index:-1;
}

.menu a{
    display: block;
    padding: 16px 25px;
    color: #333;
    text-decoration: none;
    font-size: 15px;
    border-bottom: 1px solid #eee;
    transition: 0.3s;
}

.menu a i{
    width: 25px;
}

.menu a:hover{
    background: #f8f8f8;
    padding-left: 32px;
    color: #c4956a;
}

/* Overlay */
.sidebar-overlay{
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    opacity: 0;
    visibility: hidden;
    transition: 0.3s;
    z-index: 999;
}

.sidebar-overlay.show{
    opacity: 1;
    visibility: visible;
}


    </style>
</head>
<body>

    <!-- ==================== NAVIGATION ==================== -->
    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 transition-all duration-500" style="background: transparent;">
        <div class="max-w-7xl mx-auto px-6 md:px-12 py-5 flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="font-script text-3xl md:text-4xl text-warmBrown tracking-wide">
                BB
            </a>

 

<!-- TITIK TIGA -->
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-ellipsis-v"></i>
    </button>

    <!-- OVERLAY -->
   <div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

        <div class="menu">
            <a href="index.php"><i class='fas fa-home'></i> Beranda</a>
            <a href="portofolio.php"><i class='fas fa-image'></i> Portofolio</a>
            <a href="pricelist.php"><i class='fas fa-wallet'></i> Price List</a>
            <a href="jadwal.php"><i class='fas fa-calendar'></i> Jadwal</a>
            <a href="booking.php"><i class='fas fa-pen'></i> Booking</a>
            <a href="contact.php"><i class='fas fa-phone'></i> Contact</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

    </div>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#about" class="nav-link text-sm font-body font-light tracking-widest uppercase text-warmBrown/80 hover:text-warmBrown transition-colors">Beranda</a>
                <a href="#portfolio" class="nav-link text-sm font-body font-light tracking-widest uppercase text-warmBrown/80 hover:text-warmBrown transition-colors">Portfolio</a>
                <a href="#jadwal" class="nav-link text-sm font-body font-light tracking-widest uppercase text-warmBrown/80 hover:text-warmBrown transition-colors">Jadwal</a>
                <a href="#get" class="nav-link text-sm font-body font-light tracking-widest uppercase text-warmBrown/80 hover:text-warmBrown transition-colors">Contact</a>
            </div>
            <!-- Mobile Toggle -->
            <button id="menuToggle" class="md:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 group">
                <span class="w-6 h-px bg-warmBrown transition-all duration-300 group-hover:w-5" id="bar1"></span>
                <span class="w-5 h-px bg-warmBrown transition-all duration-300 group-hover:w-6" id="bar2"></span>
                <span class="w-4 h-px bg-warmBrown transition-all duration-300 group-hover:w-5" id="bar3"></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu fixed inset-0 z-[60] bg-cream flex flex-col items-center justify-center gap-8">
       <button id="menuClose" class="absolute top-6 right-6 text-bronze text-3xl">
    ✕
</button>

<a href="#about" class="mobile-link font-display text-2xl text-warmBrown hover:text-bronze transition-colors">About</a>
        <a href="#portfolio" class="mobile-link font-display text-2xl text-warmBrown hover:text-bronze transition-colors">Portfolio</a>
        <a href="#services" class="mobile-link font-display text-2xl text-warmBrown hover:text-bronze transition-colors">Services</a>
        <a href="#stories" class="mobile-link font-display text-2xl text-warmBrown hover:text-bronze transition-colors">Stories</a>
        <a href="#contact" class="mobile-link font-display text-2xl text-warmBrown hover:text-bronze transition-colors">Contact</a>
        <div class="ornament-divider mt-4">
            <span class="font-script text-bronze text-xl">O</span>
        </div>
    </div>

    <!-- ==================== HERO SECTION ==================== -->
    <section class="relative min-h-screen flex items-end overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="images/P1.jfif" alt="Wedding Hero" class="w-full h-full object-cover parallax-img">
            <div class="absolute inset-0 bg-gradient-to-t from-deepBrown/70 via-deepBrown/30 to-deepBrown/10"></div>
        </div>
       
 <div class="relative z-10 w-full max-w-7xl mx-auto px-6 md:px-12 pb-16 md:pb-24">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">

        <!-- LEFT SIDE -->
        <div>

            <!-- Welcome + Nama (sejajar) -->
            <div class="flex items-end gap-3 mb-4 animate-on-scroll fade-up" style="animation-delay: 0.2s;">
                <p class="font-script text-rose/90 text-2xl md:text-3xl">
                    Welcome
                </p>

                <h2 class="font-body text-cream/80 text-lg md:text-2xl">
                    <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guest'; ?>
                </h2>
            </div>

            <!-- Judul utama -->
            <h1 class="font-display text-5xl md:text-7xl lg:text-8xl font-bold text-cream leading-[0.95] tracking-tight mb-6 animate-on-scroll fade-up" style="animation-delay: 0.4s;">
                Brilliant<br>Beauty
            </h1>

        </div>

        <!-- RIGHT SIDE -->
        <div class="text-left md:text-right">

            <div class="w-16 h-px bg-bronze mb-6 ml-auto md:ml-auto animate-on-scroll fade-up" style="animation-delay: 0.6s;"></div>

            <p class="font-body text-cream/80 text-sm md:text-base font-light tracking-widest uppercase mb-8 animate-on-scroll fade-up" style="animation-delay: 0.7s;">
                Sentuhan Makeup Elegan untuk Momen Istimewamu
            </p>

            <a href="#portfolio"
               class="btn-vintage inline-block border border-cream/40 text-cream px-8 py-3.5 text-xs font-body tracking-[0.25em] uppercase hover:bg-cream hover:text-warmBrown transition-all duration-400 animate-on-scroll fade-up"
               style="animation-delay: 0.9s;">
                Temukan Look Terbaikmu
            </a>

        </div>

    </div>

</div>
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2 animate-on-scroll fade-in" style="animation-delay: 1.2s;">
            <span class="text-cream/50 text-[10px] font-body tracking-[0.3em] uppercase">Scroll</span>
            <div class="w-px h-8 bg-cream/30 relative overflow-hidden">
                <div class="w-full h-3 bg-cream/70 absolute top-0 animate-bounce"></div>
            </div>
        </div>
    </section>

    <!-- ==================== MARQUEE STRIP ==================== -->
    <div class="bg-warmBrown py-4 overflow-hidden">
        <div class="marquee-track flex items-center whitespace-nowrap" style="width: max-content;">
            <span class="font-script text-cream/70 text-xl mx-8">Weding</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8">Graduation</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8"> Engagement</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8">Request</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8">Photoshoot</span>
            <span class="text-bronze mx-2">✦</span>
            
            <!-- Duplicate for seamless loop -->
             <span class="font-script text-cream/70 text-xl mx-8">Weding</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8">Graduation</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8"> Engagement</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8">Request</span>
            <span class="text-bronze mx-2">✦</span>
            <span class="font-script text-cream/70 text-xl mx-8">Photoshoot</span>
            <span class="text-bronze mx-2">✦</span>
            
          
        </div>
    </div>

    <!-- ==================== ABOUT SECTION ==================== -->
    <section id="about" class="py-24 md:py-32 bg-cream">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <!-- Left: Images -->
                <div class="relative animate-on-scroll slide-left">
                    <div class="vintage-frame overflow-hidden">
                        <img src=" images/wedding.jpeg" alt="Photographer Portrait" class="w-full h-[500px] md:h-[600px] object-cover parallax-img">
                    </div>
                    <!-- Floating small image -->
                    <div class="absolute -bottom-8 -right-4 md:-right-8 w-40 md:w-52 vintage-frame overflow-hidden shadow-2xl">
                        <img src="images/wedding4.jpeg" alt="Wedding Detail" class="w-full h-48 md:h-60 object-cover">
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -top-6 -left-6 w-24 h-24 border border-bronze/30 rounded-full"></div>
                </div>
                <!-- Right: Text -->
                <div class="lg:pl-8 animate-on-scroll slide-right">
                    <p class="font-script text-bronze text-2xl mb-3">Sentuhan Cantik Untukmu</p>
                    <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-warmBrown leading-[1.05] tracking-tight mb-6">
                        Makeup Artist Profesional<br>Brilliant Beauty
                    </h2>
                    <div class="w-12 h-px bg-bronze mb-6"></div>
                    <p class="font-body text-mutedBrown text-base md:text-lg font-light leading-relaxed mb-6">
                       Kami hadir untuk mempercantik setiap momen spesialmu dengan sentuhan makeup yang elegan, flawless, dan sesuai dengan karakter wajahmu.Mulai dari acara wisuda, lamaran, prewedding hingga pernikahan, kami percaya setiap wanita berhak tampil percaya diri dan memukau di hari pentingnya.
                    </p>
                    <p class="font-body text-mutedBrown text-base md:text-lg font-light leading-relaxed mb-8">
                       Dengan gaya rias yang soft, glam, hingga bold, kami siap mewujudkan tampilan impianmu agar terlihat anggun, berkelas, dan tak terlupakan.
                    </p>
                    
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== PORTFOLIO / GALLERY SECTION ==================== -->
    <section id="portfolio" class="py-24 md:py-32 bg-softPink">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <!-- Section Header -->
            <div class="text-center mb-16 animate-on-scroll fade-up">
                <p class="font-script text-bronze text-2xl mb-3">Our Beauty Works</p>
                <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-warmBrown tracking-tight mb-4">
                    Brilliant Beauty Portfolio
                </h2>
                <div class="ornament-divider mb-6">
                    <span class="font-script text-bronze text-lg">✦</span>
                </div>
                <p class="font-body text-mutedBrown font-light max-w-xl mx-auto">
                  Setiap wajah adalah kanvas,setiap sentuhan adalah seni.Kumpulan karya makeup yang menghadirkan keanggunan, kelembutan, dan pesona di setiap momen spesial—dari yang sederhana hingga yang paling berkesan.
                </p>
            </div>

            <!-- Gallery Grid - Masonry Style -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 stagger-children">
                <!-- Item 1 - Tall -->
                <div class="gallery-item relative overflow-hidden vintage-frame animate-on-scroll scale-in row-span-2">
                    <img src="images/wedding5.jpeg" alt="Couple at Sunset" class="w-full h-full min-h-[400px] md:min-h-[600px] object-cover parallax-img">
                    <div class="overlay absolute inset-0 bg-deepBrown/40 flex items-end p-6">
                        <div>
                            <p class="font-script text-cream text-xl">Wedding</p>
                            <p class="text-cream/70 text-xs tracking-widest uppercase font-light">Solo Putri</p>
                        </div>
                    </div>
                </div>
                <!-- Item 2 -->
                <div class="gallery-item relative overflow-hidden vintage-frame animate-on-scroll scale-in">
                    <img src="images/G5.jpeg" alt="Bouquet" class="w-full h-[280px] md:h-[290px] object-cover parallax-img">
                    <div class="overlay absolute inset-0 bg-deepBrown/40 flex items-end p-6">
                        <div>
                            <p class="font-script text-cream text-xl">Graduation</p>
                            <p class="text-cream/70 text-xs tracking-widest uppercase font-light">Natural Look</p>
                        </div>
                    </div>
                </div>
                <!-- Item 3 -->
                <div class="gallery-item relative overflow-hidden vintage-frame animate-on-scroll scale-in">
                    <img src="images/R2.jpeg" alt="Bride Getting Ready" class="w-full h-[280px] md:h-[290px] object-cover parallax-img">
                    <div class="overlay absolute inset-0 bg-deepBrown/40 flex items-end p-6">
                        <div>
                            <p class="font-script text-cream text-xl">Request</p>
                            <p class="text-cream/70 text-xs tracking-widest uppercase font-light">Natural Look</p>
                        </div>
                    </div>
                </div>
                <!-- Item 4 -->
                <div class="gallery-item relative overflow-hidden vintage-frame animate-on-scroll scale-in">
                    <img src="images/E1.jpeg" alt="Ring Exchange" class="w-full h-[280px] md:h-[290px] object-cover parallax-img">
                    <div class="overlay absolute inset-0 bg-deepBrown/40 flex items-end p-6">
                        <div>
                            <p class="font-script text-cream text-xl">Engagement</p>
                            <p class="text-cream/70 text-xs tracking-widest uppercase font-light">special Moment</p>
                        </div>
                    </div>
                </div>
                <!-- Item 5 -->
                <div class="gallery-item relative overflow-hidden vintage-frame animate-on-scroll scale-in">
                    <img src="images/G1.jpeg" alt="First Dance" class="w-full h-[280px] md:h-[290px] object-cover parallax-img">
                    <div class="overlay absolute inset-0 bg-deepBrown/40 flex items-end p-6">
                        <div>
                            <p class="font-script text-cream text-xl">Graduation</p>
                            <p class="text-cream/70 text-xs tracking-widest uppercase font-light">Barbie Look</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12 animate-on-scroll fade-up">
                <a href="portofolio.php" class="btn-vintage inline-block border border-warmBrown/30 text-warmBrown px-10 py-4 text-xs font-body tracking-[0.25em] uppercase hover:bg-warmBrown hover:text-cream transition-all duration-400">
                    View Full Gallery
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== LET'S CAPTURE SECTION ==================== -->
    <section class="relative py-32 md:py-40 overflow-hidden">
        <!-- Background -->
        <div class="absolute inset-0">
            <img src="images/P1.jfif" alt="Aisle Walk" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-deepBrown/60"></div>
        </div>
        <!-- Content -->
        <div class="relative z-10 max-w-4xl mx-auto px-6 md:px-12 text-center">
            <p class="font-script text-rose/80 text-2xl md:text-3xl mb-4 animate-on-scroll fade-up"> Get Your Perfect Look</p>
            <h2 class="font-display text-5xl md:text-7xl lg:text-8xl font-bold text-cream leading-[0.95] tracking-tight mb-6 animate-on-scroll fade-up" style="animation-delay: 0.2s;">
               Brillaint<br>Beauty
            </h2>
            <div class="w-16 h-px bg-bronze mx-auto mb-8 animate-on-scroll fade-up" style="animation-delay: 0.4s;"></div>
            <p class="font-body text-cream/70 text-base md:text-lg font-light max-w-2xl mx-auto mb-10 animate-on-scroll fade-up" style="animation-delay: 0.5s;">
                Setiap momen berharga pantas dirayakan dengan tampilan terbaik.Biarkan kami menghadirkan riasan yang memancarkan pesonamu.
            </p>
            <a href="booking.php" class="btn-vintage inline-block bg-cream text-warmBrown px-10 py-4 text-xs font-body tracking-[0.25em] uppercase hover:bg-bronze hover:text-cream transition-all duration-400 animate-on-scroll fade-up" style="animation-delay: 0.7s;">
                Book Your Glow
            </a>
        </div>
    </section>

    <!-- ==================== SERVICES SECTION ==================== -->
   <section id="services" class="py-24 md:py-32 bg-cream">
    <div class="max-w-7xl mx-auto px-6 md:px-12">

        <!-- Section Header -->
        <div class="text-center mb-16 animate-on-scroll fade-up">
            <p class="font-script text-bronze text-2xl mb-3">Bridal & Beauty Services</p>

            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-warmBrown tracking-tight mb-4">
                My Makeup Services
            </h2>

            <div class="ornament-divider mb-6">
                <span class="font-script text-bronze text-lg">✦</span>
            </div>

            <p class="font-body text-mutedBrown font-light max-w-xl mx-auto">
                Riasan profesional untuk membuatmu tampil flawless, elegan, dan percaya diri di setiap momen spesial.
            </p>
        </div>

        <!-- Service Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-children">

            <!-- Card 1 -->
            <div class="group bg-lightTan/60 border border-bronze/10 p-8 md:p-10 text-center hover:bg-lightTan transition-all duration-500 animate-on-scroll fade-up">

                <div class="w-16 h-16 mx-auto mb-6 border border-bronze/30 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                    </svg>
                </div>

                <h3 class="font-display text-2xl font-semibold text-warmBrown mb-3">
                    Wedding Makeup
                </h3>

                <p class="font-body text-mutedBrown font-light text-sm leading-relaxed mb-6">
                    Riasan pengantin elegan dan tahan lama yang membuatmu tampil anggun, glowing, dan sempurna di hari pernikahan.
                </p>

                <p class="font-display text-bronze text-lg">From Rp 1.500.000</p>
            </div>

            <!-- Card 2 -->
            <div class="group bg-warmBrown p-8 md:p-10 text-center hover:bg-deepBrown transition-all duration-500 animate-on-scroll fade-up"
                style="box-shadow: 0 20px 60px rgba(61,48,41,0.2);">

                <div class="w-16 h-16 mx-auto mb-6 border border-bronze/40 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </div>

                <h3 class="font-display text-2xl font-semibold text-cream mb-3">
                    Engagement / Prewedding
                </h3>

                <p class="font-body text-cream/60 font-light text-sm leading-relaxed mb-6">
                    Makeup soft glam natural untuk sesi foto prewedding atau lamaran agar terlihat romantis dan timeless.
                </p>

                <p class="font-display text-bronze text-lg">From Rp 500.000</p>

                <span class="inline-block mt-4 text-[10px] tracking-[0.2em] uppercase text-bronze border border-bronze/30 px-3 py-1">
                    Popular
                </span>
            </div>

            <!-- Card 3 -->
            <div class="group bg-lightTan/60 border border-bronze/10 p-8 md:p-10 text-center hover:bg-lightTan transition-all duration-500 animate-on-scroll fade-up">

                <div class="w-16 h-16 mx-auto mb-6 border border-bronze/30 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-bronze" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                    </svg>
                </div>

                <h3 class="font-display text-2xl font-semibold text-warmBrown mb-3">
                    Graduation / Party Makeup
                </h3>

                <p class="font-body text-mutedBrown font-light text-sm leading-relaxed mb-6">
                    Makeup fresh, flawless, dan glowing untuk wisuda, ulang tahun, atau acara spesial agar tampil lebih percaya diri.
                </p>

                <p class="font-display text-bronze text-lg">From Rp 300.000</p>
            </div>

        </div>
    </div>
</section>

    <!-- ==================== LOVE STORIES / BLOG SECTION ==================== -->
    <section id="stories" class="py-24 md:py-32 bg-lightTan/40">
    <div class="max-w-7xl mx-auto px-6 md:px-12">

        <!-- Section Header -->
        <div class="text-center mb-16 animate-on-scroll fade-up">
            <p class="font-script text-bronze text-2xl mb-3">Beauty Journal</p>

            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-warmBrown tracking-tight mb-4">
                Makeup Tips,<br>Glow Guide & More
            </h2>

            <div class="ornament-divider mb-6">
                <span class="font-script text-bronze text-lg">✦</span>
            </div>
        </div>

        <!-- Blog Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stagger-children">

            <!-- Tips 1 -->
            <article class="group animate-on-scroll fade-up">
                <div class="overflow-hidden vintage-frame mb-5">
                    <img src="images/img1.jpeg" class="w-full h-80 object-cover parallax-img">
                </div>

                <p class="text-bronze text-xs tracking-[0.2em] uppercase font-light mb-2">Skincare Prep</p>

                <h3 class="font-display text-xl md:text-2xl text-warmBrown group-hover:text-bronze transition-colors mb-2 leading-snug">
                    Rahasia Makeup Flawless Tahan Lama
                </h3>

                <p class="font-body text-mutedBrown font-light text-sm leading-relaxed">
                    Kunci makeup glowing dimulai dari skincare yang tepat sebelum makeup.
                </p>
            </article>

            <!-- Tips 2 -->
            <article class="group animate-on-scroll fade-up">
                <div class="overflow-hidden vintage-frame mb-5">
                    <img src="images/img2.jpeg" class="w-full h-80 object-cover parallax-img">
                </div>

                <p class="text-bronze text-xs tracking-[0.2em] uppercase font-light mb-2">Makeup Look</p>

                <h3 class="font-display text-xl md:text-2xl text-warmBrown group-hover:text-bronze transition-colors mb-2 leading-snug">
                    Soft Glam vs Bold Glam, Pilih Mana?
                </h3>

                <p class="font-body text-mutedBrown font-light text-sm leading-relaxed">
                    Kenali perbedaan look makeup untuk acara formal dan santai.
                </p>
            </article>

            <!-- Tips 3 -->
            <article class="group animate-on-scroll fade-up">
                <div class="overflow-hidden vintage-frame mb-5">
                    <img src="images/img3.jpeg" class="w-full h-80 object-cover parallax-img">
                </div>

                <p class="text-bronze text-xs tracking-[0.2em] uppercase font-light mb-2">Client Look</p>

                <h3 class="font-display text-xl md:text-2xl text-warmBrown group-hover:text-bronze transition-colors mb-2 leading-snug">
                    Before & After Makeover Client
                </h3>

                <p class="font-body text-mutedBrown font-light text-sm leading-relaxed">
                    Transformasi makeup yang natural tapi tetap bikin pangling.
                </p>
            </article>

        </div>

        <!-- Button -->
        <div class="text-center mt-12 animate-on-scroll fade-up">
            <a href="https://www.zalora.co.id/blog/kecantikan/beauty-tutorial/rahasia-dan-tips-make-up-flawless-bebas-cakey/" 
               class="btn-vintage inline-block border border-warmBrown/30 text-warmBrown px-10 py-4 text-xs font-body tracking-[0.25em] uppercase hover:bg-warmBrown hover:text-cream transition-all duration-400">
                Lihat Semua Tips
            </a>
        </div>

    </div>
</section>

    <section class="py-24 md:py-32 bg-warmBrown">
    <div class="max-w-4xl mx-auto px-6 md:px-12 text-center">
        
        <div class="ornament-divider mb-10 animate-on-scroll fade-up">
            <span class="font-script text-bronze text-xl">"</span>
        </div>

        <blockquote class="font-display text-2xl md:text-3xl lg:text-4xl text-cream/90 font-normal italic leading-relaxed mb-8 animate-on-scroll fade-up">
            Setiap sentuhan makeup yang tepat mampu menghadirkan versi terbaik dari dirimu. 
            Kami percaya kecantikan bukan hanya terlihat, tetapi juga dirasakan dari rasa percaya diri yang terpancar.
        </blockquote>

        <div class="animate-on-scroll fade-up">
            <p class="font-body text-cream text-sm tracking-widest uppercase">
                Bridal & Client Experience
            </p>
            <p class="font-script text-bronze text-lg mt-1">
                Soft Glam • Elegant Look • Natural Beauty
            </p>
        </div>

        <!-- Dots Navigation -->
        <div class="flex items-center justify-center gap-3 mt-10">
            <button class="w-2 h-2 rounded-full bg-bronze"></button>
            <button class="w-2 h-2 rounded-full bg-cream/30 hover:bg-cream/50 transition-all duration-300"></button>
            <button class="w-2 h-2 rounded-full bg-cream/30 hover:bg-cream/50 transition-all duration-300"></button>
        </div>

    </div>
</section>

    <!-- ==================== FEATURED COUPLE SECTION ==================== -->
    <!-- ==================== AVAILABILITY / BOOKING SECTION ==================== -->
<section id="jadwal" class="py-24 md:py-32 bg-cream">
    <div class="max-w-7xl mx-auto px-6 md:px-12">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            <!-- Left: Text -->
            <div class="order-2 lg:order-1 animate-on-scroll slide-left">
                
                <p class="font-script text-bronze text-2xl mb-3">
                    Your Special Day
                </p>

                <h2 class="font-display text-4xl md:text-5xl font-bold text-warmBrown leading-[1.05] tracking-tight mb-6">
                    Check My<br>Availability
                </h2>

                <div class="w-12 h-px bg-bronze mb-6"></div>

                <p class="font-body text-mutedBrown text-base md:text-lg font-light leading-relaxed mb-6">
                    Setiap tanggal memiliki cerita tersendiri. Pastikan jadwalmu tersedia lebih awal agar hari spesialmu bisa dipersiapkan dengan sempurna.
                </p>

                <p class="font-body text-mutedBrown text-base md:text-lg font-light leading-relaxed mb-8">
                    Klik untuk melihat ketersediaan tanggal dan pilih paket makeup sesuai kebutuhanmu — simple, cepat, dan tanpa ribet.
                </p>

                <a href="jadwal.php" class="btn-vintage inline-block border border-warmBrown/30 text-warmBrown px-8 py-3.5 text-xs font-body tracking-[0.25em] uppercase hover:bg-warmBrown hover:text-cream transition-all duration-400">
                    Lihat Jadwal
                </a>

            </div>

                     <!-- Kalender Icon -->
<div class="vintage-frame overflow-hidden bg-lightTan p-10 text-center flex flex-col items-center justify-center">

    <!-- Icon Kalender -->
    <svg class="w-20 h-20 text-bronze mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M8 7V3m8 4V3m-9 8h10m-12 10h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>

    <p class="font-script text-bronze text-2xl mb-2">
        Kalender Booking
    </p>

    <h3 class="font-display text-3xl text-warmBrown mb-4">
        Cek Jadwalmu
    </h3>

    <p class="font-body text-mutedBrown text-sm">
        Klik “Lihat Jadwal” untuk melihat ketersediaan tanggal makeup
    </p>

</div>

        </div>

    </div>
</section>
    <!-- ==================== CONTACT / CTA SECTION ==================== -->
    <section id="contact" class="py-24 md:py-32 bg-softPink relative overflow-hidden">
        <!-- Decorative circles -->
        <div class="absolute top-10 right-10 w-64 h-64 border border-bronze/10 rounded-full"></div>
        <div class="absolute bottom-10 left-10 w-40 h-40 border border-bronze/10 rounded-full"></div>

        <div class="max-w-3xl mx-auto px-6 md:px-12 text-center relative z-10">
            <p class="font-script text-bronze text-2xl md:text-3xl mb-4 animate-on-scroll fade-up">Ready to Tell Your Ratting?</p>
            <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-warmBrown tracking-tight mb-6 animate-on-scroll fade-up">
                Let's Make<br>Beauty Together
            </h2>
            <div class="ornament-divider mb-8 animate-on-scroll fade-up">
                <span class="font-script text-bronze text-lg">✦</span>
            </div>
            <p class="font-body text-mutedBrown font-light max-w-xl mx-auto mb-10 animate-on-scroll fade-up">
                I'd love to hear about your special day and how I can help bring out your best look. Leave your ratting below and share your experience with my makeup services. Your feedback means so much and helps me grow every day.
            </p>

<!-- Contact Form -->
<form class="text-left space-y-5 animate-on-scroll fade-up" onsubmit="handleSubmit(event)">

    <!-- Name + Email sejajar -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <div>
            <label class="block text-xs tracking-[0.15em] uppercase text-mutedBrown font-light mb-2">
                Your Name
            </label>
            <input type="text"
                class="w-full bg-cream border border-bronze/20 px-4 py-3 text-warmBrown font-light text-sm focus:outline-none focus:border-bronze transition-colors"
                placeholder="Brilliant Beauty">
        </div>

        <div>
            <label class="block text-xs tracking-[0.15em] uppercase text-mutedBrown font-light mb-2">
                Email
            </label>
            <input type="email"
                class="w-full bg-cream border border-bronze/20 px-4 py-3 text-warmBrown font-light text-sm focus:outline-none focus:border-bronze transition-colors"
                placeholder="BeautyBrilliant_@gmail.com">
        </div>

    </div>

    <!-- Textarea full -->
    <div>
        <label class="block text-xs tracking-[0.15em] uppercase text-mutedBrown font-light mb-2">
            Tell Me About Your Ratting
        </label>
        <textarea rows="4"
            class="w-full bg-cream border border-bronze/20 px-4 py-3 text-warmBrown font-light text-sm focus:outline-none focus:border-bronze transition-colors resize-none"
            placeholder="Share your ratting details..."></textarea>
    </div>

    <!-- Button -->
    <form action="kirim_rating.php" method="POST">

    <!-- input lain kalau ada -->
    
    <div class="text-center pt-2">
        <button type="submit"
            class="btn-vintage inline-block bg-warmBrown text-cream px-12 py-4 text-xs font-body tracking-[0.25em] uppercase hover:bg-bronze transition-all duration-400">
            Send Rating
        </button>
    </div>

</form>

</form>
                <!-- Toast message -->
                <div id="formToast" class="hidden text-center mt-4">
                    <p class="text-bronze text-sm font-light">✦ Thank you! I'll be in touch soon. ✦</p>
                </div>
            </form>
        </div>
    </section>

    <!-- ==================== INSTAGRAM FEED STRIP ==================== -->
    <section class="py-16 bg-cream">
    <div class="text-center mb-10">
        <p class="font-script text-bronze text-xl mb-1">Brilliant Beauty</p>
        <a href="#" class="font-body text-warmBrown text-xs tracking-[0.2em] uppercase hover:text-bronze transition-colors">
            Get Your Perfect Look
        </a>
    </div>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-1">
            <div class="overflow-hidden aspect-square">
                <img src="images/b1.jpeg" alt="Instagram" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden aspect-square">
                <img src="images/b2.jfif" alt="Instagram" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden aspect-square">
                <img src="images/b3.jpeg" alt="Instagram" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden aspect-square hidden md:block">
                <img src="images/b4.jpeg" alt="Instagram" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden aspect-square hidden md:block">
                <img src="images/b5.jpeg" alt="Instagram" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
            </div>
            <div class="overflow-hidden aspect-square hidden md:block">
                <img src="images/b6.jpeg" alt="Instagram" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
            </div>
        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-deepBrown pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <!-- Col 1: Brand -->
                <div>
                    <a href="#" class="font-script text-3xl text-cream">Brilliant Beauty</a>
                    <p class="font-body text-cream/50 font-light text-sm leading-relaxed mt-4">
                       Briliante Beauty menyediakan layanan makeup profesional dengan hasil natural dan elegan, siap menemani setiap momen spesialmu.
 
                    </p>
                    <!-- Social Icons -->
                    <div class="flex items-center gap-4 mt-6">
                        <a href="https://www.instagram.com/nymas_makeup?igsh=MXkxajlsZ3dkdXEwdg==" target="_blank" class="w-9 h-9 border border-cream/20 rounded-full flex items-center justify-center hover:border-bronze hover:bg-bronze/20 transition-all duration-300">
                            <svg class="w-4 h-4 text-cream/60" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 border border-cream/20 rounded-full flex items-center justify-center hover:border-bronze hover:bg-bronze/20 transition-all duration-300">
                            <svg class="w-4 h-4 text-cream/60" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 border border-cream/20 rounded-full flex items-center justify-center hover:border-bronze hover:bg-bronze/20 transition-all duration-300">
                            <svg class="w-4 h-4 text-cream/60" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 border border-cream/20 rounded-full flex items-center justify-center hover:border-bronze hover:bg-bronze/20 transition-all duration-300">
                            <svg class="w-4 h-4 text-cream/60" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                        </a>
                    </div>
                </div>
                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="font-display text-cream text-lg mb-5">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="font-body text-cream/50 text-sm font-light hover:text-bronze transition-colors duration-300">About Me</a></li>
                        <li><a href="#portfolio" class="font-body text-cream/50 text-sm font-light hover:text-bronze transition-colors duration-300">Portfolio</a></li>
                        <li><a href="#services" class="font-body text-cream/50 text-sm font-light hover:text-bronze transition-colors duration-300">Makeup Services</a></li>
                        <li><a href="#stories" class="font-body text-cream/50 text-sm font-light hover:text-bronze transition-colors duration-300">Makeup Tips,Glow Guide & More</a></li>
                        <li><a href="#contact" class="font-body text-cream/50 text-sm font-light hover:text-bronze transition-colors duration-300">Ratting</a></li>
                    </ul>
                </div>
                <!-- Col 3: Contact Info -->
                <div id="get">
                    <h4 class="font-display text-cream text-lg mb-5">Get In Touch</h4>
                    <ul class="space-y-3">
                        <li class="font-body text-cream/50 text-sm font-light">BeautyBrilliant_@gmail.com</li>
                        <li class="font-body text-cream/50 text-sm font-light">+62-838-7727-0927</li>
                        <li class="font-body text-cream/50 text-sm font-light">Jawa Tengah, Indonesia</li>
                        <li class="font-body text-cream/50 text-sm font-light mt-4">Available for travel nationwide</li>
                    </ul>
                </div>
            </div>
            <!-- Bottom Bar -->
            <div class="border-t border-cream/10 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="font-body text-cream/30 text-xs font-light">
                    © 2026 Briliant Beauty. All Rights Reserved
                </p>
                <div class="flex items-center gap-6">
                    <a href="#" class="font-body text-cream/30 tet-cream/60 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // ===== Navbar Scroll Effect =====
        const navbar = document.getElementById('navbar');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 80) {
                navbar.style.background = 'rgba(250, 247, 242, 0.95)';
                navbar.style.backdropFilter = 'blur(12px)';
                navbar.style.boxShadow = '0 1px 20px rgba(61,48,41,0.08)';
            } else {
                navbar.style.background = 'transparent';
                navbar.style.backdropFilter = 'none';
                navbar.style.boxShadow = 'none';
            }

            lastScroll = currentScroll;
        });

        // ===== Mobile Menu =====
        const menuToggle = document.getElementById('menuToggle');
        const menuClose = document.getElementById('menuClose');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        function closeMenu() {
            mobileMenu.classList.remove('open');
            document.body.style.overflow = '';
        }

        menuClose.addEventListener('click', closeMenu);
        mobileLinks.forEach(link => link.addEventListener('click', closeMenu));

        // ===== Scroll Animations =====
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // ===== Testimonial Dots (visual only) =====
        const dots = document.querySelectorAll('[data-dot]');
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                dots.forEach(d => {
                    d.classList.remove('bg-bronze');
                    d.classList.add('bg-cream/30');
                });
                dot.classList.remove('bg-cream/30');
                dot.classList.add('bg-bronze');
            });
        });

        // ===== Form Submit Handler =====
        function handleSubmit(e) {
            e.preventDefault();
            const toast = document.getElementById('formToast');
            toast.classList.remove('hidden');
            toast.style.animation = 'fadeInUp 0.5s ease forwards';

            // Reset form
            e.target.reset();

            // Hide toast after 4 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.style.opacity = '';
                    toast.style.transition = '';
                }, 500);
            }, 4000);
        }

        // ===== Smooth scroll for anchor links =====
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offset = 80;
                    const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    window.scrollTo({ top, behavior: 'smooth' });
                }
            });
        });

        // ===== Parallax on Hero (subtle) =====
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroImg = document.querySelector('.parallax-img');
            if (heroImg && scrolled < window.innerHeight) {
                heroImg.style.transform = `translateY(${scrolled * 0.15}px) scale(1)`;
            }
        });
        function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("overlay").classList.toggle("show");
}

function closeSidebar(){
    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("show");
}
    </script>

</body>
</html>