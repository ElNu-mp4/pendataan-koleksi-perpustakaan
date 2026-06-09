<?php
/**
 * FILE: application/views/pendataan/rekap.php
 * Gantikan seluruh file dengan ini.
 *
 * Perubahan dari versi sebelumnya:
 *  - Stat cards diganti: total laporan, judul tercetak, judul digital, anggota 2025
 *  - Chart section baru dengan 5 chart yang relevan dengan data aktual:
 *      1. Koleksi Saat Ini (bar horizontal — tercetak vs digital)
 *      2. Pertumbuhan Koleksi 2025 vs 2026 (grouped bar)
 *      3. Pengunjung & Akses 2025 vs 2026 (grouped bar)
 *      4. Pemanfaatan Tercetak 2025 vs 2026 (grouped bar)
 *      5. Distribusi Jenis Perpustakaan (donut)
 *  - Chart.js 4.4.1 dari cdnjs
 */
?>
<div class="container survey-container">
    <div class="row">
        <div class="col-md-12">

            <!-- Alert Messages -->
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="survey-intro">
                <h4>Rekap Pendataan Koleksi Perpustakaan</h4>
                <p>Halaman ini menampilkan rekap dan analisis data pendataan koleksi perpustakaan yang telah diinput.
                   Gunakan filter untuk menyaring data berdasarkan tahun dan nomor pokok perpustakaan.</p>
            </div>

            <!-- ═══════ FILTER ═══════ -->
            <div class="section-header with-actions">
                <span><i class="fa fa-filter"></i> FILTER DATA</span>
                <button type="button" class="btn-toggle-filter" onclick="toggleAdvancedFilter()">
                    <i class="fa fa-angle-down" id="filter-icon"></i> Tampilkan Filter Lanjutan
                </button>
            </div>

            <div class="question-item">
                <div class="row filter-row">
                    <div class="col-md-2 filter-col">
                        <label class="question-label">Tahun Data</label>
                        <select id="filter-tahun" class="form-control">
                            <?php if(!empty($list_tahun)): ?>
                                <?php foreach($list_tahun as $t): ?>
                                    <option value="<?php echo $t['tahun']; ?>"><?php echo $t['tahun']; ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4 filter-col">
                        <label class="question-label">Nomor Pokok Perpustakaan</label>
                        <input type="text" id="filter-nomor" class="form-control" placeholder="Cari berdasarkan nomor pokok...">
                    </div>
                    <div class="col-md-2 filter-col">
                        <label class="question-label">Per Halaman</label>
                        <select id="per-page" class="form-control" onchange="changePerPage()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-4 filter-col filter-buttons">
                        <label class="question-label">&nbsp;</label>
                        <div class="button-group">
                            <button type="button" class="btn-filter" onclick="applyFilter()"><i class="fa fa-search"></i> Filter</button>
                            <button type="button" class="btn-reset"  onclick="resetFilter()"><i class="fa fa-refresh"></i> Reset</button>
                        </div>
                    </div>
                </div>
                <div id="advanced-filter" style="display:none;">
                    <hr class="filter-divider">
                    <div class="row filter-row">
                        <div class="col-md-3 filter-col">
                            <label class="question-label">Tanggal Input (Dari)</label>
                            <input type="date" id="filter-date-from" class="form-control">
                        </div>
                        <div class="col-md-3 filter-col">
                            <label class="question-label">Tanggal Input (Sampai)</label>
                            <input type="date" id="filter-date-to" class="form-control">
                        </div>
                        <div class="col-md-6 filter-col">
                            <label class="question-label">Urutkan Berdasarkan</label>
                            <select id="sort-by" class="form-control">
                                <option value="tanggal_desc">Tanggal Terbaru</option>
                                <option value="tanggal_asc">Tanggal Terlama</option>
                                <option value="nomor_asc">Nomor Pokok (Naik)</option>
                                <option value="nomor_desc">Nomor Pokok (Turun)</option>
                                <option value="jawaban_desc">Jumlah Jawaban (Terbanyak)</option>
                                <option value="jawaban_asc">Jumlah Jawaban (Tersedikit)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════ STAT CARDS ═══════ -->
            <div class="stats-container" id="stats-container">
                <div class="stat-card" id="sc-laporan">
                    <div class="stat-icon"><i class="fa fa-file-text-o"></i></div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-laporan"><span class="stat-spinner"><i class="fa fa-spinner fa-spin"></i></span></div>
                        <div class="stat-label">Perpustakaan Melapor</div>
                    </div>
                </div>
                <div class="stat-card" id="sc-tercetak">
                    <div class="stat-icon stat-icon--tercetak"><i class="fa fa-book"></i></div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-tercetak"><span class="stat-spinner"><i class="fa fa-spinner fa-spin"></i></span></div>
                        <div class="stat-label">Total Judul Tercetak</div>
                    </div>
                </div>
                <div class="stat-card" id="sc-digital">
                    <div class="stat-icon stat-icon--digital"><i class="fa fa-tablet"></i></div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-digital"><span class="stat-spinner"><i class="fa fa-spinner fa-spin"></i></span></div>
                        <div class="stat-label">Total Judul Digital</div>
                    </div>
                </div>
                <div class="stat-card" id="sc-anggota">
                    <div class="stat-icon stat-icon--anggota"><i class="fa fa-users"></i></div>
                    <div class="stat-content">
                        <div class="stat-value" id="stat-anggota"><span class="stat-spinner"><i class="fa fa-spinner fa-spin"></i></span></div>
                        <div class="stat-label">Total Anggota 2025</div>
                    </div>
                </div>
            </div>

            <!-- ═══════ STATISTIK VISUAL ═══════ -->
            <div class="section-header with-actions" id="chart-section-header">
                <span><i class="fa fa-bar-chart"></i> STATISTIK VISUAL <span id="chart-year-badge" class="year-badge"></span></span>
                <button type="button" class="btn-toggle-filter" onclick="toggleChartSection()">
                    <i class="fa fa-angle-up" id="chart-toggle-icon"></i> Sembunyikan Grafik
                </button>
            </div>

            <div id="chart-section">
                <!-- Loading -->
                <div id="chart-loading" class="chart-loading">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Memuat grafik...</p>
                </div>

                <!-- Empty state -->
                <div id="chart-empty" class="chart-empty" style="display:none;">
                    <i class="fa fa-bar-chart fa-3x"></i>
                    <p>Tidak ada data untuk ditampilkan.</p>
                </div>

                <!-- Row 1: Koleksi saat ini (full width) -->
                <div id="chart-row-1" class="chart-row" style="display:none;">
                    <div class="chart-card chart-card--full">
                        <div class="chart-card__header">
                            <div class="chart-card__title"><i class="fa fa-database"></i> Koleksi Saat Ini</div>
                            <div class="chart-card__desc">Jumlah total judul &amp; eksemplar yang dimiliki seluruh perpustakaan pelapor</div>
                        </div>
                        <div class="chart-card__body" style="height:220px;">
                            <canvas id="chartKoleksi"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Pertumbuhan + Distribusi Jenis -->
                <div id="chart-row-2" class="chart-row chart-row--half" style="display:none;">
                    <div class="chart-card">
                        <div class="chart-card__header">
                            <div class="chart-card__title"><i class="fa fa-line-chart"></i> Pertumbuhan Koleksi</div>
                            <div class="chart-card__desc">Penambahan koleksi tahun 2025 vs 2026</div>
                        </div>
                        <div class="chart-card__body" style="height:280px;">
                            <canvas id="chartPertumbuhan"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-card__header">
                            <div class="chart-card__title"><i class="fa fa-pie-chart"></i> Distribusi Jenis Perpustakaan</div>
                            <div class="chart-card__desc">Komposisi jenis perpustakaan yang melapor</div>
                        </div>
                        <div class="chart-card__body chart-card__body--donut" style="height:280px;">
                            <canvas id="chartJenis"></canvas>
                            <div id="donut-legend" class="donut-legend"></div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Pengunjung + Pemanfaatan -->
                <div id="chart-row-3" class="chart-row chart-row--half" style="display:none;">
                    <div class="chart-card">
                        <div class="chart-card__header">
                            <div class="chart-card__title"><i class="fa fa-users"></i> Pengunjung &amp; Akses Teknologi</div>
                            <div class="chart-card__desc">Onsite, online, dan akses teknologi — 2025 vs 2026</div>
                        </div>
                        <div class="chart-card__body" style="height:280px;">
                            <canvas id="chartPengunjung"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-card__header">
                            <div class="chart-card__title"><i class="fa fa-exchange"></i> Pemanfaatan Koleksi Tercetak</div>
                            <div class="chart-card__desc">Dibaca di tempat vs dipinjam (judul) — 2025 vs 2026</div>
                        </div>
                        <div class="chart-card__body" style="height:280px;">
                            <canvas id="chartPemanfaatan"></canvas>
                        </div>
                    </div>
                </div>

            </div><!-- /chart-section -->

            <!-- ═══════ TABEL DATA ═══════ -->
            <div id="loading-indicator" style="display:none; text-align:center; padding:20px; color:#234885;">
                <i class="fa fa-spinner fa-spin fa-2x"></i><p>Memuat data...</p>
            </div>

            <div class="section-header with-actions">
                <span><i class="fa fa-table"></i> DATA PENDATAAN (<span id="total-data">0</span> Data)</span>
                <div class="header-actions">
                    <button type="button" class="btn-header-action" onclick="selectAllVisible()" title="Pilih Semua"><i class="fa fa-check-square-o"></i></button>
                    <button type="button" class="btn-header-action" onclick="deselectAll()"       title="Batal Pilih"><i class="fa fa-square-o"></i></button>
                    <button type="button" class="btn-header-action" onclick="refreshData()"       title="Refresh"><i class="fa fa-refresh"></i></button>
                </div>
            </div>

            <div id="bulk-actions" class="bulk-actions" style="display:none;">
                <span class="bulk-actions-label"><i class="fa fa-check-square"></i> <span id="selected-count">0</span> data dipilih</span>
                <button type="button" class="btn-bulk-action" onclick="bulkExport()"><i class="fa fa-download"></i> Export Terpilih</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped rekap-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;width:40px;"><input type="checkbox" id="select-all" onchange="selectAllVisible()"></th>
                            <th style="text-align:center;width:50px;">No</th>
                            <th style="text-align:center;width:200px;">Nomor Pokok</th>
                            <th style="text-align:center;width:100px;">Tahun Data</th>
                            <th style="text-align:center;width:120px;">Jumlah Jawaban</th>
                            <th style="text-align:center;width:180px;">Tanggal Input</th>
                            <th style="text-align:center;width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <tr><td colspan="7" class="text-center"><em>Memuat data...</em></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container" id="pagination-container"></div>

            <div class="action-buttons">
                <a href="#" onclick="exportData(); return false;" class="btn-export"><i class="fa fa-file-excel-o"></i> Export ke Excel</a>
                <a href="<?php echo site_url('pendataan'); ?>" class="btn-new"><i class="fa fa-plus"></i> Input Data Baru</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<script type="text/javascript">
