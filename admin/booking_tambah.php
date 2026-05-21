<?php
require_once 'config.php';
require_once 'components.php';

renderHead('Booking Baru');
renderTopNav('booking-tambah');
?>
<div class="app-layout">
<?php renderPageHeader(
    'Booking Baru',
    '<a href="index.php">Home</a> <span style="margin:0 6px;color:var(--text-muted);">/</span> <span>Formulir</span>',
    '<a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>'
); ?>
    <div class="page-content">

        <div class="surface" style="max-width:880px;">
            <div class="surface-head" style="padding:20px 24px;">
                <h2><i class="fa-solid fa-pen-fancy"></i> Formulir Booking Baru</h2>
                <span class="badge badge-gold"><span class="badge-dot"></span> Draft</span>
            </div>
            <div class="surface-body" style="padding:28px;">

                <form id="formBooking" method="POST" action="aksi.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="tambah_booking">

                    <!-- ========== SECTION 1: CUSTOMER ========== -->
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--accent-gold-pale);color:var(--accent-gold);display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;color:var(--text-heading);">Informasi Customer</h3>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:8px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Customer ID <span class="req">*</span></label>
                            <input type="number" name="customer_id" class="form-input" placeholder="ID dari sistem" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                            <input type="text" name="nama" class="form-input" placeholder="Nama customer" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">No. HP <span class="req">*</span></label>
                            <input type="text" name="no_hp" class="form-input" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <div style="height:1px;background:var(--border-light);margin:28px 0;"></div>

                    <!-- ========== SECTION 2: JADWAL ========== -->
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--accent-sky-pale);color:var(--accent-sky);display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;color:var(--text-heading);">Jadwal & Waktu</h3>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 140px;gap:16px;margin-bottom:8px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Tanggal <span class="req">*</span></label>
                            <input type="date" name="tanggal" id="inputTanggal" class="form-input" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Slot Jadwal</label>
                            <select name="jadwal_id" id="selectJadwal" class="form-input">
                                <option value="">-- Pilih tanggal dulu --</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Jam <span class="req">*</span></label>
                            <input type="text" name="jam" id="inputJam" class="form-input" placeholder="08:00" required>
                        </div>
                    </div>
                    <div class="form-hint" style="margin-bottom:0;">
                        <i class="fa-solid fa-circle-info" style="margin-right:4px;"></i>
                        Pilih tanggal terlebih dahulu untuk melihat slot jadwal yang tersedia. Jam akan terisi otomatis saat memilih slot.
                    </div>

                    <div style="height:1px;background:var(--border-light);margin:28px 0;"></div>

                    <!-- ========== SECTION 3: PAKET & ADDON ========== -->
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--accent-violet-pale);color:var(--accent-violet);display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                            <i class="fa-solid fa-gem"></i>
                        </div>
                        <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;color:var(--text-heading);">Paket & Addon</h3>
                    </div>

                    <div style="display:grid;grid-template-columns:120px 1fr 180px;gap:16px;margin-bottom:8px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Kode Paket <span class="req">*</span></label>
                            <input type="text" name="paket" class="form-input" placeholder="PKT-01" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Nama Paket <span class="req">*</span></label>
                            <input type="text" name="paket_name" class="form-input" placeholder="Contoh: Paket Wisuda Full Makeup" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Harga Paket <span class="req">*</span></label>
                            <input type="number" name="paket_price" id="inputPaketPrice" class="form-input" placeholder="0" min="0" required oninput="hitungTotal()">
                        </div>
                    </div>

                    <!-- Addon Section -->
                    <div style="margin-top:16px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <span style="font-size:0.82rem;font-weight:600;color:var(--text-secondary);">
                                <i class="fa-solid fa-puzzle-piece" style="margin-right:5px;color:var(--accent-violet);"></i> Addon Tambahan
                            </span>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="tambahAddon()">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                        <div id="addonContainer">
                            <!-- Addon rows akan muncul di sini -->
                        </div>
                        <div id="addonEmpty" style="text-align:center;padding:20px;border:2px dashed var(--border-light);border-radius:var(--radius-md);color:var(--text-muted);font-size:0.82rem;">
                            <i class="fa-solid fa-inbox" style="font-size:1.3rem;display:block;margin-bottom:6px;opacity:0.4;"></i>
                            Belum ada addon. Klik "Tambah" untuk menambahkan layanan ekstra.
                        </div>
                    </div>

                    <div style="height:1px;background:var(--border-light);margin:28px 0;"></div>

                    <!-- ========== SECTION 4: PEMBAYARAN ========== -->
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                        <div style="width:32px;height:32px;border-radius:8px;background:var(--accent-emerald-pale);color:var(--accent-emerald);display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;color:var(--text-heading);">Pembayaran</h3>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Total Biaya <span class="req">*</span></label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;font-weight:600;">Rp</span>
                                <input type="number" name="total" id="inputTotal" class="form-input" style="padding-left:38px;font-weight:700;font-size:1rem;color:var(--accent-gold);" placeholder="0" min="0" required>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Uang Muka (DP) <span class="req">*</span></label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;font-weight:600;">Rp</span>
                                <input type="number" name="dp" id="inputDp" class="form-input" style="padding-left:38px;" placeholder="0" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Metode Bayar DP</label>
                            <select name="metode_bayar" class="form-input">
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="QRIS">QRIS</option>
                                <option value="E-Wallet">E-Wallet</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Bank / E-Wallet</label>
                            <input type="text" name="bank" class="form-input" placeholder="Contoh: BCA, Mandiri, GoPay">
                        </div>
                    </div>

                    <!-- Upload Area -->
                    <div class="form-group">
                        <label class="form-label">Bukti Transfer DP</label>
                        <div id="uploadArea" onclick="document.getElementById('fileInput').click()" style="border:2px dashed var(--border-medium);border-radius:var(--radius-md);padding:28px;text-align:center;cursor:pointer;transition:all 0.2s;background:var(--bg-muted);">
                            <div id="uploadPlaceholder">
                                <div style="width:48px;height:48px;margin:0 auto 10px;border-radius:50%;background:var(--bg-surface);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--text-muted);box-shadow:var(--shadow-xs);">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div style="font-size:0.85rem;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Klik untuk upload bukti transfer</div>
                                <div style="font-size:0.72rem;color:var(--text-muted);">JPG, PNG, GIF, WebP &middot; Maks 5MB</div>
                            </div>
                            <div id="uploadPreview" style="display:none;">
                                <img id="previewImg" src="" alt="Preview" style="max-width:180px;margin:0 auto 8px;border-radius:var(--radius-sm);box-shadow:var(--shadow-sm);">
                                <div id="previewName" style="font-size:0.78rem;color:var(--accent-emerald);font-weight:600;"><i class="fa-solid fa-check-circle"></i> File dipilih</div>
                            </div>
                        </div>
                        <input type="file" name="bukti_transfer" id="fileInput" class="form-input" accept="image/*" style="display:none;" onchange="previewUpload(this)">
                    </div>

                    <!-- Catatan -->
                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-input" placeholder="Catatan tambahan dari customer (opsional)" rows="3"></textarea>
                    </div>

                    <!-- Submit -->
                    <div style="display:flex;gap:12px;margin-top:8px;padding-top:20px;border-top:1px solid var(--border-light);">
                        <button type="submit" class="btn btn-primary" style="padding:12px 36px;font-size:0.9rem;">
                            <i class="fa-solid fa-check"></i> Simpan Booking
                        </button>
                        <a href="index.php" class="btn btn-secondary" style="padding:12px 24px;">Batal</a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
