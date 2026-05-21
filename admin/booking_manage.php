<?php
require_once 'config.php';
require_once 'components.php';

// ============ STATISTIK ============
 $statTotal      = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
 $statMenunggu   = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='menunggu'")->fetchColumn();
 $statKonfirmasi = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='dikonfirmasi'")->fetchColumn();
 $statSelesai    = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='selesai'")->fetchColumn();
 $statBatal      = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='dibatalkan'")->fetchColumn();
 $totalPendapatan= $pdo->query("SELECT COALESCE(SUM(jumlah),0) FROM transaksi WHERE status='dikonfirmasi'")->fetchColumn();

// ============ FILTER & PENCARIAN ============
 $search = isset($_GET['q']) ? trim($_GET['q']) : '';
 $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
 $hal    = isset($_GET['hal']) ? max(1, (int)$_GET['hal']) : 1;
 $perPage = 10;

 $sql  = "SELECT b.* FROM booking b WHERE 1=1";
 $params = [];

if ($search) {
    $sql .= " AND (b.no_invoice LIKE ? OR b.nama LIKE ? OR b.no_hp LIKE ? OR b.paket_name LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($filter && in_array($filter, ['menunggu','dikonfirmasi','dibatalkan','selesai'])) {
    $sql .= " AND b.status = ?";
    $params[] = $filter;
}

// Hitung total untuk pagination
 $sqlCount = str_replace('b.*', 'COUNT(*)', $sql);
 $stmtCount = $pdo->prepare($sqlCount);
 $stmtCount->execute($params);
 $totalRows = (int)$stmtCount->fetchColumn();
 $totalHal  = ceil($totalRows / $perPage);
 $offset    = ($hal - 1) * $perPage;

 $sql .= " ORDER BY b.created_at DESC LIMIT $perPage OFFSET $offset";
 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $bookings = $stmt->fetchAll();

// ============ AMBIL SEMUA BUKTI TRANSFER UNTUK MODAL ============
 $allTransaksi = [];