// ─────────────────────────────────────────────────────────────
// State
// ─────────────────────────────────────────────────────────────
var currentPage   = 1;
var totalPages    = 1;
var perPage       = 10;
var selectedItems = new Set();
var currentFilters = {
    tahun:       document.getElementById('filter-tahun') ? document.getElementById('filter-tahun').value : '<?php echo date("Y"); ?>',
    nomor_pokok: '',
    date_from:   '',
    date_to:     '',
    sort_by:     'tanggal_desc'
};

// Chart instances
var charts = { koleksi: null, pertumbuhan: null, jenis: null, pengunjung: null, pemanfaatan: null };

// ─────────────────────────────────────────────────────────────
// Colour palette — consistent across all charts
// ─────────────────────────────────────────────────────────────
var C = {
    blue:        '#234885',
    blueLight:   'rgba(35,72,133,0.75)',
    blueFade:    'rgba(35,72,133,0.15)',
    teal:        '#17a2b8',
    tealLight:   'rgba(23,162,184,0.75)',
    tealFade:    'rgba(23,162,184,0.15)',
    green:       '#28a745',
    greenLight:  'rgba(40,167,69,0.75)',
    orange:      '#fd7e14',
    orangeLight: 'rgba(253,126,20,0.75)',
    purple:      '#6f42c1',
    purpleLight: 'rgba(111,66,193,0.75)',
    grid:        'rgba(0,0,0,0.06)',
    text:        '#444',
};

