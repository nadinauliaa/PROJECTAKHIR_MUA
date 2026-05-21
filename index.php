<!DOCTYPE html>
<html>
<head>
<title>Brilliant Beauty</title>

<!-- Font elegan + modern -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    height:100vh;
    font-family:'Inter',sans-serif;
    background:radial-gradient(circle at top left,#f8f4ef,#eee4d8,#dcc8b4);
    overflow:hidden;
}

/* LOGO */
.logo-top{
    position:absolute;
    top:25px;
    right:40px;
    width:65px;
    opacity:.9;
}

/* ORNAMEN SOFT */
.blob{
    position:absolute;
    border-radius:50%;
    filter:blur(65px);
    opacity:.45;
    animation:float 8s ease-in-out infinite;
}

.blob1{
    width:260px;
    height:260px;
    background:#d8c0aa;
    top:-60px;
    left:-60px;
}

.blob2{
    width:220px;
    height:220px;
    background:#b89a7e;
    bottom:-50px;
    right:-40px;
    animation-delay:2s;
}

/* GARIS HALUS */
.line{
    position:absolute;
    width:130px;
    height:1px;
    background:#9b7b5e;
    opacity:.18;
}

.line-top{
    top:120px;
    left:50%;
    transform:translateX(-50%);
}

.line-bottom{
    bottom:120px;
    left:50%;
    transform:translateX(-50%);
}

/* CONTAINER */
.container{
    height:100vh;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
}

/* TITLE */
h1{
    font-family:'Playfair Display',serif;
    font-size:44px;
    color:#3e2f24;
    letter-spacing:1px;
    animation:fadeIn 1.5s ease;
}

/* SUBTITLE */
p{
    margin:12px 0 35px;
    font-size:14px;
    color:#7a6858;
    animation:fadeIn 2s ease;
}

/* BUTTON */
.btn{
    position:relative;
    overflow:hidden;
    padding:13px 42px;
    border:1px solid #8b6a4e;
    border-radius:32px;
    background:transparent;
    color:#6e523b;
    font-size:14px;
    font-weight:500;
    cursor:pointer;
    transition:.3s ease;
}

/* HOVER */
.btn:hover{
    background:#6e523b;
    color:#fff;
    transform:translateY(-3px);
    box-shadow:0 14px 35px rgba(62,47,36,.22);
}

/* KLIK */
.btn:active{
    transform:scale(.95);
}

/* RIPPLE */
.btn span{
    position:absolute;
    border-radius:50%;
    transform:scale(0);
    animation:ripple .6s linear;
    background:rgba(255,255,255,.55);
}

/* FOOTER */
.footer{
    position:absolute;
    bottom:25px;
    width:100%;
    text-align:center;
    font-size:12px;
    color:#8c7b6c;
}

/* LOADER */
#loader{
    position:fixed;
    width:100%;
    height:100%;
    background:linear-gradient(135deg,#f8f4ef,#eee4d8,#dcc8b4);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:9999;
    transition:opacity .8s ease, visibility .8s;
}

.loader-content{
    text-align:center;
}

.spinner{
    width:60px;
    height:60px;
    border:4px solid rgba(110,82,59,.15);
    border-top:4px solid #6e523b;
    border-radius:50%;
    margin:auto;
    animation:spin 1s linear infinite;
}

.loader-content h2{
    font-family:'Playfair Display';
    margin-top:15px;
    color:#4a3728;
}

.loader-content p{
    font-size:12px;
    color:#8c7b6c;
}

#loader.hide{
    opacity:0;
    visibility:hidden;
}

/* ANIMATION */
@keyframes spin{
    to{transform:rotate(360deg);}
}

@keyframes ripple{
    to{
        transform:scale(4);
        opacity:0;
    }
}

@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-20px);}
    100%{transform:translateY(0);}
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
</head>

<body>

<!-- LOADER -->
<div id="loader">
    <div class="loader-content">
        <div class="spinner"></div>
        <h2>Brilliant Beauty</h2>
        <p>Preparing your luxury beauty experience...</p>
    </div>
</div>

<!-- ORNAMEN -->
<div class="blob blob1"></div>
<div class="blob blob2"></div>

<!-- GARIS -->
<div class="line line-top"></div>
<div class="line line-bottom"></div>

<!-- CONTENT -->
<div class="container">
    <h1>Brilliant Beauty</h1>
    <p>Luxury Makeup Artist & Beauty Service</p>

    <a href="login.php">
        <button class="btn">Start</button>
    </a>
</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 Brilliant Beauty. All Rights Reserved
</div>

<script>
window.addEventListener("load",function(){
    setTimeout(function(){
        document.getElementById("loader").classList.add("hide");
    },2000);
});

document.querySelector(".btn").addEventListener("click",function(e){

    const circle=document.createElement("span");
    const diameter=Math.max(this.clientWidth,this.clientHeight);

    circle.style.width=circle.style.height=diameter+"px";
    circle.style.left=e.offsetX-diameter/2+"px";
    circle.style.top=e.offsetY-diameter/2+"px";

    this.appendChild(circle);

    setTimeout(()=>circle.remove(),600);
});

document.querySelector(".btn").addEventListener("click",function(e){
    e.preventDefault();

    document.body.style.transition=".6s";
    document.body.style.opacity="0";

    setTimeout(()=>{
        window.location.href="login.php";
    },600);
});
</script>

</body>
</html>