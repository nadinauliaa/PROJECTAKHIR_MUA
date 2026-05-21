<?php
require_once 'config.php';
require_once 'components.php';

 $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('index.php', 'ID booking tidak valid', 'error');

 $booking = $pdo->prepare("SELECT * FROM booking WHERE id = ?");
 $booking->execute([$id]);
 $b = $booking->fetch();
if (!$b) redirect('index.php', 'Booking tidak ditemukan', 'error');

 $details = $pdo->prepare("SELECT * FROM detail_booking WHERE booking_id = ? ORDER BY jenis, id");
 $details->execute([$id]);
 $detailList = $details->fetchAll();

 $transaksi = $pdo->prepare("SELECT * FROM transaksi WHERE booking_id = ? ORDER BY tipe, id");
 $transaksi->execute([$id]);
 $transList = $transaksi->fetchAll();

 $totalDibayar = 0;
foreach ($transList as $t) {
    if ($t['status'] === 'dikonfirmasi') {
        $totalDibayar += $t['jumlah'];
    }
}
 $sisaBayar = $b['total'] - $totalDibayar;

renderHead('Detail Booking');
renderTopNav('dashboard');
?>
<div class="app-layout">
<?php renderPageHeader(
    'Detail Booking',
    '<a href="index.php">Home</a> <span style="margin:0 6px;color:var(--text-muted);">/</span> <span>' . htmlspecialchars($b['no_invoice']) . '</span>',
    '<a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>'
); ?>
    <div class="page-content">

        <!-- INFO UTAMA -->
        <div class="surface" style="margin-bottom:24px;">
            <div class="surface-head">
                <h2 style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-receipt"></i>
                    <?= htmlspecialchars($b['no_invoice']) ?>
                    <?= badgeStatus($b['status']) ?>
                </h2>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php if ($b['status'] === 'menunggu'): ?>
                        <a href="aksi.php?action=update_status&id=<?= $b['id'] ?>&status=dikonfirmasi" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> Konfirmasi</a>
                        <a href="aksi.php?action=update_status&id=<?= $b['id'] ?>&status=dibatalkan" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Batalkan</a>
                    <?php elseif ($b['status'] === 'dikonfirmasi'): ?>
                        <a href="aksi.php?action=update_status&id=<?= $b['id'] ?>&status=selesai" class="btn btn-success btn-sm"><i class="fa-solid fa-flag-checkered"></i> Selesai</a>
                        <a href="aksi.php?action=update_status&id=<?= $b['id'] ?>&status=dibatalkan" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Batalkan</a>
                    <?php endif; ?>
                    <button class="btn btn-ghost btn-sm" style="color:var(--accent-rose);" onclick="konfirmasiHapus('aksi.php?action=hapus_booking&id=<?= $b['id'] ?>','booking ini')"><i class="fa-solid fa-trash-can"></i> Hapus</button>
                </div>
            </div>
            <div class="surface-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Customer</div>
                        <div class="value" style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--accent-gold-pale);color:var(--accent-gold);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;flex-shrink:0;">
                                <?= mb_substr($b['nama'], 0, 1) ?>
                            </div>
                            <?= htmlspecialchars($b['nama']) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="label">No. HP</div>
                        <div class="value"><?= htmlspecialchars($b['no_hp']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Customer ID</div>
                        <div class="value"><?= $b['customer_id'] ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Tanggal</div>
                        <div class="value"><?= formatTanggal($b['tanggal']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Jam</div>
                        <div class="value"><?= htmlspecialchars($b['jam']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Dibuat</div>
                        <div class="value"><?= $b['created_at'] ? date('d M Y H:i', strtotime($b['created_at'])) : '-' ?></div>
                    </div>
                    <?php if ($b['catatan']): ?>
                    <div class="info-item" style="grid-column:1/-1;">
                        <div class="label">Catatan</div>
                        <div class="value"><?= htmlspecialchars($b['catatan']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- GRID 2 KOLOM -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

            <!-- KIRI -->
            <div>
                <!-- Rincian Paket -->
                <div class="surface" style="margin-bottom:20px;">
                    <div class="surface-head">
                        <h2><i class="fa-solid fa-gem"></i> Rincian Paket</h2>
                    </div>
                    <?php if (empty($detailList)): ?>
                        <div class="surface-body">
                            <div class="empty-state" style="padding:30px;">
                                <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
                                <p>Tidak ada detail paket.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="surface-flush">
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr><th>Jenis</th><th>Item</th><th style="text-align:right;">Harga</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detailList as $d):
                                            $isPaket = $d['jenis'] === 'paket';
                                            $badgeClass = $isPaket ? 'badge-gold' : 'badge-violet';
                                            $badgeText = $isPaket ? 'Paket' : 'Addon';
                                        ?>
                                        <tr>
                                            <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                                            <td><?= htmlspecialchars($d['nama_item']) ?></td>
                                            <td style="text-align:right;font-weight:600;"><?= formatRupiah($d['harga']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr style="font-weight:700;border-top:2px solid var(--border-medium);">
                                            <td colspan="2" style="font-size:0.9rem;">Total</td>
                                            <td style="text-align:right;color:var(--accent-gold);font-size:1rem;"><?= formatRupiah($b['total']) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ringkasan Pembayaran -->
                <div class="surface">
                    <div class="surface-head">
                        <h2><i class="fa-solid fa-wallet"></i> Ringkasan Pembayaran</h2>
                    </div>
                    <div class="surface-body">
                        <div style="display:flex;flex-direction:column;gap:14px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="color:var(--text-secondary);font-size:0.88rem;">Total Tagihan</span>
                                <strong style="font-size:1rem;"><?= formatRupiah($b['total']) ?></strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="color:var(--text-secondary);font-size:0.88rem;">Sudah Dibayar</span>
                                <strong style="color:var(--accent-emerald);font-size:1rem;"><?= formatRupiah($totalDibayar) ?></strong>
                            </div>
                            <hr style="border:none;border-top:1px dashed var(--border-medium);">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-weight:600;color:var(--text-heading);">Sisa Pembayaran</span>
                                <?php if ($sisaBayar > 0): ?>
                                    <strong style="color:var(--accent-rose);font-size:1.15rem;"><?= formatRupiah($sisaBayar) ?></strong>
                                <?php else: ?>
                                    <span class="badge badge-emerald" style="font-size:0.82rem;padding:6px 16px;"><span class="badge-dot"></span> LUNAS</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN -->
            <div>
                <div class="surface" style="margin-bottom:20px;">
                    <div class="surface-head">
                        <h2><i class="fa-solid fa-arrow-right-arrow-left"></i> Riwayat Transaksi</h2>
                        <?php if ($sisaBayar > 0 && $b['status'] !== 'dibatalkan'): ?>
                            <button class="btn btn-primary btn-sm" onclick="document.getElementById('formPelunasan').style.display = document.getElementById('formPelunasan').style.display === 'none' ? 'block' : 'none'">
                                <i class="fa-solid fa-plus"></i> Pelunasan
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="surface-body" style="padding:16px;">
                        <?php if (empty($transList)): ?>
                            <div class="empty-state" style="padding:30px;">
                                <div class="empty-icon"><i class="fa-solid fa-receipt"></i></div>
                                <p>Belum ada transaksi.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($transList as $t):
                                $isDp = $t['tipe'] === 'dp';
                            ?>
                            <div class="tx-card">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                                    <span class="badge <?= $isDp ? 'badge-gold' : 'badge-emerald' ?>">
                                        <span class="badge-dot"></span>
                                        <?= $isDp ? 'DP / Uang Muka' : 'Pelunasan' ?>
                                    </span>
                                    <?= badgeTransaksi($t['status']) ?>
                                </div>
                                <div style="font-size:1.15rem;font-weight:700;margin-bottom:6px;font-family:'Playfair Display',serif;"><?= formatRupiah($t['jumlah']) ?></div>
                                <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:4px;">
                                    <i class="fa-solid fa-building-columns" style="margin-right:3px;"></i>
                                    <?= htmlspecialchars($t['metode_bayar']) ?>
                                    <?php if ($t['bank']): ?> (<?= htmlspecialchars($t['bank']) ?>)<?php endif; ?>
                                    &middot;
                                    <i class="fa-regular fa-clock" style="margin-right:3px;"></i>
                                    <?= $t['created_at'] ? date('d M Y H:i', strtotime($t['created_at'])) : '-' ?>
                                </div>
                                <?php if ($t['keterangan']): ?>
                                    <div style="font-size:0.82rem;color:var(--text-secondary);margin-top:6px;font-style:italic;">
                                        <i class="fa-solid fa-quote-left" style="font-size:0.65rem;margin-right:4px;opacity:0.5;"></i>
                                        <?= htmlspecialchars($t['keterangan']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($t['bukti_transfer']): ?>
                                    <div style="margin-top:10px;">
                                        <img src="<?= htmlspecialchars($t['bukti_transfer']) ?>" class="bukti-img" alt="Bukti Transfer" onclick="bukaLightbox(this.src)">
                                    </div>
                                <?php endif; ?>
                                <?php if ($t['status'] === 'menunggu'): ?>
                                <div style="margin-top:12px;display:flex;gap:8px;">
                                    <a href="aksi.php?action=konfirmasi_transaksi&id=<?= $t['id'] ?>&booking_id=<?= $b['id'] ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> Konfirmasi</a>
                                    <a href="aksi.php?action=tolak_transaksi&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Tolak</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- FORM PELUNASAN -->
                <?php if ($sisaBayar > 0 && $b['status'] !== 'dibatalkan'): ?>
                <div class="surface" id="formPelunasan" style="display:none;">
                    <div class="surface-head">
                        <h2><i class="fa-solid fa-upload"></i> Upload Pelunasan</h2>
                    </div>
                    <div class="surface-body">
                        <form method="POST" action="aksi.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="tambah_pelunasan">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <div class="form-group">
                                <label class="form-label">Jumlah Pelunasan</label>
                                <input type="number" name="jumlah" class="form-input" value="<?= $sisaBayar ?>" min="0" required>
                                <div class="form-hint">Sisa tagihan: <?= formatRupiah($sisaBayar) ?></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Metode Bayar</label>
                                <select name="metode_bayar" class="form-input" required>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bank / E-Wallet</label>
                                <input type="text" name="bank" class="form-input" placeholder="Contoh: BCA, Mandiri, GoPay">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bukti Transfer <span class="req">*</span></label>
                                <input type="file" name="bukti_transfer" class="form-input" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-input" placeholder="Opsional"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Upload Pelunasan</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
<?php renderFooter(); ?>