// Donut palette for jenis perpustakaan
var DONUT_COLORS = [C.blue, C.teal, C.orange, C.green, C.purple,
                    '#e83e8c', '#20c997', '#ffc107', '#6c757d', '#343a40'];

// ─────────────────────────────────────────────────────────────
// Shared Chart.js defaults
// ─────────────────────────────────────────────────────────────
Chart.defaults.font.family = "'Segoe UI', sans-serif";
Chart.defaults.font.size   = 12;

function groupedBarOptions(title) {
    return {
        responsive:          true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { boxWidth: 12, padding: 16, color: C.text } },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: C.text } },
            y: { beginAtZero: true, grid: { color: C.grid }, ticks: { color: C.text } }
        }
    };
}

// ─────────────────────────────────────────────────────────────
// formatNumber helper — e.g. 1234567 → "1.234.567"
// ─────────────────────────────────────────────────────────────
function fmt(n) {
    return parseInt(n || 0).toLocaleString('id-ID');
}

// ─────────────────────────────────────────────────────────────
// Toggle chart section
// ─────────────────────────────────────────────────────────────
function toggleChartSection() {
    var s   = document.getElementById('chart-section');
    var btn = document.querySelector('#chart-section-header .btn-toggle-filter');
    var ico = document.getElementById('chart-toggle-icon');
    if (s.style.display === 'none') {
        s.style.display = 'block';
        ico.className   = 'fa fa-angle-up';
        btn.innerHTML   = '<i class="fa fa-angle-up" id="chart-toggle-icon"></i> Sembunyikan Grafik';
    } else {
        s.style.display = 'none';
        ico.className   = 'fa fa-angle-down';
        btn.innerHTML   = '<i class="fa fa-angle-down" id="chart-toggle-icon"></i> Tampilkan Grafik';
    }
}

// ─────────────────────────────────────────────────────────────
// Load chart data
// ─────────────────────────────────────────────────────────────
function loadChartData() {
    // Reset UI
    document.getElementById('chart-loading').style.display  = 'block';
    document.getElementById('chart-empty').style.display    = 'none';
    ['chart-row-1','chart-row-2','chart-row-3'].forEach(function(id) {
        document.getElementById(id).style.display = 'none';
    });

    var url = '<?php echo site_url("pendataan/get_chart_stats"); ?>'
        + '?tahun='       + encodeURIComponent(currentFilters.tahun)
        + '&nomor_pokok=' + encodeURIComponent(currentFilters.nomor_pokok)
        + '&date_from='   + encodeURIComponent(currentFilters.date_from)
        + '&date_to='     + encodeURIComponent(currentFilters.date_to);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(d) {
            document.getElementById('chart-loading').style.display = 'none';

            if (!d.stat_cards || d.stat_cards.total_laporan === 0) {
                document.getElementById('chart-empty').style.display = 'flex';
                updateStatCards({ total_laporan: 0, total_judul_tercetak: 0, total_judul_digital: 0, total_anggota_2025: 0 });
                return;
            }

            // Year badge
            document.getElementById('chart-year-badge').textContent = 'Tahun ' + currentFilters.tahun;

            updateStatCards(d.stat_cards);
            renderKoleksi(d.koleksi_saat_ini);
            renderPertumbuhan(d.pertumbuhan);
            renderJenis(d.distribusi_jenis);
            renderPengunjung(d.pengunjung);
            renderPemanfaatan(d.pemanfaatan);

            document.getElementById('chart-row-1').style.display = 'block';
            document.getElementById('chart-row-2').style.display = 'grid';
            document.getElementById('chart-row-3').style.display = 'grid';
        })
        .catch(function(e) {
            console.error('Chart error:', e);
            document.getElementById('chart-loading').style.display = 'none';
            document.getElementById('chart-empty').style.display   = 'flex';
        });
}

