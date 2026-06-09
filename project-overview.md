# Detailed Technical Overview: profile.perpus.jatengprov.go.id

## 1. Scope and Purpose
This project is a CodeIgniter 3 MVC web application for a provincial library profile website. Its main function is not only to display public content such as news, albums, videos, and static pages, but also to operate a database-driven survey and reporting module called Pendataan. In practice, the system combines three major technical concerns: CMS content management, user authentication and authorization, and survey data collection with statistical reporting.

## 2. Technology Stack and Runtime Environment
The application is built with:
- CodeIgniter 3.1.13 as the PHP MVC framework (confirmed from composer.json)
- MySQL as the persistence layer, accessed through the CI database library and the mysqli driver
- PHP runtime compatible with CodeIgniter 3; the local CLI environment verified in this workspace is PHP 8.1.25
- Apache/XAMPP as the local web server environment
- HTML, CSS, JavaScript, Bootstrap, jQuery, DataTables, and Chart.js for frontend rendering and reporting visuals
- File-based cache for performance optimization and rate limiting

The application starts from the CodeIgniter entry point in index.php and uses the route configuration in application/config/routes.php, where default_controller is set to main and maps to the Main controller class. This means the homepage is the entry point for normal public access.

## 3. System Architecture
The project follows the standard CodeIgniter MVC pattern:

- Controllers:
  - Main.php: homepage and public landing page
  - Berita.php, Albums.php, Playlist.php, Hubungi.php, Download.php, Polling.php: public content and interaction pages
  - Administrator.php: main admin panel for CRUD, authentication, permission management, and module administration
  - Pendataan.php: survey form, validation, storage, reporting, export, and chart statistics

- Models:
  - Model_app.php: generic DB helpers used for insert, update, delete, join, and permission checks
  - Model_utama.php: content-oriented query helpers used heavily by the public pages
  - Model_menu.php: menu generation logic

- Helpers:
  - engine_helper.php: permission validation, template selection, metadata retrieval, and session authorization helpers

- Views:
  - application/views/dinas-1/: public theme views
  - application/views/administrator/: admin UI and forms

This means the system is a server-rendered PHP application, not an API-first or SPA-based system. The browser requests a URL, the controller loads the required model/helper logic, the model queries MySQL, and the view renders the final HTML or JSON response.

## 4. Interaction Between Code Components
The interaction pattern is consistent throughout the system:

1. The browser sends a request to a controller.
2. The controller loads helper and library dependencies using CodeIgniter’s loader.
3. The controller calls model methods to query or update the database.
4. The controller prepares the data and passes it to a view.
5. The view renders the output for the user or returns JSON for AJAX usage.

For example:
- Main.php uses Model_utama to gather homepage content such as agenda, announcements, latest news, videos, and polling data.
- Administrator.php uses Model_app and helper functions to validate module access before allowing CRUD operations.
- Pendataan.php uses form_validation, cache, session, and database transactions to manage survey submission and reporting.

This architecture makes the project easy to extend for content management, but it also means the system behavior depends heavily on the correctness of the database schema and controller logic.

## 5. Main Functional Modules

### 5.1 Public Content CMS
The public side of the application is built around database-driven content pages:
- News/articles
- Categories and tags
- Photo albums and galleries
- Video playlists and embedded media
- Static pages
- Downloads
- Polling
- Contact form handling
- Search and content listing pages

The homepage and content pages are assembled using Model_utama methods such as:
- view_where_ordering_limit()
- view_join()
- view_join_one()
- view_join_two()

These methods allow the controllers to combine several tables into one page result, such as news joined with categories and authors.

### 5.2 Admin Management System
The admin section is centralized in Administrator.php. It contains CRUD flows for:
- News and categories
- Album/gallery management
- Videos and playlists
- Banners, templates, menus, pages, and downloads
- Polls and comments
- User accounts and module permissions
- Survey configuration and survey question management

A key technical characteristic is that admin access is controlled not only by the user’s level, but also by the users_modul table. The helper function cek_session_akses($link, $id) checks whether a specific module is authorized for the current session. This is the main enforcement mechanism behind feature-based access control.

### 5.3 Survey Module (Pendataan)
The Pendataan module is the most technically advanced part of the system.

