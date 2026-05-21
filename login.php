<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    $data  = mysqli_fetch_assoc($query);

    if ($data && $pass == $data['password']) {

        $_SESSION['id']   = $data['id_user'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: customer/index.php");
        }
        exit;

    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - Brilliant Beauty</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

:root{
    --cream:#f8f4ef;
    --soft:#eee4d8;
    --brown:#6f4e37;
    --brown-dark:#3e2b1f;
    --brown-light:#b89b84;
    --gold:#c8a97e;
}

/* BACKGROUND */
body{
    height:100vh;
    background:radial-gradient(circle at top,#f8f4ef,#eee4d8,#d9c3ad);
    overflow:hidden;
    position:relative;
}

/* FLOATING BLOB */
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
    background:#c8a97e;
    top:-100px;
    left:-90px;
}

body::after{
    background:#8b6b52;
    bottom:-120px;
    right:-90px;
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

/* LEFT */
.left{
    width:50%;
    background:
    linear-gradient(rgba(30,20,10,.35),rgba(30,20,10,.35)),
    url('login.jpeg') center/cover;

    display:flex;
    align-items:center;
    justify-content:center;
    animation:zoomBg 10s infinite alternate;
}

@keyframes zoomBg{
    from{background-size:100%;}
    to{background-size:110%;}
}

.left h1{
    color:#fff;
    font-size:46px;
    font-family:'Playfair Display',serif;
    letter-spacing:2px;
    text-shadow:0 8px 25px rgba(0,0,0,.25);
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

    background:rgba(255,255,255,.72);
    backdrop-filter:blur(18px);

    box-shadow:0 25px 50px rgba(62,43,31,.12);
    border:1px solid rgba(255,255,255,.5);

    animation:fadeUp 1s ease;
}

/* TITLE */
.card h2{
    font-family:'Playfair Display',serif;
    font-size:32px;
    color:var(--brown-dark);
    margin-bottom:8px;
}

.subtitle{
    font-size:13px;
    color:#8c7664;
    margin-bottom:25px;
}

/* INPUT */
.input-group{
    margin-bottom:16px;
}

input{
    width:100%;
    padding:13px 14px;
    border-radius:14px;
    border:1px solid #e2d6ca;
    outline:none;
    background:rgba(255,255,255,.8);
    transition:.3s;
    font-size:14px;
}

input:focus{
    border-color:var(--gold);
    box-shadow:0 0 0 4px rgba(200,169,126,.12);
    transform:translateY(-1px);
}

/* BUTTON */
button{
    width:100%;
    padding:13px;
    border:none;
    border-radius:14px;
    cursor:pointer;
    overflow:hidden;
    position:relative;

    background:linear-gradient(135deg,var(--brown),var(--brown-dark));
    color:#fff;
    font-weight:600;
    letter-spacing:.5px;

    transition:.3s;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 30px rgba(62,43,31,.22);
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
    margin-top:18px;
    font-size:14px;
    color:#8c7664;
}

.link a{
    color:var(--brown);
    text-decoration:none;
    font-weight:600;
}

.link a:hover{
    color:var(--brown-dark);
}

/* ERROR */
.error{
    background:#fff4f2;
    color:#b74c3b;
    font-size:13px;
    padding:10px 12px;
    border-radius:10px;
    margin-bottom:15px;
}

/* ANIMASI */
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

/* RESPONSIVE */
@media(max-width:768px){
    .left{display:none;}
    .right{width:100%;padding:20px;}
    .card{width:100%;}
}

/* RIPPLE */
@keyframes ripple{
    from{transform:scale(0);opacity:1;}
    to{transform:scale(2.8);opacity:0;}
}
</style>
</head>

<body>

<div class="container">

    <div class="left">
        <h1>Brilliant Beauty</h1>
    </div>

    <div class="right">
        <div class="card">

            <h2>Welcome Back</h2>
            <div class="subtitle">Luxury beauty experience starts here</div>

            <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

            <form method="POST">

                <div class="input-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button name="login">Login</button>

            </form>

            <div class="link">
                Belum punya akun? <a href="register.php">Daftar</a>
            </div>

        </div>
    </div>

</div>

<script>
document.querySelector("button").addEventListener("click", function(e){

    let ripple = document.createElement("span");

    ripple.style.position = "absolute";
    ripple.style.width = "100px";
    ripple.style.height = "100px";
    ripple.style.borderRadius = "50%";
    ripple.style.background = "rgba(255,255,255,.45)";
    ripple.style.left = (e.offsetX - 50) + "px";
    ripple.style.top  = (e.offsetY - 50) + "px";
    ripple.style.animation = "ripple .6s linear";

    this.appendChild(ripple);

    setTimeout(()=>ripple.remove(),600);

});
</script>

</body>
</html>