// ─────────────────────────────────────────────────────────────
// Update stat cards
// ─────────────────────────────────────────────────────────────
function updateStatCards(sc) {
    document.getElementById('stat-laporan').textContent  = fmt(sc.total_laporan);
    document.getElementById('stat-tercetak').textContent = fmt(sc.total_judul_tercetak);
    document.getElementById('stat-digital').textContent  = fmt(sc.total_judul_digital);
    document.getElementById('stat-anggota').textContent  = fmt(sc.total_anggota_2025);
}

// ─────────────────────────────────────────────────────────────
// CHART 1: Koleksi saat ini — horizontal bar
// ─────────────────────────────────────────────────────────────
function renderKoleksi(p) {
    if (charts.koleksi) charts.koleksi.destroy();
    var ctx = document.getElementById('chartKoleksi').getContext('2d');
    charts.koleksi = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: p.labels,
            datasets: [{
                label:           'Jumlah',
                data:            p.data,
                backgroundColor: [C.blueLight, C.blue, C.tealLight, C.teal],
                borderColor:     [C.blue,      C.blue, C.teal,      C.teal],
                borderWidth:     1,
                borderRadius:    5,
            }]
        },
        options: {
            indexAxis:           'y',
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: function(ctx) { return ' ' + fmt(ctx.parsed.x); } }
                }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: C.grid }, ticks: { color: C.text, callback: function(v) { return fmt(v); } } },
                y: { grid: { display: false }, ticks: { color: C.text, font: { weight: '600' } } }
            }
        }
    });
}

// ─────────────────────────────────────────────────────────────
// CHART 2: Pertumbuhan koleksi — grouped bar 2025 vs 2026
// ─────────────────────────────────────────────────────────────
function renderPertumbuhan(p) {
    if (charts.pertumbuhan) charts.pertumbuhan.destroy();
    var ctx = document.getElementById('chartPertumbuhan').getContext('2d');
    charts.pertumbuhan = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: p.labels,
            datasets: [
                { label: '2025', data: p.data_2025, backgroundColor: C.blueLight, borderColor: C.blue, borderWidth: 1, borderRadius: 4 },
                { label: '2026', data: p.data_2026, backgroundColor: C.tealLight, borderColor: C.teal, borderWidth: 1, borderRadius: 4 }
            ]
        },
        options: groupedBarOptions()
    });
}

// ─────────────────────────────────────────────────────────────
// CHART 3: Distribusi jenis perpustakaan — donut
// ─────────────────────────────────────────────────────────────
function renderJenis(p) {
    if (charts.jenis) charts.jenis.destroy();

    if (!p.labels || p.labels.length === 0) {
        document.getElementById('chartJenis').closest('.chart-card').style.display = 'none';
        return;
    }

    var ctx    = document.getElementById('chartJenis').getContext('2d');
    var colors = p.labels.map(function(_, i) { return DONUT_COLORS[i % DONUT_COLORS.length]; });
    var total  = p.data.reduce(function(a, b) { return a + b; }, 0);

    charts.jenis = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels:   p.labels,
            datasets: [{
                data:            p.data,
                backgroundColor: colors,
                borderColor:     '#fff',
                borderWidth:     3,
                hoverOffset:     6,
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            cutout:              '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var pct = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // Custom legend
    var legendEl = document.getElementById('donut-legend');
    legendEl.innerHTML = '';
    p.labels.forEach(function(label, i) {
        var pct  = total > 0 ? Math.round(p.data[i] / total * 100) : 0;
        var item = document.createElement('div');
        item.className = 'legend-item';
        item.innerHTML =
            '<span class="legend-dot" style="background:' + colors[i] + '"></span>' +
            '<span class="legend-label">' + label + '</span>' +
            '<span class="legend-val">' + p.data[i] + ' <small>(' + pct + '%)</small></span>';
        legendEl.appendChild(item);
    });
}

// ─────────────────────────────────────────────────────────────
// CHART 4: Pengunjung & Akses — grouped bar
// ─────────────────────────────────────────────────────────────
function renderPengunjung(p) {
    if (charts.pengunjung) charts.pengunjung.destroy();
    var ctx = document.getElementById('chartPengunjung').getContext('2d');
    charts.pengunjung = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: p.labels,
            datasets: [
                { label: '2025', data: p.data_2025, backgroundColor: C.blueLight,  borderColor: C.blue,  borderWidth: 1, borderRadius: 4 },
                { label: '2026', data: p.data_2026, backgroundColor: C.greenLight, borderColor: C.green, borderWidth: 1, borderRadius: 4 }
            ]
        },
        options: groupedBarOptions()
    });
}

// ─────────────────────────────────────────────────────────────
// CHART 5: Pemanfaatan koleksi tercetak — grouped bar
// ─────────────────────────────────────────────────────────────
function renderPemanfaatan(p) {
    if (charts.pemanfaatan) charts.pemanfaatan.destroy();
    var ctx = document.getElementById('chartPemanfaatan').getContext('2d');
    charts.pemanfaatan = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: p.labels,
            datasets: [
                { label: '2025', data: p.data_2025, backgroundColor: C.orangeLight, borderColor: C.orange, borderWidth: 1, borderRadius: 4 },
                { label: '2026', data: p.data_2026, backgroundColor: C.purpleLight, borderColor: C.purple, borderWidth: 1, borderRadius: 4 }
            ]
        },
        options: groupedBarOptions()
    });
}