Its process flow is:
1. The controller checks the status of the survey from config_pendataan.
2. If the survey is active, it loads active questions from pertanyaan_koleksi and options from opsi_pertanyaan_koleksi.
3. The user fills the form.
4. The controller validates each question dynamically based on:
   - required / optional
   - text / number / textarea / radio / checkbox / select
   - min_value / max_value
5. The system performs security checks:
   - honeypot
   - session/IP rate limit
   - double-submit prevention
   - input sanitization
6. The submission is stored in a transaction:
   - identitas_input_koleksi stores the submission metadata
   - jawaban_koleksi stores each answer

This design makes the survey module robust against invalid input and repeated submissions.

## 6. Data Model and Storage Logic
The core survey tables are:
- identitas_input_koleksi: contains submission metadata and the year of submission
- jawaban_koleksi: stores each answer for each submission
- pertanyaan_koleksi: defines the survey question structure
- opsi_pertanyaan_koleksi: stores answer options for multiple-choice questions
- config_pendataan: stores active survey status, year, and closed-message text

The survey module uses a database transaction so that both the main submission record and all answer rows are stored consistently as one unit. The data integrity model is stronger for the survey module than for many CMS tables, where most relationships are implemented by query joins and application logic rather than strict foreign key enforcement.

## 7. Reporting and Visualization
The reporting page is implemented through:
- AJAX endpoint: Pendataan.php -> get_chart_stats()
- JSON response: aggregated statistics and chart-ready data
- Chart.js on the frontend: visual rendering of charts
- File cache: to reduce repeated computation

The chart endpoint returns five categories of data:
1. total report count
2. total printed titles
3. total digital titles
4. total members in 2025
5. visual analytics for collection growth, access, utilization, and library type distribution

This gives the application a dashboard-like reporting system without using a separate BI tool or API service.

## 8. Export Functionality
The export function in Pendataan.php supports:
- full filtered export
- selected-record export
- chunked CSV generation for large datasets
- UTF-8 BOM for Excel compatibility
- export rate limiting for abuse prevention

This is technically important because the system is designed not only to collect survey results, but also to produce downloadable reports for administrative use.

## 9. Security and Reliability Aspects
The application includes several technical safeguards:
- session-based authentication
- password verification using PHP password hashing
- CAPTCHA in the admin login flow
- honeypot fields for anti-spam protection
- session/IP-based rate limiting
- double-submit detection
- input sanitization and tag stripping
- transaction-based survey storage
- file-based cache to reduce repetitive expensive operations

These mechanisms show that the project is not just a simple CMS; it contains real operational safeguards for survey integrity and administrative security.

## 10. Technical Summary
The project can be summarized as a traditional CodeIgniter 3 PHP web application with:
- a database-driven CMS,
- an admin authorization system based on module permissions,
- a dynamic survey engine with validation and reporting,
- export capability,
- and a frontend that combines server-rendered views with AJAX and Chart.js visualization.

The main technical value of the system lies in how the controllers, models, helpers, and views work together as one integrated application rather than as separate services. That structure is appropriate for a campus or institutional website that needs content management, survey collection, and reporting in a single platform.

## Database Schema Overview
The database `testdatabaseperpus` contains 40 tables. Most relationships are implemented via application joins rather than enforced foreign keys.

### Core Tables and Relationships

#### Survey/Pendataan Module (InnoDB)
- **identitas_input_koleksi**: Stores survey submission metadata
  - PK: id_koleksi
  - Fields: nomor_pokok, tahun_data, tanggal_submit, ip_address, session_id
- **jawaban_koleksi**: Individual survey answers
  - PK: id_jawaban
  - FK: id_koleksi → identitas_input_koleksi.id_koleksi (enforced)
  - Fields: id_pertanyaan, jawaban
- **pertanyaan_koleksi**: Survey questions
  - PK: id_pertanyaan
  - Fields: section, isi_pertanyaan, tipe_jawaban (text/number/textarea/radio/checkbox/select), wajib, min_value, max_value, urutan, aktif
- **opsi_pertanyaan_koleksi**: Question options for multiple choice
  - PK: id_opsi
  - Fields: id_pertanyaan, label_opsi, nilai_opsi, urutan, aktif
