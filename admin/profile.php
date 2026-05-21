<?php
session_start();

echo $_SESSION['id'];
echo $id;
exit;
include "../koneksi.php";

/* =========================
   CEK LOGIN
========================= */
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['id'];

/* =========================
   UPDATE DATA
========================= */
if (isset($_POST['update'])) {

    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);

    $update = mysqli_query($koneksi,"
        UPDATE users 
        SET nama='$nama', email='$email'
        WHERE id_user='$id'
    ");

    if ($update) {
        echo "<script>alert('Profile berhasil diperbarui');window.location='profile.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update profile');</script>";
    }
}

/* =========================
   AMBIL DATA USER LOGIN
========================= */
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id'");
$data  = mysqli_fetch_assoc($query);

$nama   = $data['nama'];
$email  = $data['email'];
$role   = ucfirst($data['role']);
$status = "Online";
$foto   = strtoupper(substr($nama,0,1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile Saya</title>

<style>
body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:#f5f5f5;
}

.container{
    width:90%;
    max-width:850px;
    margin:40px auto;
}

.card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.header{
    background:linear-gradient(135deg,#111,#333);
    color:#fff;
    text-align:center;
    padding:35px 20px;
}

.avatar{
    width:95px;
    height:95px;
    border-radius:50%;
    background:linear-gradient(135deg,#d4af37,#f6d365);
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-size:34px;
    font-weight:bold;
    margin-bottom:15px;
}

.header h2{
    margin:0;
}

.header p{
    margin-top:5px;
    color:#ddd;
}

.body{
    padding:30px;
}

.form-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:bold;
    color:#444;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:15px;
    box-sizing:border-box;
}

input:focus{
    border-color:#d4af37;
    outline:none;
}

.role-box{
    padding:12px;
    background:#f7f7f7;
    border-radius:10px;
    color:#333;
}

.btn-group{
    margin-top:25px;
    display:flex;
    gap:12px;
}

.btn{
    flex:1;
    text-align:center;
    padding:13px;
    border:none;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
    text-decoration:none;
    font-size:15px;
}

.save{
    background:#d4af37;
    color:#fff;
}

.save:hover{
    background:#b89425;
}

.back{
    background:#eee;
    color:#333;
}

.back:hover{
    background:#ddd;
}

.logout{
    background:#ff4d4d;
    color:#fff;
}

.logout:hover{
    background:#e60000;
}
</style>
</head>
<body>

<div class="container">
<div class="card">

    <div class="header">
        <div class="avatar"><?= $foto ?></div>
        <h2><?= $nama ?></h2>
        <p><?= $role ?></p>
    </div>

    <div class="body">

        <form method="POST">

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" value="<?= $nama ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= $email ?>" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <div class="role-box"><?= $role ?></div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <div class="role-box" style="color:green;">Online</div>
            </div>

            <div class="btn-group">
                <button type="submit" name="update" class="btn save">Simpan</button>
                <a href="index.php" class="btn back">Kembali</a>
                <a href="logout.php" class="btn logout">Logout</a>
            </div>

        </form>

    </div>

</div>
</div>

</body>
</html>