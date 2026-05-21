<?php
include '../koneksi.php';
?>

<style>
body{
    background:#f5f6fa;
    font-family: Arial, sans-serif;
}

.container{
    max-width:1100px;
    margin:30px auto;
}

.panel{
    background:#fff;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
    overflow:hidden;
}

.panel-heading{
    padding:18px 20px;
    background:linear-gradient(135deg,#111827,#374151);
    color:#fff;
}

.panel-body{ padding:20px; }

.table{
    width:100%;
    border-collapse:collapse;
}

.table th{
    background:#f3f4f6;
    padding:12px;
    font-size:13px;
}

.table td{
    padding:12px;
    font-size:13px;
    border-bottom:1px solid #eee;
}

.label{
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
    color:#fff;
}

.label-warning{background:#f59e0b;}
.label-info{background:#3b82f6;}
.label-success{background:#10b981;}
.label-danger{background:#ef4444;}

.status-ok{color:#10b981;font-weight:bold;}
.status-wait{color:#f59e0b;font-weight:bold;}
.status-fail{color:#ef4444;font-weight:bold;}
</style>

<div class="container">
    <div class="panel">

        <div class="panel-heading">
            <h4>Status Booking (Admin / Semua Data)</h4>
        </div>

        <div class="panel-body">

            <table class="table">
                <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Paket</th>
                    <th>Total</th>
                    <th>Status Booking</th>
                    <th>Status Bayar</th>
                </tr>

<?php
$no = 1;

$data = mysqli_query($koneksi, "
    SELECT * FROM booking 
    ORDER BY id DESC
");

while ($d = mysqli_fetch_array($data)) {
?>

<tr>
    <td><?= $no++; ?></td>
    <td><?= $d['no_invoice']; ?></td>
    <td><?= $d['nama']; ?></td>
    <td><?= $d['tanggal']; ?></td>
    <td><?= $d['paket_name']; ?></td>
    <td>Rp <?= number_format($d['total']); ?></td>

    <td>
        <?php
        if ($d['status']=="menunggu") {
            echo "<span class='label label-warning'>MENUNGGU</span>";
        } elseif ($d['status']=="dikonfirmasi") {
            echo "<span class='label label-info'>DIKONFIRMASI</span>";
        } elseif ($d['status']=="selesai") {
            echo "<span class='label label-success'>SELESAI</span>";
        } else {
            echo "<span class='label label-danger'>DIBATALKAN</span>";
        }
        ?>
    </td>

    <td>
        <?php
        $id = $d['id'];
        $trx = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE booking_id='$id'");

        $bayar = "<span class='status-wait'>BELUM</span>";

        while ($t = mysqli_fetch_array($trx)) {
            if ($t['status']=="menunggu") {
                $bayar = "<span class='status-wait'>WAIT</span>";
            } elseif ($t['status']=="dikonfirmasi") {
                $bayar = "<span class='status-ok'>OK</span>";
            } elseif ($t['status']=="ditolak") {
                $bayar = "<span class='status-fail'>FAIL</span>";
            }
        }

        echo $bayar;
        ?>
    </td>

</tr>

<?php } ?>

            </table>

        </div>
    </div>
</div>