- **config_pendataan**: Survey configuration
  - PK: id
  - Fields: tahun_aktif, status_pendataan (aktif/nonaktif), pesan_nonaktif

#### CMS Content Module (Mostly MyISAM)
- **berita**: News articles
  - PK: id_berita
  - Fields: id_kategori, username, judul, judul_seo, headline, aktif, utama, isi_berita, gambar, dibaca, tag, status
- **kategori**: News categories
  - PK: id_kategori
  - Fields: nama_kategori, kategori_seo, aktif, sidebar
- **komentar**: News comments
  - PK: id_komentar
  - Fields: id_berita, nama_komentar, isi_komentar, tgl, jam_komentar, aktif, email
- **album**: Photo albums
  - PK: id_album
  - Fields: jdl_album, album_seo, aktif
- **gallery**: Album photos
  - PK: id_gallery
  - Fields: id_album, username, jdl_gallery, gallery_seo, keterangan, gbr_gallery
- **playlist**: Video playlists
  - PK: id_playlist
  - Fields: jdl_playlist, username, playlist_seo, aktif
- **video**: Videos
  - PK: id_video
  - Fields: id_playlist, username, jdl_video, video_seo, keterangan, gbr_video, video, youtube, dilihat, tagvid
- **komentarvid**: Video comments
  - PK: id_komentar
  - Fields: id_video, nama_komentar, isi_komentar, tgl, jam_komentar, aktif

#### User Management and Permissions
- **users**: User accounts
  - PK: username
  - Fields: password, nama_lengkap, email, no_telp, foto, level, blokir, id_session
- **modul**: System modules
  - PK: id_modul
  - Fields: nama_modul, link, publish, status, aktif, urutan, link_seo
- **users_modul**: User-module permissions
  - PK: id_umod
  - Fields: id_session, id_modul

#### Other Supporting Tables
- **agenda, banner, download, halamanstatis, header, hubungi, identitas, katajelek, link, logo, menu, mod_alamat, mod_ym, pasangiklan, poling, sekilasinfo, statistik, tag, tagvid, tbl_comment, templates**: Various CMS features like static pages, downloads, polls, etc.

## Code Structure

### MVC Architecture (CodeIgniter)
- **Controllers**: Handle requests and business logic
  - Administrator.php: Main admin controller (news, categories, albums, videos, survey management)
  - Pendataan.php: Public survey interface
  - Main.php, Berita.php, etc.: Public-facing content controllers
- **Models**: Database interactions
  - Model_app.php: Core database helpers
  - Model_utama.php: Content queries
  - Model_menu.php: Menu generation
- **Views**: Templates and presentation
  - dinas-1/: Main theme templates
  - administrator/: Admin interface templates
- **Helpers**: Utility functions
  - engine_helper.php: Permission checks
- **Libraries**: Extended functionality
- **Config**: Database, routes, etc.

### Key Features

#### Public Features
- News/articles with categories and comments
- Photo galleries with albums
- Video playlists with comments
- Static pages
- Contact forms
- Downloads
- Polls
- Search functionality

#### Survey Module (Pendataan)
- Dynamic question creation (text, number, radio, checkbox, select)
- Multi-section surveys
- Required/optional questions with validation
- Year-based data collection
- Admin result viewing and export

#### Admin Features
- User management with role-based access
- Content CRUD operations
- Survey question management
- Configuration settings
- File uploads (images, videos)
- Comment moderation

## Process Flows

### User Journey
1. Visit website → Browse content (news, galleries, videos)
2. Access survey → Fill form → Validate → Submit → Success
3. Admin login → Manage content/survey → Logout

### Survey Submission Process
1. Check survey status (config_pendataan)
2. Load questions (pertanyaan_koleksi + opsi_pertanyaan_koleksi)
3. User inputs answers
4. Validate required fields
5. Save submission (identitas_input_koleksi)
6. Save answers (jawaban_koleksi)

### Admin Survey Management
1. Login and access pendataan module
2. Create/edit questions and options
3. Configure survey settings
4. View/export results with joins