// ─────────────────────────────────────────────────────────────
// Table / filter / pagination (tidak berubah dari versi lama)
// ─────────────────────────────────────────────────────────────
function formatDate(ds) {
    var d = new Date(ds);
    return [String(d.getDate()).padStart(2,'0'), String(d.getMonth()+1).padStart(2,'0'), d.getFullYear()].join('/')
        + ' ' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
}

function toggleAdvancedFilter() {
    var af  = document.getElementById('advanced-filter');
    var btn = event.target.closest('.btn-toggle-filter');
    var ico = document.getElementById('filter-icon');
    if (af.style.display === 'none') {
        af.style.display = 'block'; ico.className = 'fa fa-angle-up';
        btn.innerHTML = '<i class="fa fa-angle-up" id="filter-icon"></i> Sembunyikan Filter Lanjutan';
    } else {
        af.style.display = 'none'; ico.className = 'fa fa-angle-down';
        btn.innerHTML = '<i class="fa fa-angle-down" id="filter-icon"></i> Tampilkan Filter Lanjutan';
    }
}

function changePerPage() { perPage = parseInt(document.getElementById('per-page').value); loadData(1); }

function loadData(page) {
    page = page || 1; currentPage = page;
    document.getElementById('loading-indicator').style.display = 'block';
    document.getElementById('table-body').innerHTML = '<tr><td colspan="7" class="text-center"><em>Memuat data...</em></td></tr>';
    var url = '<?php echo site_url("pendataan/get_rekap_data"); ?>'
        + '?page=' + page + '&per_page=' + perPage
        + '&tahun='       + encodeURIComponent(currentFilters.tahun)
        + '&nomor_pokok=' + encodeURIComponent(currentFilters.nomor_pokok)
        + '&date_from='   + encodeURIComponent(currentFilters.date_from)
        + '&date_to='     + encodeURIComponent(currentFilters.date_to)
        + '&sort_by='     + encodeURIComponent(currentFilters.sort_by);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(result) {
            displayData(result); createPagination(result);
            document.getElementById('loading-indicator').style.display = 'none';
        })
        .catch(function(err) {
            console.error('loadData error:', err);
            document.getElementById('table-body').innerHTML = '<tr><td colspan="7" class="text-center text-danger"><em>Gagal memuat data.</em></td></tr>';
            document.getElementById('loading-indicator').style.display = 'none';
        });
}

function displayData(result) {
    var tb = document.getElementById('table-body');
    tb.innerHTML = '';
    if (!result.data || result.data.length === 0) {
        tb.innerHTML = '<tr><td colspan="7" class="text-center"><em>Tidak ada data</em></td></tr>';
        document.getElementById('total-data').textContent = '0';
        return;
    }
    var start = (result.page - 1) * result.per_page;
    result.data.forEach(function(row, i) {
        var tr = document.createElement('tr');
        var sel = selectedItems.has(row.id_koleksi);
        if (sel) tr.className = 'selected-row';
        tr.innerHTML =
            '<td class="text-center"><input type="checkbox" class="row-checkbox" value="' + row.id_koleksi + '" ' + (sel ? 'checked' : '') + ' onchange="toggleRowSelection(this)"></td>' +
            '<td class="text-center">' + (start + i + 1) + '.</td>' +
            '<td><strong>' + (row.nomor_pokok || '-') + '</strong></td>' +
            '<td class="text-center">' + (row.tahun_data || '-') + '</td>' +
            '<td class="text-center"><span class="badge badge-info">' + (row.jumlah_jawaban || '0') + '</span></td>' +
            '<td>' + formatDate(row.tanggal_submit) + '</td>' +
            '<td class="text-center"><a href="<?php echo site_url("pendataan/detail/"); ?>' + row.id_koleksi + '" class="btn-action btn-detail"><i class="fa fa-eye"></i> Detail</a></td>';
        tb.appendChild(tr);
    });
    document.getElementById('total-data').textContent = result.total || 0;
    totalPages = result.total_pages || 1;
    updateSelectAllCheckbox();
}

function toggleRowSelection(cb) {
    var id = parseInt(cb.value), row = cb.closest('tr');
    if (cb.checked) { selectedItems.add(id); row.classList.add('selected-row'); }
    else            { selectedItems.delete(id); row.classList.remove('selected-row'); }
    updateBulkActions(); updateSelectAllCheckbox();
}

function updateSelectAllCheckbox() {
    var sa = document.getElementById('select-all');
    var cbs = document.querySelectorAll('.row-checkbox');
    var cc  = document.querySelectorAll('.row-checkbox:checked').length;
    sa.checked       = cbs.length > 0 && cc === cbs.length;
    sa.indeterminate = cc > 0 && cc < cbs.length;
}

function selectAllVisible() {
    document.querySelectorAll('.row-checkbox').forEach(function(cb) { cb.checked = true; toggleRowSelection(cb); });
    document.getElementById('select-all').checked = true;
}

