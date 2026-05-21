<?php
require_once 'config.php';
require_once 'components.php';

// Statistik
 $statTotal     = $pdo->query("SELECT COUNT(*) FROM booking")->fetchColumn();
 $statMenunggu  = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='menunggu'")->fetchColumn();
 $statKonfirmasi= $pdo->query("SELECT COUNT(*) FROM booking WHERE status='dikonfirmasi'")->fetchColumn();
 $statSelesai   = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='selesai'")->fetchColumn();
 $statBatal     = $pdo->query("SELECT COUNT(*) FROM booking WHERE status='dibatalkan'")->fetchColumn();

// Total pendapatan (transaksi yang dikonfirmasi)
 $totalPendapatan = $pdo->query("SELECT COALESCE(SUM(jumlah),0) FROM transaksi WHERE status='dikonfirmasi'")->fetchColumn();

// Pendapatan bulan ini
 $pendapatanBulanIni = $pdo->prepare("SELECT COALESCE(SUM(jumlah),0) FROM transaksi WHERE status='dikonfirmasi' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())");
 $pendapatanBulanIni->execute();
 $pendBulanIni = $pendapatanBulanIni->fetchColumn();

// Pendapatan 7 hari terakhir (untuk chart)
 $chartData = [];
 $chartLabels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('D', strtotime($date));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah),0) FROM transaksi WHERE status='dikonfirmasi' AND DATE(created_at)=?");
    $stmt->execute([$date]);
    $chartData[] = (int)$stmt->fetchColumn();
}
 $chartMax = max(max($chartData), 1);

// Filter & pencarian booking
 $search = isset($_GET['q']) ? trim($_GET['q']) : '';
 $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

 $sql = "SELECT * FROM booking WHERE 1=1";
 $params = [];
if ($search) {
    $sql .= " AND (no_invoice LIKE ? OR nama LIKE ? OR no_hp LIKE ? OR paket_name LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like, $like]);
}
if ($filter && in_array($filter, ['menunggu','dikonfirmasi','dibatalkan','selesai'])) {
    $sql .= " AND status = ?";
    $params[] = $filter;
}
 $sql .= " ORDER BY created_at DESC LIMIT 8";

 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $recentBookings = $stmt->fetchAll();

