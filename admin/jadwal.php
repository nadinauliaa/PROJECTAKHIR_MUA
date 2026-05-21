<?php
require_once 'config.php';
require_once 'components.php';

// Handle tambah jadwal via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'tambah_jadwal') {
        $tanggal     = $_POST['tanggal'] ?? '';
        $jam_mulai   = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';

        if ($tanggal && $jam_mulai && $jam_selesai) {
            $cek = $pdo->prepare("SELECT id FROM jadwal WHERE tanggal=? AND jam_mulai=? AND jam_selesai=?");
            $cek->execute([$tanggal, $jam_mulai, $jam_selesai]);
            if (!$cek->fetch()) {
                $ins = $pdo->prepare("INSERT INTO jadwal (tanggal, jam_mulai, jam_selesai, status, created_at) VALUES (?,?,?,'tersedia',NOW())");
                $ins->execute([$tanggal, $jam_mulai, $jam_selesai]);
                redirect('jadwal.php?tanggal=' . $tanggal, 'Jadwal berhasil ditambahkan');
            } else {
                redirect('jadwal.php?tanggal=' . $tanggal, 'Jadwal dengan waktu tersebut sudah ada', 'warning');
            }
        } else {
            redirect('jadwal.php', 'Semua field wajib diisi', 'error');
        }
    }
}

 $filterTanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

 $sql = "SELECT j.*,
        (SELECT COUNT(*) FROM booking b WHERE b.jadwal_id = j.id AND b.status NOT IN ('dibatalkan')) as jumlah_booking
        FROM jadwal j WHERE j.tanggal = ? ORDER BY j.jam_mulai";
 $stmt = $pdo->prepare($sql);
 $stmt->execute([$filterTanggal]);
 $jadwalList = $stmt->fetchAll();

// Hitung ringkasan untuk tanggal ini
 $totalSlot = count($jadwalList);
 $tersediaSlot = 0;
 $penuhSlot = 0;
foreach ($jadwalList as $js) {
    if ($js['status'] === 'tersedia') $tersediaSlot++;
    else $penuhSlot++;
}

 $months = [
    '01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr',
    '05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug',
    '09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'
];
 $bulan = date('m', strtotime($filterTanggal));
 $tahun = date('Y', strtotime($filterTanggal));
 $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil tanggal yang punya jadwal di bulan ini (untuk dot indicator di kalender)
 $dotDates = $pdo->prepare("SELECT DISTINCT tanggal FROM jadwal WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? ORDER BY tanggal");
 $dotDates->execute(["$tahun-$bulan"]);
 $dotArr = [];
while ($dr = $dotDates->fetch()) {
    $dotArr[] = $dr['tanggal'];
}