function deselectAll() {
    selectedItems.clear();
    document.querySelectorAll('.row-checkbox').forEach(function(cb) { cb.checked = false; cb.closest('tr').classList.remove('selected-row'); });
    document.getElementById('select-all').checked = false;
    updateBulkActions();
}

function updateBulkActions() {
    var ba = document.getElementById('bulk-actions');
    if (selectedItems.size > 0) { ba.style.display = 'flex'; document.getElementById('selected-count').textContent = selectedItems.size; }
    else                        { ba.style.display = 'none'; }
}

function refreshData() { loadData(currentPage); loadChartData(); showNotification('Data berhasil di-refresh', 'success'); }

function createPagination(result) {
    var c = document.getElementById('pagination-container');
    c.innerHTML = '';
    if (result.total_pages <= 1) return;
    var ul = document.createElement('ul'); ul.className = 'pagination';
    var cur = result.page, tot = result.total_pages;
    var prev = document.createElement('li');
    if (cur === 1) prev.className = 'disabled';
    prev.innerHTML = '<a href="#" onclick="changePage(' + (cur-1) + '); return false;">&laquo; Prev</a>';
    ul.appendChild(prev);
    var s = Math.max(1, cur-2), e = Math.min(tot, cur+2);
    if (s > 1) { var fl = document.createElement('li'); fl.innerHTML = '<a href="#" onclick="changePage(1); return false;">1</a>'; ul.appendChild(fl); if (s > 2) { var dl = document.createElement('li'); dl.className = 'disabled'; dl.innerHTML = '<span>...</span>'; ul.appendChild(dl); } }
    for (var i = s; i <= e; i++) { var li = document.createElement('li'); if (i === cur) li.className = 'active'; li.innerHTML = i === cur ? '<strong>' + i + '</strong>' : '<a href="#" onclick="changePage(' + i + '); return false;">' + i + '</a>'; ul.appendChild(li); }
    if (e < tot) { if (e < tot-1) { var dl2 = document.createElement('li'); dl2.className = 'disabled'; dl2.innerHTML = '<span>...</span>'; ul.appendChild(dl2); } var ll = document.createElement('li'); ll.innerHTML = '<a href="#" onclick="changePage(' + tot + '); return false;">' + tot + '</a>'; ul.appendChild(ll); }
    var next = document.createElement('li');
    if (cur === tot) next.className = 'disabled';
    next.innerHTML = '<a href="#" onclick="changePage(' + (cur+1) + '); return false;">Next &raquo;</a>';
    ul.appendChild(next);
    c.appendChild(ul);
}

function changePage(p) {
    if (p < 1 || p > totalPages) return;
    loadData(p);
    document.querySelector('.rekap-table').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function applyFilter() {
    currentFilters.tahun       = document.getElementById('filter-tahun').value;
    currentFilters.nomor_pokok = document.getElementById('filter-nomor').value;
    currentFilters.date_from   = document.getElementById('filter-date-from').value;
    currentFilters.date_to     = document.getElementById('filter-date-to').value;
    currentFilters.sort_by     = document.getElementById('sort-by').value;
    loadData(1); loadChartData();
    showNotification('Filter berhasil diterapkan', 'success');
}

function resetFilter() {
    document.getElementById('filter-tahun').selectedIndex = 0;
    document.getElementById('filter-nomor').value         = '';
    document.getElementById('filter-date-from').value     = '';
    document.getElementById('filter-date-to').value       = '';
    document.getElementById('sort-by').value              = 'tanggal_desc';
    document.getElementById('per-page').value             = '10';
    perPage = 10;
    currentFilters = { tahun: document.getElementById('filter-tahun').value, nomor_pokok: '', date_from: '', date_to: '', sort_by: 'tanggal_desc' };
    loadData(1); loadChartData();
    showNotification('Filter berhasil direset', 'info');
}

function exportData() {
    window.location.href = '<?php echo site_url("pendataan/export_excel"); ?>'
        + '?tahun='       + encodeURIComponent(currentFilters.tahun)
        + '&nomor_pokok=' + encodeURIComponent(currentFilters.nomor_pokok)
        + '&date_from='   + encodeURIComponent(currentFilters.date_from)
        + '&date_to='     + encodeURIComponent(currentFilters.date_to)
        + '&sort_by='     + encodeURIComponent(currentFilters.sort_by);
    showNotification('Memulai export data...', 'info');
}

function bulkExport() {
    if (selectedItems.size === 0) { showNotification('Tidak ada data yang dipilih', 'warning'); return; }
    var form = document.createElement('form');
    form.method = 'POST'; form.action = '<?php echo site_url("pendataan/export_excel"); ?>';
    selectedItems.forEach(function(id) { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'selected_ids[]'; inp.value = id; form.appendChild(inp); });
    document.body.appendChild(form); form.submit(); document.body.removeChild(form);
    showNotification('Memulai export ' + selectedItems.size + ' data terpilih...', 'info');
}

function showNotification(msg, type) {
    var n = document.createElement('div');
    var icons = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle', info: 'info-circle' };
    n.className = 'notification notification-' + type;
    n.innerHTML = '<i class="fa fa-' + (icons[type] || 'info-circle') + '"></i> ' + msg;
    document.body.appendChild(n);
    setTimeout(function() { n.classList.add('show'); }, 10);
    setTimeout(function() { n.classList.remove('show'); setTimeout(function() { n.remove(); }, 300); }, 3000);
}

// ─────────────────────────────────────────────────────────────
// Init
// ─────────────────────────────────────────────────────────────
window.onload = function() {
    loadData(1);
    loadChartData();
    document.getElementById('filter-nomor').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyFilter();
    });
};
</script>