// Booking terbaru menunggu
 $menungguList = $pdo->query("SELECT * FROM booking WHERE status='menunggu' ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Conversion rate
 $convRate = $statTotal > 0 ? round(($statSelesai / $statTotal) * 100) : 0;

renderHead('Dashboard');
renderTopNav('dashboard');
?>
<div class="app-layout">
<?php 
renderPageHeader(
    'Dashboard',
    '<a href="index.php">Home</a> <span style="margin:0 6px;color:var(--text-muted);">/</span> Overview',
    '<a href="booking_tambah.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Booking Baru</a>'
); ?>
    <div class="page-content">

        <!-- STAT CARDS -->
        <div class="stats-row">
            <div class="stat-card sc-gold">
                <div class="stat-top">
                    <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
                    <div class="stat-trend up"><i class="fa-solid fa-arrow-up" style="font-size:0.6rem;"></i> Bulan ini</div>
                </div>
                <div class="stat-value"><?= formatRupiah($pendBulanIni) ?></div>
                <div class="stat-label">Pendapatan Bulan Ini</div>
                <div class="stat-mini-chart">
                    <?php foreach ($chartData as $v): $h = max(($v/$chartMax)*100, 5); ?>
                        <div class="stat-mini-bar" style="height:<?= $h ?>%;"></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="stat-card sc-emerald">
                <div class="stat-top">
                    <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-trend up"><i class="fa-solid fa-arrow-up" style="font-size:0.6rem;"></i> Selesai</div>
                </div>
                <div class="stat-value"><?= $statSelesai ?></div>
                <div class="stat-label">Booking Selesai</div>
                <div class="stat-mini-chart">
                    <?php for ($i=0;$i<7;$i++): $h = rand(15,90); ?>
                        <div class="stat-mini-bar" style="height:<?= $h ?>%;"></div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="stat-card sc-amber">
                <div class="stat-top">
                    <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                    <?php if ($statMenunggu > 0): ?>
                        <div class="stat-trend neutral"><?= $statMenunggu ?> baru</div>
                    <?php else: ?>
                        <div class="stat-trend up">Clear</div>
                    <?php endif; ?>
                </div>
                <div class="stat-value"><?= $statMenunggu ?></div>
                <div class="stat-label">Menunggu Konfirmasi</div>
            </div>
            <div class="stat-card sc-sky">
                <div class="stat-top">
                    <div class="stat-icon"><i class="fa-solid fa-chart-pie"></i></div>
                    <div class="stat-trend <?= $convRate >= 50 ? 'up' : 'down' ?>"><?= $convRate ?>%</div>
                </div>
                <div class="stat-value"><?= $convRate ?>%</div>
                <div class="stat-label">Conversion Rate</div>
            </div>
            <div class="stat-card sc-rose">
                <div class="stat-top">
                    <div class="stat-icon"><i class="fa-solid fa-ban"></i></div>
                    <div class="stat-trend <?= $statBatal > 0 ? 'down' : 'up' ?>"><?= $statBatal ?></div>
                </div>
                <div class="stat-value"><?= $statBatal ?></div>
                <div class="stat-label">Dibatalkan</div>
            </div>
        </div>

        <!-- MAIN GRID: Chart + Menunggu -->
        <div style="display:grid;grid-template-columns:1fr 380px;gap:20px;margin-bottom:24px;">

            <!-- Chart: Pendapatan 7 Hari -->
            <div class="surface">
                <div class="surface-head">
                    <h2><i class="fa-solid fa-chart-column"></i> Pendapatan 7 Hari Terakhir</h2>
                    <span class="badge badge-gold">Total: <?= formatRupiah(array_sum($chartData)) ?></span>
                </div>
                <div class="surface-body">
                    <div class="css-chart">
                        <?php foreach ($chartData as $i => $v):
                            $h = max(($v/$chartMax)*100, 4);
                            $color = $i === 6 ? 'var(--accent-gold)' : 'var(--accent-gold-pale)';
                        ?>
                        <div class="css-chart-bar-wrap">
                            <div class="css-chart-bar" style="height:<?= $h ?>%;background:<?= $color ?>;">
                                <div class="bar-tooltip"><?= formatRupiah($v) ?></div>
                            </div>
                            <div class="css-chart-label"><?= $chartLabels[$i] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Menunggu Konfirmasi -->
            <div class="surface">
                <div class="surface-head">
                    <h2><i class="fa-solid fa-clock"></i> Menunggu</h2>
                    <span class="badge badge-amber"><?= $statMenunggu ?></span>
                </div>
                <div class="surface-body" style="padding:12px 16px;">
                    <?php if (empty($menungguList)): ?>
                        <div class="empty-state" style="padding:30px 12px;">
                            <div class="empty-icon"><i class="fa-solid fa-check-double"></i></div>
                            <p>Semua sudah diproses</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($menungguList as $mb): ?>
                        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border-light);">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--accent-amber-pale);color:var(--accent-amber);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.85rem;font-weight:700;">
                                <?= mb_substr($mb['nama'], 0, 1) ?>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.84rem;font-weight:600;color:var(--text-heading);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($mb['nama']) ?></div>
                                <div style="font-size:0.72rem;color:var(--text-muted);"><?= formatTanggal($mb['tanggal']) ?> &middot; <?= htmlspecialchars($mb['paket_name']) ?></div>
                            </div>
                            <a href="booking_detail.php?id=<?= $mb['id'] ?>" class="btn btn-ghost btn-icon btn-sm" title="Lihat Detail">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RINGKASAN BISNIS -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:24px;">
            <div class="surface">
                <div class="surface-body" style="text-align:center;padding:28px;">
                    <div class="progress-ring-wrap">
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="var(--border-light)" stroke-width="8"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="var(--accent-emerald)" stroke-width="8"
                                stroke-dasharray="<?= 2 * pi() * 42 ?>"
                                stroke-dashoffset="<?= 2 * pi() * 42 * (1 - $convRate/100) ?>"
                                stroke-linecap="round"
                                transform="rotate(-90 50 50)"
                                style="transition:stroke-dashoffset 1s ease;"/>
                            <text x="50" y="54" text-anchor="middle" font-size="20" font-weight="800" fill="var(--text-heading)" font-family="Playfair Display"><?= $convRate ?>%</text>
                        </svg>
                        <div class="progress-ring-text">Completion Rate</div>
                    </div>
                </div>
            </div>
            <div class="surface">
                <div class="surface-body" style="padding:28px;">
                    <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;margin-bottom:16px;">Business Summary</div>
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:0.84rem;color:var(--text-secondary);">Total Pendapatan</span>
                            <span style="font-size:0.95rem;font-weight:700;color:var(--accent-emerald);"><?= formatRupiah($totalPendapatan) ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:0.84rem;color:var(--text-secondary);">Total Booking</span>
                            <span style="font-size:0.95rem;font-weight:700;color:var(--text-heading);"><?= $statTotal ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:0.84rem;color:var(--text-secondary);">Dikonfirmasi</span>
                            <span style="font-size:0.95rem;font-weight:700;color:var(--accent-sky);"><?= $statKonfirmasi ?></span>
                        </div>
                        <hr style="border:none;border-top:1px solid var(--border-light);">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:0.84rem;color:var(--text-secondary);">Avg. per Booking</span>
                            <span style="font-size:0.95rem;font-weight:700;color:var(--accent-gold);"><?= formatRupiah($statSelesai > 0 ? round($totalPendapatan/$statSelesai) : 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="surface">
                <div class="surface-body" style="padding:28px;">
                    <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;margin-bottom:16px;">Status Breakdown</div>
                    <?php
                    $statusArr = [
                        ['Menunggu', $statMenunggu, 'amber'],
                        ['Dikonfirmasi', $statKonfirmasi, 'sky'],
                        ['Selesai', $statSelesai, 'emerald'],
                        ['Dibatalkan', $statBatal, 'rose'],
                    ];
                    foreach ($statusArr as $si):
                        $pct = $statTotal > 0 ? round(($si[1]/$statTotal)*100) : 0;
                    ?>
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                            <span style="font-size:0.8rem;color:var(--text-secondary);"><?= $si[0] ?></span>
                            <span style="font-size:0.8rem;font-weight:600;color:var(--text-heading);"><?= $si[1] ?> (<?= $pct ?>%)</span>
                        </div>
                        <div style="height:6px;background:var(--bg-sunken);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:<?= $pct ?>%;background:var(--accent-<?= $si[2] ?>);border-radius:3px;transition:width 0.8s ease;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- TABEL BOOKING TERBARU -->
        <div class="surface">
            <div class="surface-head">
                <h2><i class="fa-solid fa-list"></i> Booking Terbaru</h2>
                <div style="display:flex;gap:10px;align-items:center;">
                    <div class="inline-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <form method="GET" style="display:flex;">
                            <input type="text" name="q" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
                        </form>
                    </div>
                    <select class="filter-select" onchange="window.location='index.php?filter='+this.value<?= $search ? "+'&q='.urlencode('$search')" : '' ?>">
                        <option value="">Semua</option>
                        <option value="menunggu" <?= $filter==='menunggu'?'selected':'' ?>>Menunggu</option>
                        <option value="dikonfirmasi" <?= $filter==='dikonfirmasi'?'selected':'' ?>>Dikonfirmasi</option>
                        <option value="selesai" <?= $filter==='selesai'?'selected':'' ?>>Selesai</option>
                        <option value="dibatalkan" <?= $filter==='dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                    </select>
                </div>
            </div>
            <div class="surface-flush">
                <div class="table-wrap">
                    <?php if (empty($recentBookings)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                            <p>Belum ada data booking.</p>
                        </div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Paket</th>
                                <th>Total</th>
                                <th>DP</th>
                                <th>Status</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $b): ?>
                            <tr>
                                <td><strong style="color:var(--accent-gold);font-size:0.82rem;"><?= htmlspecialchars($b['no_invoice']) ?></strong></td>
                                <td style="font-size:0.82rem;"><?= formatTanggal($b['tanggal']) ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:32px;height:32px;border-radius:50%;background:var(--accent-gold-pale);color:var(--accent-gold);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;flex-shrink:0;">
                                            <?= mb_substr($b['nama'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:600;font-size:0.84rem;"><?= htmlspecialchars($b['nama']) ?></div>
                                            <div style="font-size:0.7rem;color:var(--text-muted);"><?= htmlspecialchars($b['no_hp']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:0.82rem;"><?= htmlspecialchars($b['paket_name']) ?></td>
                                <td style="font-weight:600;font-size:0.84rem;"><?= formatRupiah($b['total']) ?></td>
                                <td style="font-size:0.82rem;color:var(--text-secondary);"><?= formatRupiah($b['dp']) ?></td>
                                <td><?= badgeStatus($b['status']) ?></td>
                                <td style="text-align:center;">
                                    <a href="booking_detail.php?id=<?= $b['id'] ?>" class="btn btn-ghost btn-icon btn-sm" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                    <button class="btn btn-ghost btn-icon btn-sm" title="Hapus" onclick="konfirmasiHapus('aksi.php?action=hapus_booking&id=<?= $b['id'] ?>','<?= htmlspecialchars($b['no_invoice']) ?>')" style="color:var(--accent-rose);"><i class="fa-solid fa-trash-can"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php renderFooter(); ?>