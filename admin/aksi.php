<?php
require_once 'config.php';

 $action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {

    // ==========================================
    // AJAX: Ambil jadwal berdasarkan tanggal
    // ==========================================
    case 'get_jadwal':
        header('Content-Type: application/json');
        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
        if (!$tanggal) { echo json_encode([]); exit; }
        $stmt = $pdo->prepare("SELECT * FROM jadwal WHERE tanggal = ? AND status = 'tersedia' ORDER BY jam_mulai");
        $stmt->execute([$tanggal]);
        echo json_encode($stmt->fetchAll());
        exit;

    // ==========================================
    // TAMBAH BOOKING
    // ==========================================
    case 'tambah_booking':
        $customer_id = $_POST['customer_id'] ?? '';
        $nama        = trim($_POST['nama'] ?? '');
        $no_hp       = trim($_POST['no_hp'] ?? '');
        $tanggal     = $_POST['tanggal'] ?? '';
        $jadwal_id   = !empty($_POST['jadwal_id']) ? $_POST['jadwal_id'] : null;
        $jam         = trim($_POST['jam'] ?? '');
        $paket       = trim($_POST['paket'] ?? '');
        $paket_name  = trim($_POST['paket_name'] ?? '');
        $paket_price = (int)($_POST['paket_price'] ?? 0);
        $total       = (int)($_POST['total'] ?? 0);
        $dp          = (int)($_POST['dp'] ?? 0);
        $metode_bayar= trim($_POST['metode_bayar'] ?? 'Transfer Bank');
        $bank        = trim($_POST['bank'] ?? '');
        $catatan     = trim($_POST['catatan'] ?? '');

        // Validasi wajib
        if (!$customer_id || !$nama || !$no_hp || !$tanggal || !$jam || !$paket || !$paket_name) {
            redirect('booking_tambah.php', 'Field bertanda * wajib diisi', 'error');
        }

        // Upload bukti transfer
        $buktiPath = null;
        if (!empty($_FILES['bukti_transfer']['name'])) {
            $buktiPath = uploadBukti($_FILES['bukti_transfer']);
            if (!$buktiPath) {
                redirect('booking_tambah.php', 'Gagal upload bukti transfer. Pastikan format gambar dan ukuran < 5MB', 'error');
            }
        }

        $noInvoice = generateInvoice($pdo);

        try {
            $pdo->beginTransaction();

            // 1. Insert booking
            $stmt = $pdo->prepare("INSERT INTO booking (no_invoice, customer_id, tanggal, jadwal_id, jam, paket, paket_name, paket_price, total, dp, nama, no_hp, catatan, metode_bayar, bank, bukti_transfer, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())");
            $stmt->execute([$noInvoice, $customer_id, $tanggal, $jadwal_id, $jam, $paket, $paket_name, $paket_price, $total, $dp, $nama, $no_hp, $catatan, $metode_bayar, $bank, $buktiPath, 'menunggu']);
            $bookingId = $pdo->lastInsertId();

            // 2. Insert detail_booking: paket utama
            $stmtDetail = $pdo->prepare("INSERT INTO detail_booking (booking_id, jenis, nama_item, harga, created_at) VALUES (?,'paket',?,?,NOW())");
            $stmtDetail->execute([$bookingId, $paket_name, $paket_price]);

            // 3. Insert detail_booking: addon
            $addonNama  = $_POST['addon_nama'] ?? [];
            $addonHarga = $_POST['addon_harga'] ?? [];
            if (!empty($addonNama)) {
                $stmtAddon = $pdo->prepare("INSERT INTO detail_booking (booking_id, jenis, nama_item, harga, created_at) VALUES (?,'addon',?,?,NOW())");
                foreach ($addonNama as $i => $an) {
                    $an = trim($an);
                    $ah = (int)($addonHarga[$i] ?? 0);
                    if ($an && $ah > 0) {
                        $stmtAddon->execute([$bookingId, $an, $ah]);
                    }
                }
            }

            // 4. Insert transaksi DP
            if ($dp > 0) {
                $stmtTx = $pdo->prepare("INSERT INTO transaksi (booking_id, no_invoice, tipe, jumlah, metode_bayar, bank, bukti_transfer, status, keterangan, created_at) VALUES (?,?,?,?,?,?,?,?,'Pembayaran DP',NOW())");
                $stmtTx->execute([$bookingId, $noInvoice, 'dp', $dp, $metode_bayar, $bank, $buktiPath, 'menunggu']);
            }

            // 5. Update jadwal status ke penuh jika ada jadwal_id
            if ($jadwal_id) {
                $pdo->prepare("UPDATE jadwal SET status = 'penuh' WHERE id = ?")->execute([$jadwal_id]);
            }

            $pdo->commit();
            redirect('booking_detail.php?id=' . $bookingId, 'Booking berhasil dibuat! Invoice: ' . $noInvoice);

        } catch (Exception $e) {
            $pdo->rollBack();
            redirect('booking_tambah.php', 'Gagal menyimpan booking: ' . $e->getMessage(), 'error');
        }
        break;

    // ==========================================
    // HAPUS BOOKING
    // ==========================================
    case 'hapus_booking':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('index.php', 'ID tidak valid', 'error');

        $b = $pdo->prepare("SELECT jadwal_id FROM booking WHERE id = ?");
        $b->execute([$id]);
        $booking = $b->fetch();

        try {
            $pdo->beginTransaction();

            // Hapus transaksi
            $pdo->prepare("DELETE FROM transaksi WHERE booking_id = ?")->execute([$id]);
            // Hapus detail_booking
            $pdo->prepare("DELETE FROM detail_booking WHERE booking_id = ?")->execute([$id]);
            // Hapus booking
            $pdo->prepare("DELETE FROM booking WHERE id = ?")->execute([$id]);
            // Kembalikan jadwal ke tersedia
            if ($booking && $booking['jadwal_id']) {
                $pdo->prepare("UPDATE jadwal SET status = 'tersedia' WHERE id = ?")->execute([$booking['jadwal_id']]);
            }

            $pdo->commit();
            redirect('index.php', 'Booking berhasil dihapus');
        } catch (Exception $e) {
            $pdo->rollBack();
            redirect('index.php', 'Gagal menghapus: ' . $e->getMessage(), 'error');
        }
        break;

    // ==========================================
    // UPDATE STATUS BOOKING
    // ==========================================
    case 'update_status':
        $id     = (int)($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? '';
        $allowedStatus = ['menunggu', 'dikonfirmasi', 'dibatalkan', 'selesai'];
        if (!$id || !in_array($status, $allowedStatus)) redirect('index.php', 'Parameter tidak valid', 'error');

        $pdo->prepare("UPDATE booking SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$status, $id]);

        // Jika dibatalkan, kembalikan jadwal ke tersedia
        if ($status === 'dibatalkan') {
            $b = $pdo->prepare("SELECT jadwal_id FROM booking WHERE id = ?");
            $b->execute([$id]);
            $booking = $b->fetch();
            if ($booking && $booking['jadwal_id']) {
                $pdo->prepare("UPDATE jadwal SET status = 'tersedia' WHERE id = ?")->execute([$booking['jadwal_id']]);
            }
        }

        redirect('booking_detail.php?id=' . $id, 'Status booking diperbarui ke "' . ucfirst($status) . '"');
        break;

    // ==========================================
    // HAPUS JADWAL
    // ==========================================
    case 'hapus_jadwal':
        $id = (int)($_GET['id'] ?? 0);
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
        if (!$id) redirect('jadwal.php', 'ID tidak valid', 'error');

        $pdo->prepare("DELETE FROM jadwal WHERE id = ?")->execute([$id]);
        redirect('jadwal.php?tanggal=' . $tanggal, 'Jadwal berhasil dihapus');
        break;

    // ==========================================
    // TAMBAH PELUNASAN
    // ==========================================
    case 'tambah_pelunasan':
        $booking_id  = (int)($_POST['booking_id'] ?? 0);
        $jumlah      = (int)($_POST['jumlah'] ?? 0);
        $metode_bayar= trim($_POST['metode_bayar'] ?? '');
        $bank        = trim($_POST['bank'] ?? '');
        $keterangan  = trim($_POST['keterangan'] ?? '');

        if (!$booking_id || !$jumlah || !$metode_bayar) {
            redirect('booking_detail.php?id=' . $booking_id, 'Field wajib tidak lengkap', 'error');
        }

        // Upload bukti
        $buktiPath = null;
        if (!empty($_FILES['bukti_transfer']['name'])) {
            $buktiPath = uploadBukti($_FILES['bukti_transfer'], 'bukti_transfer');
            if (!$buktiPath) {
                redirect('booking_detail.php?id=' . $booking_id, 'Gagal upload bukti transfer', 'error');
            }
        }

        // Ambil no_invoice
        $inv = $pdo->prepare("SELECT no_invoice FROM booking WHERE id = ?");
        $inv->execute([$booking_id]);
        $invoice = $inv->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO transaksi (booking_id, no_invoice, tipe, jumlah, metode_bayar, bank, bukti_transfer, status, keterangan, created_at) VALUES (?,?,'pelunasan',?,?,?,?,?,'menunggu',NOW())");
        $stmt->execute([$booking_id, $invoice, $jumlah, $metode_bayar, $bank, $buktiPath, $keterangan]);

        redirect('booking_detail.php?id=' . $booking_id, 'Bukti pelunasan berhasil diupload');
        break;

    // ==========================================
    // KONFIRMASI TRANSAKSI
    // ==========================================
    case 'konfirmasi_transaksi':
        $id         = (int)($_GET['id'] ?? 0);
        $booking_id = (int)($_GET['booking_id'] ?? 0);

        $pdo->prepare("UPDATE transaksi SET status = 'dikonfirmasi', verified_at = NOW() WHERE id = ?")->execute([$id]);

        // Cek apakah sudah lunas
        $b = $pdo->prepare("SELECT total FROM booking WHERE id = ?");
        $b->execute([$booking_id]);
        $totalBooking = $b->fetchColumn();

        $t = $pdo->prepare("SELECT SUM(jumlah) as dibayar FROM transaksi WHERE booking_id = ? AND status = 'dikonfirmasi'");
        $t->execute([$booking_id]);
        $totalDibayar = (int)$t->fetchColumn();

        // Jika sudah lunas, update status booking ke selesai
        if ($totalDibayar >= $totalBooking) {
            $pdo->prepare("UPDATE booking SET status = 'selesai', updated_at = NOW() WHERE id = ?")->execute([$booking_id]);
            redirect('booking_detail.php?id=' . $booking_id, 'Transaksi dikonfirmasi. Booking sudah LUNAS dan ditandai selesai!');
        }

        redirect('booking_detail.php?id=' . $booking_id, 'Transaksi DP/Pelunasan berhasil dikonfirmasi');
        break;

    // ==========================================
    // TOLAK TRANSAKSI
    // ==========================================
    case 'tolak_transaksi':
        $id         = (int)($_GET['id'] ?? 0);
        $booking_id = (int)$_GET['booking_id'] ?? 0;

        // Ambil info transaksi untuk keterangan
        $tx = $pdo->prepare("SELECT tipe FROM transaksi WHERE id = ?");
        $tx->execute([$id]);
        $txData = $tx->fetch();

        $pdo->prepare("UPDATE transaksi SET status = 'gagal', keterangan = CONCAT(IFNULL(keterangan,''), ' [DITOLAK ADMIN]'), verified_at = NOW() WHERE id = ?")->execute([$id]);

        if ($booking_id) {
            redirect('booking_detail.php?id=' . $booking_id, 'Transaksi ditolak', 'warning');
        } else {
            redirect('index.php', 'Transaksi ditolak', 'warning');
        }
        break;

        // ==========================================
    // EDIT JADWAL
    // ==========================================
    case 'edit_jadwal':
        $id             = (int)($_POST['id'] ?? 0);
        $jam_mulai      = trim($_POST['jam_mulai'] ?? '');
        $jam_selesai    = trim($_POST['jam_selesai'] ?? '');
        $tanggalRedirect= $_POST['tanggal_redirect'] ?? date('Y-m-d');

        if (!$id || !$jam_mulai || !$jam_selesai) {
            redirect('jadwal.php?tanggal=' . $tanggalRedirect, 'Jam mulai dan selesai wajib diisi', 'error');
        }

        // Validasi: jam selesai harus setelah jam mulai
        if ($jam_selesai <= $jam_mulai) {
            redirect('jadwal.php?tanggal=' . $tanggalRedirect, 'Jam selesai harus setelah jam mulai', 'error');
        }

        // Cek duplikat (kecuali dirinya sendiri)
        $cek = $pdo->prepare("SELECT id FROM jadwal WHERE tanggal=(SELECT tanggal FROM jadwal WHERE id=?) AND jam_mulai=? AND jam_selesai=? AND id != ?");
        $cek->execute([$id, $jam_mulai, $jam_selesai, $id]);
        if ($cek->fetch()) {
            redirect('jadwal.php?tanggal=' . $tanggalRedirect, 'Jadwal dengan waktu tersebut sudah ada di tanggal yang sama', 'warning');
        }

        $pdo->prepare("UPDATE jadwal SET jam_mulai=?, jam_selesai=? WHERE id=?")->execute([$jam_mulai, $jam_selesai, $id]);

        // Jika jam berubah, update juga kolom jam di tabel booking yang terkait
        $pdo->prepare("UPDATE booking SET jam=? WHERE jadwal_id=?")->execute([$jam_mulai, $id]);

        redirect('jadwal.php?tanggal=' . $tanggalRedirect, 'Jadwal berhasil diperbarui');
        break;

    // ==========================================
    // TOGGLE STATUS JADWAL (AJAX)
    // ==========================================
    case 'toggle_jadwal':
        header('Content-Type: application/json');
        $id     = (int)($_GET['id'] ?? 0);
        $status = $_GET['status'] ?? '';

        if (!$id || !in_array($status, ['tersedia', 'penuh'])) {
            echo json_encode(['success' => false, 'msg' => 'Parameter tidak valid']);
            exit;
        }

        // Cek apakah ada booking aktif
        $cek = $pdo->prepare("SELECT COUNT(*) FROM booking WHERE jadwal_id=? AND status NOT IN ('dibatalkan')");
        $cek->execute([$id]);
        $jumlahBooking = (int)$cek->fetchColumn();

        // Jika mau diubah ke tersedia tapi masih ada booking, tolak
        if ($status === 'tersedia' && $jumlahBooking > 0) {
            echo json_encode(['success' => false, 'msg' => 'Tidak bisa membuka jadwal — masih ada ' . $jumlahBooking . ' booking aktif']);
            exit;
        }

        $pdo->prepare("UPDATE jadwal SET status=? WHERE id=?")->execute([$status, $id]);
        echo json_encode(['success' => true, 'msg' => 'Status diperbarui']);
        exit;

    // ==========================================
    // DEFAULT: redirect ke index
    // ==========================================
    default:
        redirect('index.php');
        break;
}