<?php
include 'koneksi.php';

if (isset($_POST['daftar'])) {
    $nama   = $_POST['nama'];
    $email  = $_POST['email'];
    $pass   = $_POST['password'];

    mysqli_query($koneksi, "INSERT INTO users 
    (nama,email,password,role)
    VALUES ('$nama','$email','$pass','customer')");

    echo "<script>alert('Berhasil daftar!'); window.location='login.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register - Brilliant Beauty</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

/* BACKGROUND */
body{
    height:100vh;
    background:linear-gradient(135deg,#f8f5f2,#efe7df,#e4d6c7);
    overflow:hidden;
}

/* SOFT BLOB */
body::before,
body::after{
    content:'';
    position:absolute;
    width:320px;
    height:320px;
    border-radius:50%;
    filter:blur(80px);
    opacity:.45;
    animation:float 8s ease-in-out infinite;
}

body::before{
    background:#d8c2aa;
    top:-100px;
    left:-80px;
}

body::after{
    background:#b89e7e;
    bottom:-120px;
    right:-80px;
    animation-delay:2s;
}

@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-30px);}
    100%{transform:translateY(0);}
}

/* LAYOUT */
.container{
    display:flex;
    height:100vh;
    position:relative;
    z-index:2;
}

/* LEFT SIDE */
.left{
    width:50%;
    background:
    linear-gradient(rgba(40,28,18,.45),rgba(40,28,18,.45)),
    url('login.jpeg') center/cover;

    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
    text-align:center;
    padding:40px;
    animation:zoomBg 10s infinite alternate;
}

@keyframes zoomBg{
    from{background-size:100%;}
    to{background-size:110%;}
}

.left h1{
    font-family:'Playfair Display',serif;
    font-size:42px;
    line-height:1.3;
    letter-spacing:1px;
    animation:fadeUp 1.2s ease;
}

/* RIGHT */
.right{
    width:50%;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.card{
    width:360px;
    padding:42px;
    border-radius:24px;
    background:rgba(255,255,255,.75);
    backdrop-filter:blur(16px);
    box-shadow:0 25px 50px rgba(60,40,20,.15);
    border:1px solid rgba(255,255,255,.4);
    animation:fadeUp 1s ease;
}

/* TITLE */
.card h2{
    font-family:'Playfair Display',serif;
    color:#3f3025;
    font-size:34px;
    margin-bottom:8px;
}

.subtitle{
    font-size:13px;
    color:#8a7460;
    margin-bottom:24px;
}

/* INPUT */
.input-group{
    margin-bottom:16px;
}

input{
    width:100%;
    padding:14px 16px;
    border-radius:14px;
    border:1px solid #ddd2c5;
    outline:none;
    background:#fff;
    transition:.3s;
    font-size:14px;
}

input:focus{
    border-color:#9c7d5a;
    box-shadow:0 0 0 4px rgba(156,125,90,.12);
    transform:translateY(-2px);
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#7d6144,#5e4832);
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    position:relative;
    overflow:hidden;
    transition:.3s;
    margin-top:8px;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 25px rgba(80,55,30,.25);
}

button::before{
    content:'';
    position:absolute;
    top:0;
    left:-100%;
    width:100%;
    height:100%;
    background:linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.45),
        transparent
    );
}

button:hover::before{
    animation:shine .8s;
}

@keyframes shine{
    from{left:-100%;}
    to{left:100%;}
}

/* LINK */
.link{
    text-align:center;
    margin-top:20px;
    font-size:14px;
    color:#7c6652;
}

.link a{
    color:#5e4832;
    text-decoration:none;
    font-weight:600;
}

.link a:hover{
    text-decoration:underline;
}

/* ANIMATION */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(30px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* MOBILE */
@media(max-width:768px){
    .left{display:none;}
    .right{
        width:100%;
        padding:20px;
    }

    .card{
        width:100%;
        max-width:400px;
    }
}
</style>
</head>

<body>

<div class="container">

    <div class="left">
        <h1>Join<br>Brilliant Beauty</h1>
    </div>

    <div class="right">
        <div class="card">

            <h2>Register</h2>
            <div class="subtitle">Create your luxury beauty account</div>

            <form method="POST">

                <div class="input-group">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>

                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button name="daftar">Create Account</button>

            </form>

            <div class="link">
                Sudah punya akun? <a href="login.php">Login</a>
            </div>

        </div>
    </div>

</div>

<!-- Ripple -->
<script>
document.querySelector("button").addEventListener("click", function(e){

    let ripple = document.createElement("span");

    ripple.style.position = "absolute";
    ripple.style.width = "120px";
    ripple.style.height = "120px";
    ripple.style.borderRadius = "50%";
    ripple.style.background = "rgba(255,255,255,.45)";
    ripple.style.left = (e.offsetX - 60) + "px";
    ripple.style.top = (e.offsetY - 60) + "px";
    ripple.style.transform = "scale(0)";
    ripple.style.animation = "ripple .6s linear";

    this.appendChild(ripple);

    setTimeout(()=>{
        ripple.remove();
    },600);

});
</script>

<style>
@keyframes ripple{
    to{
        transform:scale(2.8);
        opacity:0;
    }
}
</style>

</body>
</html>