renderHead('Kelola Jadwal');
renderTopNav('jadwal');
?>
<div class="app-layout">
<?php renderPageHeader(
    'Kelola Jadwal',
    '<a href="index.php">Home</a> <span style="margin:0 6px;color:var(--text-muted);">/</span> <span>Manajemen Jadwal</span>'
); ?>
    <div class="page-content">

        <!-- STAT RINGKASAN HARI INI -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
            <div class="surface" style="overflow:visible;">
                <div class="surface-body" style="padding:18px 22px;display:flex;align-items:center;gap:16px;">
                    <div style="width:46px;height:46px;border-radius:12px;background:var(--accent-sky-pale);color:var(--accent-sky);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;line-height:1.1;"><?= $totalSlot ?></div>
                        <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">Total Slot</div>
                    </div>
                </div>
            </div>
            <div class="surface" style="overflow:visible;">
                <div class="surface-body" style="padding:18px 22px;display:flex;align-items:center;gap:16px;">
                    <div style="width:46px;height:46px;border-radius:12px;background:var(--accent-emerald-pale);color:var(--accent-emerald);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;line-height:1.1;color:var(--accent-emerald);"><?= $tersediaSlot ?></div>
                        <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">Tersedia</div>
                    </div>
                </div>
            </div>
            <div class="surface" style="overflow:visible;">
                <div class="surface-body" style="padding:18px 22px;display:flex;align-items:center;gap:16px;">
                    <div style="width:46px;height:46px;border-radius:12px;background:var(--accent-rose-pale);color:var(--accent-rose);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;line-height:1.1;color:var(--accent-rose);"><?= $penuhSlot ?></div>
                        <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">Penuh</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LAYOUT 2 KOLOM UTAMA -->
        <div style="display:grid;grid-template-columns:380px 1fr;gap:24px;align-items:start;">

            <!-- ==================== KOLOM KIRI: KALENDER + FORM ==================== -->
            <div style="display:flex;flex-direction:column;gap:20px;">

                <!-- KALENDER -->
                <div class="surface">
                    <div class="surface-head">
                        <h2><i class="fa-solid fa-calendar-days"></i> Kalender</h2>
                        <div style="display:flex;gap:4px;">
                            <?php
                            $prevBulan = $bulan - 1; $prevTahun = $tahun;
                            if ($prevBulan < 1) { $prevBulan = 12; $prevTahun--; }
                            $nextBulan = $bulan + 1; $nextTahun = $tahun;
                            if ($nextBulan > 12) { $nextBulan = 1; $nextTahun++; }
                            ?>
                            <a href="?tanggal=<?= $prevTahun . '-' . str_pad($prevBulan,2,'0',STR_PAD_LEFT) . '-01' ?>" class="btn btn-ghost btn-icon btn-sm"><i class="fa-solid fa-chevron-left"></i></a>
                            <a href="?tanggal=<?= $nextTahun . '-' . str_pad($nextBulan,2,'0',STR_PAD_LEFT) . '-01' ?>" class="btn btn-ghost btn-icon btn-sm"><i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="surface-body" style="padding:18px;">

                        <!-- Bulan Title -->
                        <div style="text-align:center;margin-bottom:14px;">
                            <span style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700;color:var(--text-heading);"><?= $months[$bulan] ?> <?= $tahun ?></span>
                        </div>

                        <!-- Pill Bulan -->
                        <div class="month-pills" style="margin-bottom:16px;">
                            <?php foreach ($months as $num => $name): ?>
                                <?php $bulanTgl = $tahun . '-' . $num . '-01'; ?>
                                <a href="?tanggal=<?= $bulanTgl ?>" class="month-pill <?= ($num == $bulan) ? 'mp-active' : '' ?>"><?= $name ?></a>
                            <?php endforeach; ?>
                        </div>

                        <!-- Grid Hari -->
                        <div class="cal-grid">
                            <?php
                            $hariNama = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
                            foreach ($hariNama as $hn):
                            ?>
                                <div class="cal-head"><?= $hn ?></div>
                            <?php endforeach; ?>

                            <?php
                            $firstDayOfWeek = (int)date('w', strtotime("$tahun-$bulan-01"));
                            for ($i = 0; $i < $firstDayOfWeek; $i++):
                            ?>
                                <div class="cal-day cal-empty"></div>
                            <?php endfor; ?>

                            <?php for ($d = 1; $d <= $daysInMonth; $d++):
                                $tgl = "$tahun-$bulan-" . str_pad($d, 2, '0', STR_PAD_LEFT);
                                $isToday = ($tgl === date('Y-m-d'));
                                $isSelected = ($tgl === $filterTanggal);
                                $hasJadwal = in_array($tgl, $dotArr);

                                $class = 'cal-day';
                                if ($isSelected) $class .= ' cal-active';
                                elseif ($isToday) $class .= ' cal-today';
                            ?>
                                <a href="?tanggal=<?= $tgl ?>" style="text-decoration:none;">
                                    <div class="<?= $class ?>">
                                        <?= $d ?>
                                        <?php if ($hasJadwal && !$isSelected): ?>
                                            <div style="width:4px;height:4px;border-radius:50%;background:var(--accent-gold);margin:2px auto 0;"></div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- FORM TAMBAH JADWAL -->
                <div class="surface">
                    <div class="surface-head">
                        <h2><i class="fa-solid fa-plus-circle"></i> Tambah Slot Baru</h2>
                    </div>
                    <div class="surface-body" style="padding:20px;">
                        <form method="POST">
                            <input type="hidden" name="action" value="tambah_jadwal">
                            <div class="form-group">
                                <label class="form-label">Tanggal <span class="req">*</span></label>
                                <input type="date" name="tanggal" class="form-input" value="<?= $filterTanggal ?>" required>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Jam Mulai <span class="req">*</span></label>
                                    <input type="time" name="jam_mulai" class="form-input" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Jam Selesai <span class="req">*</span></label>
                                    <input type="time" name="jam_selesai" class="form-input" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                                <i class="fa-solid fa-plus"></i> Tambah Jadwal
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ==================== KOLOM KANAN: DAFTAR JADWAL ==================== -->
            <div class="surface">
                <div class="surface-head" style="padding:18px 24px;">
                    <h2>
                        <i class="fa-solid fa-clock"></i>
                        Slot Jadwal
                        <span style="font-family:'DM Sans',sans-serif;font-size:0.78rem;font-weight:500;color:var(--text-muted);margin-left:6px;"><?= formatTanggal($filterTanggal) ?></span>
                    </h2>
                    <span class="badge badge-gold"><?= $totalSlot ?> slot</span>
                </div>
                <div class="surface-body" style="padding:16px 20px;">

                    <?php if (empty($jadwalList)): ?>
                        <div class="empty-state" style="padding:60px 20px;">
                            <div class="empty-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                            <p style="margin-bottom:6px;">Tidak ada jadwal untuk tanggal ini</p>
                            <span style="font-size:0.78rem;">Gunakan form di samping untuk menambah slot baru</span>
                        </div>
                    <?php else: ?>

                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <?php foreach ($jadwalList as $j): ?>
                            <div class="jadwal-slot" id="jadwal-row-<?= $j['id'] ?>">

                                <!-- Kolom Kiri: Waktu -->
                                <div style="min-width:170px;">
                                    <div style="font-size:1rem;font-weight:700;color:var(--text-heading);display:flex;align-items:center;gap:6px;">
                                        <i class="fa-regular fa-clock" style="color:var(--accent-gold);font-size:0.9rem;"></i>
                                        <?= substr($j['jam_mulai'], 0, 5) ?>
                                        <span style="color:var(--text-muted);font-weight:400;font-size:0.85rem;">—</span>
                                        <?= substr($j['jam_selesai'], 0, 5) ?>
                                    </div>
                                    <div style="font-size:0.7rem;color:var(--text-muted);margin-top:3px;">
                                        ID: <?= $j['id'] ?>
                                        <?php if ($j['jumlah_booking'] > 0): ?>
                                            &middot; <span style="color:var(--accent-sky);font-weight:600;"><?= $j['jumlah_booking'] ?> booking</span>
                                        <?php else: ?>
                                            &middot; <span style="color:var(--text-muted);">0 booking</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Kolom Tengah: Status -->
                                <div id="status-badge-<?= $j['id'] ?>" style="min-width:110px;">
                                    <?php if ($j['status'] === 'tersedia'): ?>
                                        <span class="badge badge-emerald"><span class="badge-dot"></span> Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge badge-rose"><span class="badge-dot"></span> Penuh</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Spacer -->
                                <div style="flex:1;"></div>

                                <!-- Kolom Kanan: Aksi -->
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <button class="btn btn-sm <?= $j['status']==='tersedia' ? 'btn-warning' : 'btn-success' ?>"
                                            onclick="toggleStatus(<?= $j['id'] ?>, '<?= $j['status'] ?>', '<?= $filterTanggal ?>')"
                                            title="<?= $j['status']==='tersedia' ? 'Tandai Penuh' : 'Buka Kembali' ?>">
                                        <i class="fa-solid <?= $j['status']==='tersedia' ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                                        <span class="toggle-text-<?= $j['id'] ?>"><?= $j['status']==='tersedia' ? 'Penuh' : 'Buka' ?></span>
                                    </button>

                                    <button class="btn btn-ghost btn-sm" onclick="bukaEdit(<?= $j['id'] ?>, '<?= substr($j['jam_mulai'],0,5) ?>', '<?= substr($j['jam_selesai'],0,5) ?>')" title="Edit Jadwal">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <?php if ($j['jumlah_booking'] == 0): ?>
                                        <button class="btn btn-ghost btn-sm" style="color:var(--accent-rose);" title="Hapus Jadwal" onclick="konfirmasiHapus('aksi.php?action=hapus_jadwal&id=<?= $j['id'] ?>&tanggal=<?= $filterTanggal ?>','jadwal <?= substr($j['jam_mulai'],0,5) ?>-<?= substr($j['jam_selesai'],0,5) ?>')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    <?php else: ?>
                                        <span style="font-size:0.68rem;color:var(--text-muted);font-style:italic;padding:0 4px;" title="Ada booking aktif"><i class="fa-solid fa-shield-halved"></i></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Form Edit (Hidden) -->
                            <div id="edit-form-<?= $j['id'] ?>" class="jadwal-edit-form">
                                <form method="POST" action="aksi.php" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">
                                    <input type="hidden" name="action" value="edit_jadwal">
                                    <input type="hidden" name="id" value="<?= $j['id'] ?>">
                                    <input type="hidden" name="tanggal_redirect" value="<?= $filterTanggal ?>">
                                    <div class="form-group" style="margin:0;flex:1;">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" name="jam_mulai" id="edit-mulai-<?= $j['id'] ?>" class="form-input" required>
                                    </div>
                                    <div class="form-group" style="margin:0;flex:1;">
                                        <label class="form-label">Jam Selesai</label>
                                        <input type="time" name="jam_selesai" id="edit-selesai-<?= $j['id'] ?>" class="form-input" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i> Simpan</button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="tutupEdit(<?= $j['id'] ?>)"><i class="fa-solid fa-xmark"></i> Batal</button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    <?php endif; ?>

                </div>
            </div>

        </div>

    </div>