## File Organization
- `/application/`: CodeIgniter app code
- `/asset/`: Static assets (CSS, JS, images)
- `/captcha/`: CAPTCHA images
- `/system/`: CodeIgniter framework
- `/template/`: Theme templates
- `/DATABASE/`: Database files
- `composer.json`: PHP dependencies
- `index.php`: Entry point

## Notable Technical Aspects
- Mixed storage engines (InnoDB for survey, MyISAM for CMS)
- Session-based authentication
- URL SEO with slug fields
- Image/file upload handling
- AJAX for dynamic content (likely in views)
- Permission system via users_modul table
- No enforced foreign keys except one in survey module
- Uses CodeIgniter's Query Builder and active record patterns

## Data Integrity and Constraints
- Only one MySQL foreign key: jawaban_koleksi.id_koleksi → identitas_input_koleksi.id_koleksi
- Relationships maintained via application logic and naming conventions
- Potential for data inconsistency if not handled properly in code

## Important Files and Their Roles

### Controllers (application/controllers/)
- **Administrator.php**: Central admin controller handling CRUD for news (berita), categories (kategori), albums, galleries, videos, playlists, comments, downloads, polls, banners, templates, and survey management. Includes session checks and permission validation.
- **Pendataan.php**: Handles public survey interface, loading questions, processing submissions, and displaying results.
- **Main.php**: Homepage controller, likely loads featured content.
- **Berita.php**: News/articles controller for public viewing.
- **Albums.php**: Photo gallery controller.
- **Playlist.php**: Video playlist controller.
- **Hubungi.php**: Contact form handler.

### Models (application/models/)
- **Model_app.php**: Core database helper with methods for insert, update, delete, view joins, and permission checks (umenu_akses). Used across controllers for common DB operations.
- **Model_utama.php**: Content-specific queries, like view_join_two for news with categories/users, and view_where for filtering.
- **Model_menu.php**: Generates menu structures from the menu table.

### Views (application/views/)
- **dinas-1/**: Main theme directory
  - content_*.php: Homepage sections (news, galleries, etc.)
  - detail*.php: Detail pages (news, albums, videos)
  - berita.php, album.php, playlist.php: Listing pages
  - pendataan.php, pendataan_detail.php: Survey pages
  - sidebar_kanan.php: Right sidebar with widgets
- **administrator/**: Admin interface
  - mod_*/view_*.php: CRUD forms for each module (e.g., mod_berita/view_berita.php)
  - menu-admin.php: Admin navigation
  - view_home_users.php: User dashboard

### Config Files (application/config/)
- **database.php**: DB connection settings (host, user, password, database name).
- **routes.php**: URL routing, default controller is Main.
- **autoload.php**: Auto-load libraries, helpers, models.
- **constants.php**: App constants.

### Helpers (application/helpers/)
- **engine_helper.php**: Contains cek_session_akses() for permission checks, queries users_modul table.

### Libraries (application/libraries/)
- Custom libraries if any, but not heavily used based on code.

### Core Files
- **index.php**: Entry point, loads CodeIgniter.
- **composer.json**: PHP dependencies (likely minimal).
- **schema-ddl.txt**: Generated MySQL schema dump.
- **erd.puml, activity-diagram.puml, etc.**: Diagram files.

## Potential Improvements
- Migrate all tables to InnoDB for better transaction support
- Add proper foreign key constraints
- Implement proper data validation layers
- Add API endpoints for modern integrations
- Improve security (password hashing, CSRF protection)

This overview provides a comprehensive understanding of the application's architecture, functionality, and technical implementation.

## 3.2.2 Karakteristik Pengguna
Sub-bab ini mengidentifikasi profil pengguna sasaran yang akan berinteraksi dengan sistem. Pengguna dikelompokkan berdasarkan peran dan tingkat akses.

| No. | Jenis Pengguna | Deskripsi |
|-----|----------------|-----------|
| 1 | Pengguna Umum | Pengguna yang mengisi survei pendataan koleksi dan melihat rekap statistik tanpa akses admin. |
| 2 | Administrator | Petugas yang masuk ke panel admin untuk mengelola survei, pertanyaan, opsi jawaban, konfigurasi, dan melihat hasil rekap. |
| 3 | Sistem | Komponen back-end yang memproses validasi, penyimpanan, agregasi, dan otentikasi pengguna. |

