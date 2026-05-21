<?php
session_start();
include "../koneksi.php";

/* ===============================
CEK LOGIN ADMIN
=============================== */


/* ===============================
UPLOAD FOTO PORTOFOLIO
Data masuk ke folder:
../customer/images/

Supaya customer portofolio.php otomatis ambil file baru
=============================== */

if (isset($_POST['upload'])) {

    $kategori = $_POST['kategori']; // wedding / graduation / request

    $namaFile   = $_FILES['foto']['name'];
    $tmpFile    = $_FILES['foto']['tmp_name'];
    $size       = $_FILES['foto']['size'];

    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if(in_array($ext,$allowed)){

        $newName = time().'_'.rand(100,999).'.'.$ext;

        $tujuan = "../customer/images/".$newName;

        move_uploaded_file($tmpFile,$tujuan);

        mysqli_query($koneksi,"
            INSERT INTO portofolio
            (foto,kategori)
            VALUES
            ('$newName','$kategori')
        ");

        echo "<script>alert('Foto berhasil upload');location='portofolio.php';</script>";
        exit;
    }
}

/* ===============================
HAPUS FOTO
=============================== */
if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $q = mysqli_query($koneksi,"SELECT * FROM portofolio WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);

    if($d){
        unlink("../customer/images/".$d['foto']);
        mysqli_query($koneksi,"DELETE FROM portofolio WHERE id='$id'");
    }

    echo "<script>location='portofolio.php';</script>";
    exit;
}

$data = mysqli_query($koneksi,"SELECT * FROM portofolio ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Portofolio</title>

<style>
body{
    font-family:Arial;
    background:#f5f5f5;
    margin:0;
}

.container{
    width:95%;
    max-width:1100px;
    margin:auto;
    padding:30px;
}

.card{
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    margin-bottom:25px;
}

h2{
    margin-top:0;
}

input,select{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    background:goldenrod;
    color:#fff;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
}

.gallery{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.item{
    background:#fff;
    padding:12px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,.07);
}

.item img{
    width:100%;
    height:240px;
    object-fit:cover;
    border-radius:10px;
}

.tag{
    margin-top:10px;
    font-size:13px;
    color:#777;
}

.hapus{
    display:inline-block;
    margin-top:10px;
    background:red;
    color:#fff;
    padding:8px 12px;
    text-decoration:none;
    border-radius:7px;
}

.btn-kembali{
    display:inline-block;
    background:#333;
    color:#fff;
    padding:12px 20px;
    border-radius:8px;
    text-decoration:none;
    cursor:pointer;
    margin-left:10px;
    transition:0.3s;
}

.btn-kembali:hover{
    background:#555;
}
</style>
</head>
<body>

<div class="container">


<div class="card">
<h2>Upload Foto Portofolio</h2>

<form method="POST" enctype="multipart/form-data">

<select name="kategori" required>
<option value="">Pilih Kategori</option>
<option value="wedding">Wedding</option>
<option value="graduation">Graduation</option>
<option value="request">Request</option>
</select>

<input type="file" name="foto" required>

<button type="submit" name="upload">Upload Foto</button>
<a href="index.php" class="btn-kembali">Kembali</a>

</form>
</div>

<div class="gallery">

<?php while($row=mysqli_fetch_assoc($data)){ ?>

<div class="item">
<img src="../customer/images/<?= $row['foto']; ?>">
<div class="tag"><?= ucfirst($row['kategori']); ?></div>
<a href="?hapus=<?= $row['id']; ?>" class="hapus"
onclick="return confirm('Hapus foto ini?')">Hapus</a>
</div>

<?php } ?>

</div>

</div>

</body>
</html>