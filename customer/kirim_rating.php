<?php
include "koneksi.php";

$nama  = $_POST['nama'];
$email = $_POST['email'];
$rating = $_POST['rating'];

$query = "INSERT INTO rating (nama, email, rating)
          VALUES ('$nama', '$email', '$rating')";

$result = mysqli_query($koneksi, $query);

if($result){
    echo "<script>
        alert('Rating berhasil dikirim!');
        window.location.href='index.php';
    </script>";
} else {
    echo "Gagal kirim rating: " . mysqli_error($koneksi);
}
?>