## 3.2.3 Kebutuhan Fungsional
Sub-bab ini merinci daftar fitur dan layanan spesifik yang wajib disediakan oleh sistem.

| No. | SRS ID | Deskripsi |
|-----|--------|-----------|
| 1 | SRS-F01 | Sistem menyediakan form survei dinamis dengan pertanyaan yang dapat diatur oleh admin. |
| 2 | SRS-F02 | Sistem memvalidasi input survei, termasuk field wajib, batas nilai, honeypot, rate limit, dan duplikasi. |
| 3 | SRS-F03 | Sistem menyimpan data survei pada tabel `identitas_input_koleksi` dan `jawaban_koleksi` secara konsisten. |
| 4 | SRS-F04 | Sistem menampilkan halaman rekap statistik dengan grafik dan tabel yang dapat difilter. |
| 5 | SRS-F05 | Sistem menyediakan autentikasi admin untuk akses manajemen pendataan. |
| 6 | SRS-F06 | Sistem menyediakan CRUD untuk pertanyaan survei, opsi jawaban, dan konfigurasi survei. |
| 7 | SRS-F07 | Sistem merespons AJAX request rekap dengan JSON dan mendukung kondisi data kosong. |
| 8 | SRS-F08 | Sistem membatasi akses administratif berdasarkan hak akses pengguna. |

## 3.2.4 Kebutuhan Non-fungsional
Sub-bab ini menjelaskan batasan kualitas dan perilaku sistem.

- Performa: Halaman survei dan rekap merespons dalam waktu wajar (< 3 detik) pada beban normal.
- Keamanan: Sistem melindungi area admin dengan autentikasi dan menjaga kerahasiaan kredensial.
- Keandalan: Sistem menjaga konsistensi data menggunakan transaksi database untuk simpanan survei.
- Kompatibilitas: Antarmuka bekerja pada browser modern dan mendukung tampilan responsif sederhana.
- Skalabilitas: Sistem dapat menangani pertumbuhan jumlah respon survei dengan mekanisme rate limit dan berikut cache submission sederhana.

## 3.2.5 Kebutuhan Antarmuka
Sub-bab ini menjelaskan hubungan sistem dengan subsistem lain dan antarmuka eksternal.

- Antarmuka utama adalah browser web yang mengakses halaman survei, rekap, dan admin.
- Sistem berinteraksi dengan database MySQL lokal untuk menyimpan dan mengambil data.
- Sistem menggunakan AJAX di halaman rekap untuk memuat grafik dan tabel tanpa reload penuh.
- Tidak ada integrasi API eksternal utama; sistem beroperasi pada lingkungan internal aplikasi CodeIgniter.

## 3.3 Analisis Sistem Usulan
Sub-bab ini menjelaskan model analisis yang digunakan untuk memahami sistem.

### 3.3.1 Pemodelan Proses
- Flowchart/Activity Diagram: Menggambarkan alur penggunaan survei, validasi, penyimpanan data, serta pemanggilan rekap statistik.
- Use Case: Menjelaskan aktor Pengguna Umum dan Administrator serta fungsi inti seperti mengisi survei, melihat rekap, login admin, dan mengelola pendataan.
- User Stories: Mendefinisikan kebutuhan dari perspektif pengguna, contohnya "Sebagai pengguna, saya ingin mengisi survei agar koleksi perpustakaan terekam".

### 3.3.2 Pemodelan Data
- ERD: Menjelaskan struktur tabel pendataan utama dan relasinya, termasuk `identitas_input_koleksi`, `jawaban_koleksi`, `pertanyaan_koleksi`, `opsi_pertanyaan_koleksi`, dan `config_pendataan`.
- Data Model: Menggambarkan entitas dan hubungan data yang mendukung proses input survei dan rekap.

### 3.3.3 Pemodelan Antarmuka
- Sketsa kasar UI/UX mencakup halaman survei dengan form pertanyaan dinamis, halaman rekap dengan filter dan grafik, serta panel admin untuk manajemen.
- Fokus pada navigasi yang jelas, kemudahan membaca hasil rekap, dan pengelolaan data tanpa kebingungan bagi pengguna administratif.