</div>
<?php renderFooter(); ?>

<script>
    // === BUKA FORM EDIT ===
    function bukaEdit(id, mulai, selesai) {
        document.querySelectorAll('.jadwal-edit-form').forEach(el => {
            el.style.display = 'none';
        });
        document.getElementById('edit-mulai-' + id).value = mulai;
        document.getElementById('edit-selesai-' + id).value = selesai;
        const form = document.getElementById('edit-form-' + id);
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // === TUTUP FORM EDIT ===
    function tutupEdit(id) {
        document.getElementById('edit-form-' + id).style.display = 'none';
    }

    // === TOGGLE STATUS (AJAX) ===
    function toggleStatus(id, statusSekarang, tanggal) {
        const statusBaru = (statusSekarang === 'tersedia') ? 'penuh' : 'tersedia';
        const labelBaru = (statusBaru === 'tersedia') ? 'Tersedia' : 'Penuh';

        // Optimistic UI update
        const badge = document.getElementById('status-badge-' + id);
        if (statusBaru === 'tersedia') {
            badge.innerHTML = '<span class="badge badge-emerald"><span class="badge-dot"></span> Tersedia</span>';
        } else {
            badge.innerHTML = '<span class="badge badge-rose"><span class="badge-dot"></span> Penuh</span>';
        }

        // Update tombol
        const row = document.getElementById('jadwal-row-' + id);
        const toggleBtn = row.querySelector('.btn-warning, .btn-success');
        if (toggleBtn) {
            if (statusBaru === 'tersedia') {
                toggleBtn.className = 'btn btn-sm btn-warning';
                toggleBtn.title = 'Tandai Penuh';
                toggleBtn.innerHTML = '<i class="fa-solid fa-lock"></i> <span class="toggle-text-' + id + '">Penuh</span>';
            } else {
                toggleBtn.className = 'btn btn-sm btn-success';
                toggleBtn.title = 'Buka Kembali';
                toggleBtn.innerHTML = '<i class="fa-solid fa-lock-open"></i> <span class="toggle-text-' + id + '">Buka</span>';
            }
            toggleBtn.setAttribute('onclick', "toggleStatus(" + id + ", '" + statusBaru + "', '" + tanggal + "')");
        }

        // Kirim ke server
        fetch('aksi.php?action=toggle_jadwal&id=' + id + '&status=' + statusBaru)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showToast(data.msg || 'Gagal mengubah status', 'error');
                    window.location.reload();
                } else {
                    showToast('Status diubah ke "' + labelBaru + '"', 'success');
                }
            })
            .catch(() => {
                showToast('Gagal menghubungi server', 'error');
                window.location.reload();
            });
    }
</script>