<?php renderFooter(); ?>

<script>
    let addonCount = 0;

    // Hover effect upload area
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.addEventListener('mouseover', function() {
        this.style.borderColor = 'var(--accent-gold)';
        this.style.background = 'var(--accent-gold-pale)';
    });
    uploadArea.addEventListener('mouseout', function() {
        this.style.borderColor = 'var(--border-medium)';
        this.style.background = 'var(--bg-muted)';
    });

    // Preview upload
    function previewUpload(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('previewName').innerHTML = '<i class="fa-solid fa-check-circle"></i> ' + file.name;
                document.getElementById('uploadPlaceholder').style.display = 'none';
                document.getElementById('uploadPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Load jadwal berdasarkan tanggal
    document.getElementById('inputTanggal').addEventListener('change', function() {
        const tanggal = this.value;
        const sel = document.getElementById('selectJadwal');
        const jamInput = document.getElementById('inputJam');
        sel.innerHTML = '<option value="">-- Memuat --</option>';
        jamInput.value = '';

        if (!tanggal) {
            sel.innerHTML = '<option value="">-- Pilih tanggal dulu --</option>';
            return;
        }

        fetch('aksi.php?action=get_jadwal&tanggal=' + encodeURIComponent(tanggal))
            .then(r => r.json())
            .then(data => {
                sel.innerHTML = '<option value="">-- Pilih slot (opsional) --</option>';
                if (data.length === 0) {
                    sel.innerHTML += '<option value="" disabled>Tidak ada jadwal tersedia</option>';
                } else {
                    data.forEach(j => {
                        const opt = document.createElement('option');
                        opt.value = j.id;
                        opt.textContent = j.jam_mulai + ' - ' + j.jam_selesai;
                        sel.appendChild(opt);
                    });
                }
            })
            .catch(() => {
                sel.innerHTML = '<option value="">Gagal memuat jadwal</option>';
            });
    });

    // Set jam otomatis saat pilih jadwal
    document.getElementById('selectJadwal').addEventListener('change', function() {
        const text = this.options[this.selectedIndex].text;
        const match = text.match(/^(\d{2}:\d{2})/);
        document.getElementById('inputJam').value = match ? match[1] : '';
    });

    // Hitung total otomatis
    function hitungTotal() {
        const paketPrice = parseInt(document.getElementById('inputPaketPrice').value) || 0;
        let addonTotal = 0;
        document.querySelectorAll('.addon-harga').forEach(el => {
            addonTotal += parseInt(el.value) || 0;
        });
        document.getElementById('inputTotal').value = paketPrice + addonTotal;
    }

    // Tambah baris addon
    function tambahAddon() {
        addonCount++;
        document.getElementById('addonEmpty').style.display = 'none';

        const row = document.createElement('div');
        row.className = 'addon-row';
        row.id = 'addon-' + addonCount;
        row.innerHTML = `
            <div class="form-group" style="margin:0;">
                <label class="form-label">Nama Addon</label>
                <input type="text" name="addon_nama[]" class="form-input" placeholder="Contoh: Eyelash extension">
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Harga</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.8rem;">Rp</span>
                    <input type="number" name="addon_harga[]" class="form-input addon-harga" style="padding-left:34px;" placeholder="0" min="0" value="0" oninput="hitungTotal()">
                </div>
            </div>
            <button type="button" class="btn btn-ghost btn-icon btn-sm" style="color:var(--accent-rose);margin-bottom:0;height:40px;align-self:end;" onclick="hapusAddon('addon-${addonCount}')" title="Hapus addon">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        `;
        document.getElementById('addonContainer').appendChild(row);

        // Focus ke input nama
        row.querySelector('input[type="text"]').focus();
    }

    // Hapus baris addon
    function hapusAddon(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                el.remove();
                hitungTotal();
                if (document.querySelectorAll('.addon-row').length === 0) {
                    document.getElementById('addonEmpty').style.display = 'block';
                }
            }, 200);
        }
    }

    // Set minimum tanggal hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('inputTanggal').setAttribute('min', today);
</script>