## 3.4 Perancangan Sistem
Bab ini berisi rancangan teknis solusi berdasarkan analisis kebutuhan.

### 3.4.1 Perancangan Proses
- Sequence Diagram: Menjelaskan interaksi pengguna dengan controller dan database selama proses submit survei dan load rekap.
- Spesifikasi fungsional: Menjabarkan langkah implementasi untuk validasi survei, penyimpanan data, dan otentikasi admin.

### 3.4.2 Perancangan Data
- PDM: Desain model fisik database untuk tabel inti pendataan dan struktur penyimpanan relasional.
- Class Diagram: Menyajikan komponen aplikasi seperti controller, model, dan entitas data secara konseptual.

### 3.4.3 Perancangan Antarmuka
- UI/UX detail: Menunjukkan rancangan halaman survei, rekap, dan admin dengan fokus pada kemudahan penggunaan dan aksesibilitas.
- Rancangan antarmuka mendukung filter rekap, visualisasi chart, dan manajemen data administratif.

## 4 Alur Penggunaan (User & Admin)

Bagian ini menambahkan alur penggunaan terperinci dari dua perspektif: **Pengguna (User/Publik)** dan **Administrator**. Setiap alur fokus pada langkah operasional yang relevan untuk fitur inti sistem.

### 4.1 Alur Penggunaan — Pengguna (User)

- **Beranda & Navigasi:** Buka situs → lihat highlight/berita → klik judul untuk detail → gunakan sidebar atau menu untuk navigasi kategori.
- **Berita/Artikel:** Buka daftar berita → pilih artikel → baca isi → berikan komentar (jika tersedia) → komentar dikirim untuk moderasi (jika perlu).
- **Galeri & Album Foto:** Buka halaman album → pilih album → lihat daftar foto → klik foto untuk tampilan besar → unduh/zoom jika diizinkan.
- **Video & Playlist:** Buka daftar playlist → pilih video → putar video (embedded/YouTube) → beri komentar pada video (opsional).
- **Halaman Statis:** Akses menu → buka halaman statis (tentang, layanan, dsb.) → baca dan gunakan informasi kontak jika diperlukan.
- **Kontak / Hubungi Kami:** Buka formulir kontak → isi nama, email, pesan → kirim → tampilkan pesan sukses; admin menerima pemberitahuan/lihat pesan di panel.
- **Download:** Buka halaman download → klik link file → unduh; file yang memerlukan login akan meminta autentikasi.
- **Polling (Poling):** Pilih opsi pada widget polling → kirim suara → lihat hasil ringkas (grafik/angka) jika diizinkan.
- **Pencarian:** Masukkan kata kunci → kirim → tampilkan hasil terfilter di halaman hasil pencarian.
- **Survei / Pendataan:** Buka halaman pendataan → sistem menampilkan formulir untuk **tahun aktif** (ditentukan oleh admin via `config_pendataan.tahun_aktif`) → isi tiap section/pertanyaan dinamis → validasi client/server untuk field wajib/batasan (min/max, tipe) → kirim → simpan `identitas_input_koleksi` (menggunakan `tahun_data` = `config_pendataan.tahun_aktif`, dengan fallback `date('Y')` jika kosong) + `jawaban_koleksi` → tampilkan pesan sukses dan nomor referensi (jika ada).

  Catatan teknis singkat: implementasi pada `application/controllers/Pendataan.php` menunjukkan bahwa user **tidak** memilih tahun saat submit — tahun diset otomatis dari `config_pendataan.tahun_aktif`, atau `date('Y')` jika field tersebut kosong. Validasi dilakukan di sisi klien (JS) dan sisi server (`form_validation`) berdasarkan field `wajib`, `tipe_jawaban`, `min_value`, `max_value`. Mekanisme keamanan meliputi honeypot, rate-limit, pemeriksaan double-submit, sanitasi input, dan penggunaan transaksi DB saat menyimpan.
- **Autentikasi Pengguna:** (Jika tersedia) Login → akses fitur yang membutuhkan otentikasi (mis. unduhan terbatas, dashboard pengguna) → logout.

### 4.2 Alur Penggunaan — Administrator