<style>
/* ── Existing base styles ─────────────────────────────────── */
.survey-container{max-width:1200px;margin:40px auto;padding:20px}
.section-header{background:#234885;color:white;padding:15px 20px;margin-top:30px;margin-bottom:20px;border-radius:5px;font-weight:bold;font-size:18px;display:flex;justify-content:flex-start;align-items:center;flex-wrap:wrap;gap:10px}
.section-header:first-of-type{margin-top:0}
.section-header.with-actions{justify-content:space-between}
.section-header>span{display:flex;align-items:center;gap:8px}
.btn-toggle-filter{background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);color:white;padding:6px 12px;border-radius:4px;cursor:pointer;font-size:14px;transition:all 0.3s}
.btn-toggle-filter:hover{background:rgba(255,255,255,0.3)}
.header-actions{display:flex;gap:5px}
.btn-header-action{background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);color:white;padding:6px 10px;border-radius:4px;cursor:pointer;transition:all 0.3s}
.btn-header-action:hover{background:rgba(255,255,255,0.3)}
.question-item{background:#f0f0f0;padding:20px;margin-bottom:20px;border-radius:5px;border-left:4px solid #234885}
.filter-row{display:flex;flex-wrap:wrap;align-items:flex-end;margin:0 -10px}
.filter-col{padding:0 10px;margin-bottom:0}
.filter-buttons{display:flex;flex-direction:column}
.button-group{display:flex;gap:8px}
.filter-divider{margin:20px 0;border:0;border-top:1px solid #ccc}
.question-label{font-weight:600;color:#333;margin-bottom:8px;line-height:1.4;display:block;font-size:14px;white-space:nowrap}
.form-control{width:100%;padding:7px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;height:36px}
.form-control:focus{outline:none;border-color:#234885;box-shadow:0 0 0 2px rgba(35,72,133,0.1)}
input[type="date"].form-control{padding:6px 12px}
.btn-filter,.btn-reset,.btn-export,.btn-new{padding:8px 16px;font-size:14px;border:none;border-radius:4px;cursor:pointer;text-decoration:none;display:inline-block;transition:all 0.3s;height:36px;line-height:20px}
.btn-filter{background:#234885;color:white}.btn-filter:hover{background:#1a3764;transform:translateY(-1px)}
.btn-reset{background:#6c757d;color:white;margin-left:5px}.btn-reset:hover{background:#5a6268;transform:translateY(-1px)}
.btn-export{background:#28a745;color:white}.btn-export:hover{background:#218838;transform:translateY(-1px)}
.btn-new{background:#234885;color:white;margin-left:10px}.btn-new:hover{background:#1a3764;transform:translateY(-1px)}
.bulk-actions{background:#fff3cd;border:1px solid #ffc107;border-radius:5px;padding:12px 20px;margin-bottom:15px;display:none;align-items:center;gap:15px}
.bulk-actions-label{color:#856404;font-weight:600;flex:1}
.btn-bulk-action{background:#234885;color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;font-size:14px;transition:all 0.3s}
.btn-bulk-action:hover{transform:translateY(-1px);box-shadow:0 2px 5px rgba(0,0,0,0.2)}
.table-responsive{overflow-x:auto}
.rekap-table{width:100%;background:white;border-collapse:collapse}
.rekap-table thead{background:#234885;color:white}
.rekap-table th{padding:12px;text-align:left;font-weight:600}
.rekap-table td{padding:12px;vertical-align:middle;border-bottom:1px solid #dee2e6}
.rekap-table tbody tr:hover{background:#f5f5f5}
.rekap-table tbody tr.selected-row{background:#e3f2fd}
.badge{display:inline-block;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600}
.badge-info{background:#17a2b8;color:white}
.btn-action{display:inline-block;padding:6px 10px;margin:0 2px;border-radius:3px;text-decoration:none;color:white;font-size:14px;transition:all 0.3s}
.btn-action:hover{transform:translateY(-1px);box-shadow:0 2px 5px rgba(0,0,0,0.2)}
.btn-detail{background:#17a2b8}.btn-detail:hover{background:#138496}
.alert{padding:15px;margin-bottom:20px;border-radius:5px;position:relative}
.alert-dismissible{padding-right:35px}
.alert .close{position:absolute;right:10px;top:10px;background:none;border:none;font-size:20px;cursor:pointer;opacity:0.5}
.alert .close:hover{opacity:1}
.alert-danger{background:#f8d7da;border:1px solid #f5c6cb;color:#721c24}
.alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724}
.survey-intro{background:#f0f0f0;padding:20px;border-radius:5px;margin-bottom:30px}
.survey-intro h4{color:#234885;margin-bottom:10px}
.survey-intro p{color:#666;line-height:1.6;margin:0}
.action-buttons{text-align:right;margin:20px 0}
.text-center{text-align:center}
.text-danger{color:#dc3545}
.pagination-container{margin:20px 0;text-align:center}
.pagination{display:inline-block;padding:0;margin:0;list-style:none}
.pagination li{display:inline-block;margin:0 2px;vertical-align:middle}
.pagination li a,.pagination li strong,.pagination li span{display:inline-block;padding:8px 14px;text-decoration:none;background:#f0f0f0;color:#234885;border:1px solid #ddd;border-radius:4px;transition:all 0.3s;min-width:40px;text-align:center}
.pagination li a:hover{background:#234885;color:white;border-color:#234885;transform:translateY(-1px)}
.pagination li.active strong{background:#234885;color:white;border-color:#234885;font-weight:600}
.pagination li.disabled a,.pagination li.disabled span{color:#999;cursor:not-allowed;background:#f9f9f9}
.notification{position:fixed;top:20px;right:-400px;background:white;padding:15px 20px;border-radius:5px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;min-width:300px;transition:right 0.3s ease}
.notification.show{right:20px}
.notification-success{border-left:4px solid #28a745;color:#155724}
.notification-error{border-left:4px solid #dc3545;color:#721c24}
.notification-warning{border-left:4px solid #ffc107;color:#856404}
.notification-info{border-left:4px solid #17a2b8;color:#0c5460}
.notification i{margin-right:10px}

/* ── Stat cards ───────────────────────────────────────────── */
.stats-container{display:flex;gap:16px;margin-bottom:30px;flex-wrap:wrap}
.stat-card{flex:1;min-width:200px;background:white;border-radius:8px;padding:18px 20px;display:flex;align-items:center;box-shadow:0 2px 6px rgba(0,0,0,0.08);border-left:4px solid #234885;transition:box-shadow 0.2s}
.stat-card:hover{box-shadow:0 4px 14px rgba(0,0,0,0.12)}
.stat-icon{font-size:32px;color:#234885;margin-right:16px;width:44px;text-align:center;flex-shrink:0}
.stat-icon--tercetak{color:#234885}
.stat-icon--digital{color:#17a2b8}
.stat-icon--anggota{color:#28a745}
.stat-content{flex:1;min-width:0}
.stat-value{font-size:26px;font-weight:700;color:#234885;line-height:1.2;margin-bottom:4px;word-break:break-word}
.stat-label{font-size:13px;color:#777;line-height:1.3}
.stat-spinner{font-size:18px;color:#aaa}

/* ── Year badge ───────────────────────────────────────────── */
.year-badge{background:rgba(255,255,255,0.22);border:1px solid rgba(255,255,255,0.35);color:white;font-size:12px;font-weight:500;padding:2px 10px;border-radius:20px;margin-left:4px}

/* ── Chart section ────────────────────────────────────────── */
.chart-loading{text-align:center;padding:48px 20px;color:#234885;background:white;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:24px}
.chart-loading i{display:block;margin-bottom:12px}
.chart-loading p{margin:0;color:#888}
.chart-empty{text-align:center;padding:48px 20px;color:#bbb;background:white;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:24px;display:none;flex-direction:column;align-items:center;gap:12px}
.chart-empty p{margin:0;font-size:15px}

.chart-row{margin-bottom:20px}
.chart-row--half{display:grid;grid-template-columns:1fr 1fr;gap:20px}

.chart-card{background:white;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-top:3px solid #234885;display:flex;flex-direction:column;overflow:hidden}
.chart-card--full{width:100%}
.chart-card__header{padding:14px 20px 10px;border-bottom:1px solid #f3f3f3}
.chart-card__title{font-weight:700;color:#234885;font-size:14px;display:flex;align-items:center;gap:7px;margin-bottom:4px}
.chart-card__desc{font-size:12px;color:#888;line-height:1.4}
.chart-card__body{padding:16px 20px 18px;flex:1;position:relative}
.chart-card__body--donut{display:flex;gap:20px;align-items:center}
.chart-card__body--donut {
    min-height: 180px;
}
.chart-card__body--donut canvas {
    flex-shrink: 0;
    width: 160px !important;
    min-width: 160px;
    max-width: 160px;
}

/* Donut legend */
.donut-legend{flex:1;min-width:0;display:flex;flex-direction:column;gap:7px;max-height:200px;overflow-y:auto}
.legend-item{display:flex;align-items:center;gap:8px;font-size:13px}
.legend-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.legend-label{flex:1;color:#444;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.legend-val{color:#234885;font-weight:600;white-space:nowrap}
.legend-val small{color:#888;font-weight:400}

/* ── Responsive ───────────────────────────────────────────── */
@media(max-width:900px){
    .chart-row--half{grid-template-columns:1fr}
}
@media(max-width:768px){
    .stats-container{flex-direction:column}
    .stat-card{min-width:100%}
    .section-header{flex-direction:column;align-items:flex-start;gap:10px}
    .header-actions{width:100%;justify-content:flex-start}
    .btn-toggle-filter{width:100%;text-align:left}
    .filter-row{margin:0}
    .filter-col{width:100%;padding:0;margin-bottom:15px}
    .button-group{width:100%}
    .btn-filter,.btn-reset{flex:1}
    .action-buttons{text-align:center}
    .action-buttons a{display:block;margin:5px 0}
    .bulk-actions{flex-direction:column;align-items:stretch}
    .btn-bulk-action{width:100%;margin:5px 0}
    .chart-card__body--donut{flex-direction:column;align-items:flex-start}
    .chart-card__body--donut canvas{width:140px !important;height:140px !important}
}
@media(max-width:576px){
    .survey-container{padding:10px}
    .question-item{padding:15px}
    .stat-value{font-size:22px}
    .stat-icon{font-size:26px;width:38px}
}
</style>