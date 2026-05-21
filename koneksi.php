<?php 
     $koneksi = mysqli_connect("localhost","root","","db_mua");
     if(mysqli_connect_errno()) {
     	echo "Koneksi database gagal : ".
     	     mysqli_connect_error();
     }
?>