<?php
session_start();
include "../koneksi.php";



/* ========== SIMPAN (INSERT / UPDATE) ========== */
if (isset($_POST['simpan'])) {

    $id = $_POST['id'];
    $kategori = $_POST['kategori'];
    $judul = $_POST['judul'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $includes = $_POST['includes'];

    /* upload gambar */
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if ($gambar != "") {
        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
        $newName = time().".".$ext;
        move_uploaded_file($tmp, "../customer/images/".$newName);
    } else {
        $newName = $_POST['old_gambar'];
    }

    if ($id == "") {
        mysqli_query($koneksi, "INSERT INTO pricelist 
        (kategori,judul,harga,deskripsi,includes,gambar)
        VALUES 
        ('$kategori','$judul','$harga','$deskripsi','$includes','$newName')");
    } else {
        mysqli_query($koneksi, "UPDATE pricelist SET 
        kategori='$kategori',
        judul='$judul',
        harga='$harga',
        deskripsi='$deskripsi',
        includes='$includes',
        gambar='$newName'
        WHERE id='$id'");
    }

    echo "<script>alert('Berhasil');location='pricelist.php';</script>";
}

/* ========== HAPUS ========== */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pricelist WHERE id='$id'");
    header("Location: pricelist.php");
}

/* ========== EDIT ========== */
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($koneksi, "SELECT * FROM pricelist WHERE id='$id'");
    $edit = mysqli_fetch_assoc($q);
}

/* ========== DATA ========== */
$data = mysqli_query($koneksi, "SELECT * FROM pricelist ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Pricelist</title>

<style>
body{
    margin:0;
    font-family:Inter;
    background:#f5efe7;
}

.container{
    width:95%;
    max-width:1000px;
    margin:auto;
    padding:30px;
}

.card{
    background:#fff;
    padding:20px;
    border-radius:16px;
    margin-bottom:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

input,textarea{
    width:100%;
    padding:10px;
    margin:6px 0;
    border-radius:10px;
    border:1px solid #ccc;
}

button{
    background:#3f3025;
    color:#fff;
    padding:10px 15px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-size:14px;
    line-height:1;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#3f3025;
    color:#fff;
    padding:10px;
}

td{
    padding:10px;
    border-bottom:1px solid #eee;
}

img{
    width:80px;
    border-radius:8px;
}

.btn-kembali{
    display:inline-block;
    background:#6b5b4b; /* beda warna tapi masih estetik */
    color:#fff;
    padding:10px 15px; /* SAMA dengan button */
    border:none;
    border-radius:10px;
    text-decoration:none;
    cursor:pointer;
    margin-left:10px;
    transition:0.3s;
    font-size:14px;
    line-height:1;
}
button, .btn-kembali{
    transition: all 0.25s ease;
}

button:hover{
    transform: translateY(-1px);
    background:#2a1f17;
}

.btn-kembali:hover{
    transform: translateY(-1px);
    background:#7a6a5a;
}
</style>

</head>
<body>

<div class="container">

<div class="card">
<h2>Kelola Pricelist</h2>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<input type="hidden" name="old_gambar" value="<?= $edit['gambar'] ?? '' ?>">

<input type="text" name="kategori" placeholder="kategori" value="<?= $edit['kategori'] ?? '' ?>">
<input type="text" name="judul" placeholder="judul" value="<?= $edit['judul'] ?? '' ?>">
<input type="text" name="harga" placeholder="harga" value="<?= $edit['harga'] ?? '' ?>">

<textarea name="deskripsi" placeholder="deskripsi"><?= $edit['deskripsi'] ?? '' ?></textarea>
<textarea name="includes" placeholder="includes"><?= $edit['includes'] ?? '' ?></textarea>

<input type="file" name="gambar">

<button type="submit" name="simpan">Simpan</button>
<a href="index.php" class="btn-kembali">Kembali</a>

</form>
</div>

<div class="card">
<h3>Data Pricelist</h3>

<table>
<tr>
<th>Gambar</th>
<th>Judul</th>
<th>Kategori</th>
<th>Harga</th>
<th>Aksi</th>
</tr>

<?php while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
<td>
<?php if($row['gambar']){ ?>
<img src="../customer/images/<?= $row['gambar']; ?>">
<?php } ?>
</td>
<td><?= $row['judul'] ?></td>
<td><?= $row['kategori'] ?></td>
<td><?= $row['harga'] ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a>
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('hapus?')">Hapus</a>
</td>
</tr>
<?php } ?>

</table>

</div>

</div>

</body>
</html>