if (!empty($bookings)) {
    $ids = array_column($bookings, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $txStmt = $pdo->prepare("SELECT * FROM transaksi WHERE booking_id IN ($placeholders) ORDER BY created_at DESC");
    $txStmt->execute($ids);
    $txRows = $txStmt->fetchAll();
    foreach ($txRows as $tx) {
        $allTransaksi[$tx['booking_id']][] = $tx;
    }
}

renderHead('Kelola Booking');
renderTopNav('booking-manage');
?>
<div class="app-layout">
<?php renderPageHeader(
    'Kelola Booking',
    '<a href="index.php">Home</a> <span style="margin:0 6px;color:var(--text-muted);">/</span> <span>Semua Booking</span>',
    '<a href="booking_tambah.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Booking Baru</a>'
); ?>
    <div class="page-content">

        <!-- ============ STAT MINI ============ -->
        <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:24px;">
            <div style="background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:var(--text-heading);"><?= $statTotal ?></div>
                <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;margin-top:2px;">Total</div>
            </div>
            <div style="background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:var(--accent-amber);"><?= $statMenunggu ?></div>
                <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;margin-top:2px;">Menunggu</div>
            </div>
            <div style="background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:var(--accent-sky);"><?= $statKonfirmasi ?></div>
                <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;margin-top:2px;">Dikonfirmasi</div>
            </div>
            <div style="background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:var(--accent-emerald);"><?= $statSelesai ?></div>
                <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;margin-top:2px;">Selesai</div>
            </div>
            <div style="background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:var(--accent-rose);"><?= $statBatal ?></div>
                <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;margin-top:2px;">Dibatalkan</div>
            </div>
            <div style="background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-md);padding:16px;text-align:center;">
                <div style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:var(--accent-gold);"><?= formatRupiah($totalPendapatan) ?></div>
                <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;margin-top:2px;">Pendapatan</div>
            </div>
        </div>

        <!-- ============ FILTER BAR ============ -->
        <div class="surface" style="margin-bottom:20px;">
            <div class="surface-body" style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <div class="inline-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <form method="GET" style="display:flex;">
                            <input type="text" name="q" placeholder="Cari invoice, nama, paket..." value="<?= htmlspecialchars($search) ?>">
                            <?php if ($filter): ?><input type="hidden" name="filter" value="<?= $filter ?>"><?php endif; ?>
                        </form>
                    </div>
                    <select class="filter-select" onchange="window.location='?filter='+this.value<?= $search ? "+'&q='+encodeURIComponent('".addslashes($search)."')" : '' ?>">
                        <option value="">Semua Status</option>
                        <option value="menunggu" <?= $filter==='menunggu'?'selected':'' ?>>Menunggu</option>
                        <option value="dikonfirmasi" <?= $filter==='dikonfirmasi'?'selected':'' ?>>Dikonfirmasi</option>
                        <option value="selesai" <?= $filter==='selesai'?'selected':'' ?>>Selesai</option>
                        <option value="dibatalkan" <?= $filter==='dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                    </select>
                    <?php if ($filter || $search): ?>
                        <a href="booking_manage.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-xmark"></i> Reset</a>
                    <?php endif; ?>
                </div>
                <div style="font-size:0.78rem;color:var(--text-muted);">
                    <?= $totalRows ?> booking ditemukan
                </div>
            </div>
        </div>

        <!-- ============ TABEL BOOKING ============ -->
        <div class="surface">
            <div class="surface-flush">
                <div class="table-wrap">
                    <?php if (empty($bookings)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                            <p>Tidak ada booking ditemukan.</p>
                        </div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Paket</th>
                                <th style="text-align:right;">Total</th>
                                <th style="text-align:right;">DP</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th style="text-align:center;">Bukti TF</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b):
                                // Hitung pembayaran status
                                $txList = $allTransaksi[$b['id']] ?? [];
                                $dpStatus = '-'; $lunasStatus = '-';
                                foreach ($txList as $tx) {
                                    if ($tx['tipe'] === 'dp') {
                                        $dpStatus = $tx['status'] === 'dikonfirmasi' ? '<span class="badge badge-emerald" style="font-size:0.65rem;padding:2px 8px;">DP OK</span>' : ($tx['status'] === 'menunggu' ? '<span class="badge badge-amber" style="font-size:0.65rem;padding:2px 8px;">DP Wait</span>' : '<span class="badge badge-rose" style="font-size:0.65rem;padding:2px 8px;">DP Fail</span>');
                                    }
                                    if ($tx['tipe'] === 'pelunasan') {
                                        $lunasStatus = $tx['status'] === 'dikonfirmasi' ? '<span class="badge badge-emerald" style="font-size:0.65rem;padding:2px 8px;">Lunas</span>' : ($tx['status'] === 'menunggu' ? '<span class="badge badge-amber" style="font-size:0.65rem;padding:2px 8px;">Lunas Wait</span>' : '<span class="badge badge-rose" style="font-size:0.65rem;padding:2px 8px;">Lunas Fail</span>');
                                    }
                                }
                                $bayarHtml = $dpStatus;
                                if ($lunasStatus !== '-') $bayarHtml .= ' ' . $lunasStatus;

                                // Cek ada bukti atau tidak
                                $hasBukti = false;
                                foreach ($txList as $tx) {
                                    if (!empty($tx['bukti_transfer'])) { $hasBukti = true; break; }
                                }
                            ?>
                            <tr>
                                <td>
                                    <strong style="color:var(--accent-gold);font-size:0.8rem;"><?= htmlspecialchars($b['no_invoice']) ?></strong>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:32px;height:32px;border-radius:50%;background:var(--accent-gold-pale);color:var(--accent-gold);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;flex-shrink:0;">
                                            <?= mb_substr($b['nama'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:600;font-size:0.82rem;"><?= htmlspecialchars($b['nama']) ?></div>
                                            <div style="font-size:0.68rem;color:var(--text-muted);"><?= htmlspecialchars($b['no_hp']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:0.8rem;"><?= formatTanggal($b['tanggal']) ?><br><span style="color:var(--text-muted);font-size:0.7rem;"><?= htmlspecialchars($b['jam']) ?></span></td>
                                <td style="font-size:0.8rem;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= htmlspecialchars($b['paket_name']) ?>"><?= htmlspecialchars($b['paket_name']) ?></td>
                                <td style="text-align:right;font-weight:600;font-size:0.84rem;"><?= formatRupiah($b['total']) ?></td>
                                <td style="text-align:right;font-size:0.82rem;color:var(--text-secondary);"><?= formatRupiah($b['dp']) ?></td>
                                <td style="font-size:0.72rem;"><?= $bayarHtml ?></td>
                                <td><?= badgeStatus($b['status']) ?></td>
                                <td style="text-align:center;">
                                    <?php if ($hasBukti): ?>
                                        <button class="btn btn-ghost btn-icon btn-sm" onclick="bukaBukti(<?= $b['id'] ?>)" title="Lihat Bukti Transfer" style="color:var(--accent-emerald);">
                                            <i class="fa-solid fa-image"></i>
                                        </button>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted);font-size:0.7rem;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex;gap:4px;justify-content:center;">
                                        <a href="booking_detail.php?id=<?= $b['id'] ?>" class="btn btn-ghost btn-icon btn-sm" title="Detail"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                        <?php if ($b['status'] === 'menunggu'): ?>
                                            <a href="aksi.php?action=update_status&id=<?= $b['id'] ?>&status=dikonfirmasi" class="btn btn-ghost btn-icon btn-sm" title="Konfirmasi" style="color:var(--accent-emerald);"><i class="fa-solid fa-check"></i></a>
                                        <?php endif; ?>
                                        <?php if (in_array($b['status'], ['menunggu','dikonfirmasi'])): ?>
                                            <a href="aksi.php?action=update_status&id=<?= $b['id'] ?>&status=dibatalkan" class="btn btn-ghost btn-icon btn-sm" title="Batalkan" style="color:var(--accent-rose);"><i class="fa-solid fa-xmark"></i></a>
                                        <?php endif; ?>
                                        <button class="btn btn-ghost btn-icon btn-sm" title="Hapus" style="color:var(--accent-rose);" onclick="konfirmasiHapus('aksi.php?action=hapus_booking&id=<?= $b['id'] ?>','<?= htmlspecialchars($b['no_invoice']) ?>')"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ============ PAGINATION ============ -->
            <?php if ($totalHal > 1): ?>
            <div style="padding:14px 20px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:0.78rem;color:var(--text-muted);">
                    Halaman <?= $hal ?> dari <?= $totalHal ?>
                </div>
                <div style="display:flex;gap:4px;">
                    <?php if ($hal > 1): ?>
                        <a href="?hal=1<?= $filter ? '&filter='.$filter : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="btn btn-secondary btn-xs"><i class="fa-solid fa-angles-left"></i></a>
                        <a href="?hal=<?= $hal-1 ?><?= $filter ? '&filter='.$filter : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="btn btn-secondary btn-xs"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php
                    // Tampilkan max 5 halaman di sekitar halaman aktif
                    $startPage = max(1, $hal - 2);
                    $endPage = min($totalHal, $hal + 2);
                    for ($p = $startPage; $p <= $endPage; $p++):
                        $isActive = ($p === $hal);
                    ?>
                        <a href="?hal=<?= $p ?><?= $filter ? '&filter='.$filter : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>"
                           class="btn btn-xs <?= $isActive ? 'btn-primary' : 'btn-secondary' ?>"><?= $p ?></a>
                    <?php endfor; ?>

                    <?php if ($hal < $totalHal): ?>
                        <a href="?hal=<?= $hal+1 ?><?= $filter ? '&filter='.$filter : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="btn btn-secondary btn-xs"><i class="fa-solid fa-chevron-right"></i></a>
                        <a href="?hal=<?= $totalHal ?><?= $filter ? '&filter='.$filter : '' ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="btn btn-secondary btn-xs"><i class="fa-solid fa-angles-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- ============ MODAL BUKTI TRANSFER ============ -->
<?php foreach ($bookings as $b):
    $txList = $allTransaksi[$b['id']] ?? [];
    // Hanya render modal kalau ada bukti
    $modalNeeded = false;
    foreach ($txList as $tx) { if (!empty($tx['bukti_transfer'])) { $modalNeeded = true; break; } }
    if (!$modalNeeded) continue;
?>
<div class="modal-overlay" id="modalBukti-<?= $b['id'] ?>" onclick="if(event.target===this)tutupBukti(<?= $b['id'] ?>)">
    <div class="modal-panel" style="max-width:620px;max-height:85vh;overflow-y:auto;">
        <!-- Header -->
        <div style="padding:22px 24px 0;display:flex;align-items:center;gap:12px;">
            <div style="width:42px;height:42px;border-radius:50%;background:var(--accent-emerald-pale);color:var(--accent-emerald);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                <i class="fa-solid fa-images"></i>
            </div>
            <div style="flex:1;">
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-heading);">Bukti Transfer</h3>
                <div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars($b['no_invoice']) ?> &middot; <?= htmlspecialchars($b['nama']) ?></div>
            </div>
            <button class="btn btn-ghost btn-icon" onclick="tutupBukti(<?= $b['id'] ?>)"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Body: Daftar Bukti -->
        <div style="padding:20px 24px;">
            <div style="display:flex;flex-direction:column;gap:16px;">
                <?php foreach ($txList as $tx):
                    if (empty($tx['bukti_transfer'])) continue;
                    $isDp = $tx['tipe'] === 'dp';
                ?>
                <div style="border:1px solid var(--border-light);border-radius:var(--radius-md);overflow:hidden;">
                    <!-- Label tipe -->
                    <div style="padding:10px 16px;background:var(--bg-muted);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-light);">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="badge <?= $isDp ? 'badge-gold' : 'badge-emerald' ?>">
                                <span class="badge-dot"></span>
                                <?= $isDp ? 'DP / Uang Muka' : 'Pelunasan' ?>
                            </span>
                            <?= badgeTransaksi($tx['status']) ?>
                        </div>
                        <div style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:var(--text-heading);">
                            <?= formatRupiah($tx['jumlah']) ?>
                        </div>
                    </div>

                    <!-- Gambar -->
                    <div style="padding:16px;text-align:center;background:var(--bg-surface);">
                        <img src="<?= htmlspecialchars($tx['bukti_transfer']) ?>" alt="Bukti Transfer"
                             style="max-width:100%;max-height:300px;border-radius:var(--radius-sm);border:1px solid var(--border-light);cursor:pointer;transition:transform 0.2s;"
                             onclick="bukaLightbox(this.src)"
                             onmouseover="this.style.transform='scale(1.02)'"
                             onmouseout="this.style.transform='none'">
                    </div>

                    <!-- Info detail -->
                    <div style="padding:14px 16px;border-top:1px solid var(--border-light);background:var(--bg-muted);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div>
                                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);font-weight:600;margin-bottom:2px;">Metode Bayar</div>
                                <div style="font-size:0.84rem;font-weight:600;color:var(--text-heading);">
                                    <i class="fa-solid fa-building-columns" style="margin-right:4px;color:var(--accent-gold);font-size:0.75rem;"></i>
                                    <?= htmlspecialchars($tx['metode_bayar']) ?>
                                </div>
                            </div>
                            <div>
                                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);font-weight:600;margin-bottom:2px;">Bank / E-Wallet</div>
                                <div style="font-size:0.84rem;font-weight:600;color:var(--text-heading);">
                                    <?= htmlspecialchars($tx['bank']) ?: '-' ?>
                                </div>
                            </div>
                            <div>
                                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);font-weight:600;margin-bottom:2px;">Waktu Upload</div>
                                <div style="font-size:0.84rem;font-weight:600;color:var(--text-heading);">
                                    <?= $tx['created_at'] ? date('d M Y, H:i', strtotime($tx['created_at'])) : '-' ?>
                                </div>
                            </div>
                            <div>
                                <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);font-weight:600;margin-bottom:2px;">Diverifikasi</div>
                                <div style="font-size:0.84rem;font-weight:600;color:var(--text-heading);">
                                    <?= $tx['verified_at'] ? date('d M Y, H:i', strtotime($tx['verified_at'])) : '<span style="color:var(--text-muted);">Belum</span>' ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($tx['keterangan']): ?>
                        <div style="margin-top:12px;padding:10px 14px;background:var(--bg-surface);border:1px solid var(--border-light);border-radius:var(--radius-sm);">
                            <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.6px;color:var(--text-muted);font-weight:600;margin-bottom:4px;">Keterangan</div>
                            <div style="font-size:0.84rem;color:var(--text-secondary);font-style:italic;">
                                <i class="fa-solid fa-quote-left" style="font-size:0.6rem;margin-right:4px;opacity:0.4;"></i>
                                <?= htmlspecialchars($tx['keterangan']) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Aksi konfirmasi/tolak -->
                        <?php if ($tx['status'] === 'menunggu'): ?>
                        <div style="margin-top:14px;display:flex;gap:8px;justify-content:flex-end;">
                            <a href="aksi.php?action=konfirmasi_transaksi&id=<?= $tx['id'] ?>&booking_id=<?= $b['id'] ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> Konfirmasi</a>
                            <a href="aksi.php?action=tolak_transaksi&id=<?= $tx['id'] ?>&booking_id=<?= $b['id'] ?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Tolak</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php renderFooter(); ?>

<script>
    // Buka modal bukti
    function bukaBukti(bookingId) {
        const modal = document.getElementById('modalBukti-' + bookingId);
        if (modal) modal.classList.add('show');
    }

    // Tutup modal bukti
    function tutupBukti(bookingId) {
        const modal = document.getElementById('modalBukti-' + bookingId);
        if (modal) modal.classList.remove('show');
    }

    // Tutup modal dengan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.show').forEach(m => {
                if (m.id && m.id.startsWith('modalBukti-')) {
                    m.classList.remove('show');
                } else {
                    m.classList.remove('show');
                }
            });
        }
    });
</script>