- **Login Admin:** Buka `/administrator` → masukkan kredensial → cek hak akses via `users_modul` → akses dashboard.
- **Dashboard:** Lihat ringkasan (jumlah respon survei, berita terbaru, komentar tertunda, statistik kunjungan) → pilih modul untuk aksi cepat.
- **Manajemen Berita/Artikel:** Admin → Modul Berita → Buat/Edit → Masukkan judul, konten, kategori, gambar, tag → Set publikasi (aktif/utama) → Simpan → Artikel muncul di front-end sesuai status SEO.
- **Kategori Berita:** Tambah/Edit/Hapus kategori → atur tampil di sidebar atau tidak.
- **Moderasi Komentar:** Buka modul komentar → lihat komentar baru → set `aktif` atau `hapus` → jika diaktifkan tampil di front-end.
- **Manajemen Album & Galeri:** Modul Album → buat album → unggah foto di modul gallery → set metadata → simpan → foto muncul di galeri publik.
- **Playlist & Video:** Modul Playlist → buat playlist → tambah video → unggah/masukkan link YouTube → atur thumbnail dan deskripsi → publikasikan.
- **Halaman Statis / Download / Banner / Menu:** CRUD untuk halaman statis, file download, banner, dan struktur menu → simpan konfigurasi tampilan.
- **Polling:** Buat poll → tambahkan opsi → aktifkan polling → lihat hasil real-time dan reset jika perlu.
- **Manajemen Pengguna & Hak Akses:** Buat/ubah akun user → atur `level` dan `blokir` → kelola modul akses via `users_modul` untuk membatasi fitur admin.
- **Survei / Pendataan (Konfigurasi):** Akses modul Pendataan → atur `config_pendataan` (tahun aktif, status) → buat/edit `pertanyaan_koleksi` dan `opsi_pertanyaan_koleksi` → atur validasi (wajib, min/max, tipe jawaban) → simpan.
- **Lihat & Ekspor Hasil Survei:** Buka laporan pendataan → filter berdasarkan tahun/section/kategori → tampilkan tabel/diagram → ekspor ke CSV/XLS (jika fitur tersedia) → unduh file hasil.
- **Validasi & Konsistensi Data:** Gunakan preview data → periksa jawaban tidak valid atau duplikat → lakukan pembersihan atau beri catatan.
- **File Uploads & Manajemen Media:** Kelola file di server → hapus file usang → perbaiki thumbnail dan path jika perlu.
- **Konfigurasi Situs & Template:** Ubah pengaturan identitas situs (`identitas` table), template, sidebar, dan widget.
- **Log & Audit:** Cek logs di `/application/logs/` untuk aktivitas yang mencurigakan atau error → ambil tindakan (rollback/restore) bila perlu.

### 4.3 Alur Rekap & Ekspor (Admin dan User Terbatas)

- **Filter Rekap:** Pilih rentang waktu, kategori, atau filter lain di UI rekap → klik `Terapkan` → sistem men-query DB dan menampilkan tabel + grafik.
- **Visualisasi:** Data di-render menjadi chart (bar/line/pie) via library chart (AJAX → JSON → chart rendering).
- **Ekspor:** Klik `Ekspor` → sistem menyiapkan CSV/XLS → user/admin mengunduh file. Untuk data sensitif, hanya admin dengan hak akses yang dapat mengekspor.

### 4.4 Catatan Operasional dan Validasi

- Semua input survei divalidasi di client dan server; entri wajib dan batas numerik dicek sebelum penyimpanan.
- Rate-limit dan anti-duplicate (session/ip) diterapkan untuk mencegah spam pada submit survei dan polling.
- Setiap operasi CRUD di area admin memeriksa permission via helper `cek_session_akses()` dan `users_modul`.
- Export dan fungsi administrasi penting disarankan hanya diakses lewat HTTPS dan oleh akun dengan level admin.

---

Dokumentasi alur ini ditambahkan untuk mempermudah pemahaman proses bisnis dari sisi pengguna akhir dan operator administrasi. Jika Anda ingin detail visual (diagram aktivitas per fitur), saya bisa menghasilkan file PlantUML terpisah untuk beberapa alur prioritas.
