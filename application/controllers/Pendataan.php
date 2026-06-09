<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendataan extends CI_Controller {

    private $cache_ttl = [
        'questions'   => 86400,
        'years'       => 3600,
        'rekap_data'  => 300,
        'detail'      => 3600,
        'rate_limit'  => 3600,
        'config'      => 1800
    ];

    public function get_chart_stats()
    {
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(403)
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['error' => 'Forbidden']));
            return;
        }
    
        // ── Sanitasi parameter ───────────────────────────────────────────────
        $tahun     = $this->security->xss_clean($this->input->get('tahun')       ?? date('Y'));
        $nomor     = $this->security->xss_clean($this->input->get('nomor_pokok') ?? '');
        $date_from = $this->security->xss_clean($this->input->get('date_from')   ?? '');
        $date_to   = $this->security->xss_clean($this->input->get('date_to')     ?? '');
    
        // ── Cache ────────────────────────────────────────────────────────────
        $cache_key = 'chart_stats_v2_' . md5($tahun . $nomor . $date_from . $date_to);
        $cached    = $this->cache->get($cache_key);
        if ($cached !== FALSE) {
            $this->output->set_content_type('application/json')->set_output(json_encode($cached));
            return;
        }
    
        // ── Helper: SUM satu id_pertanyaan dengan semua filter aktif ─────────
        $sum_q = function(int $id_pertanyaan) use ($tahun, $nomor, $date_from, $date_to): int {
            $this->db->select('COALESCE(SUM(CAST(j.jawaban AS UNSIGNED)), 0) AS total');
            $this->db->from('jawaban_koleksi j');
            $this->db->join('identitas_input_koleksi i', 'i.id_koleksi = j.id_koleksi');
            $this->db->where('j.id_pertanyaan', $id_pertanyaan);
            $this->db->where('i.tahun_data', $tahun);
            // Hanya hitung jawaban numerik murni — menghindari textarea atau input kotor
            $this->db->where("j.jawaban REGEXP '^[0-9]+$'");
            if (!empty($nomor))     { $this->db->like('i.nomor_pokok', $nomor); }
            if (!empty($date_from)) { $this->db->where('DATE(i.tanggal_submit) >=', $date_from); }
            if (!empty($date_to))   { $this->db->where('DATE(i.tanggal_submit) <=', $date_to); }
            $row = $this->db->get()->row_array();
            return (int)($row['total'] ?? 0);
        };
    
        // ── Helper: WHERE clause untuk identitas_input_koleksi ──────────────
        $base_where = function() use ($tahun, $nomor, $date_from, $date_to) {
            $this->db->where('tahun_data', $tahun);
            if (!empty($nomor))     { $this->db->like('nomor_pokok', $nomor); }
            if (!empty($date_from)) { $this->db->where('DATE(tanggal_submit) >=', $date_from); }
            if (!empty($date_to))   { $this->db->where('DATE(tanggal_submit) <=', $date_to); }
        };
    
        // ════════════════════════════════════════════════════════════════════
        // STAT CARDS
        // ════════════════════════════════════════════════════════════════════
        $this->db->select('COUNT(*) as total');
        $this->db->from('identitas_input_koleksi');
        $base_where();
        $total_laporan = (int)($this->db->get()->row_array()['total'] ?? 0);
    
        $stat_cards = [
            'total_laporan'        => $total_laporan,
            'total_judul_tercetak' => $sum_q(15),
            'total_judul_digital'  => $sum_q(16),
            'total_anggota_2025'   => $sum_q(51),
        ];
    
        // ════════════════════════════════════════════════════════════════════
        // CHART 1: Koleksi saat ini — judul & eksemplar, tercetak vs digital
        // id: Judul Tercetak=15, Eksemplar Tercetak=17, Judul Digital=16, Eksemplar Digital=18
        // ════════════════════════════════════════════════════════════════════
        $koleksi_saat_ini = [
            'labels' => ['Judul Tercetak', 'Eksemplar Tercetak', 'Judul Digital', 'Eksemplar Digital'],
            'data'   => [$sum_q(15), $sum_q(17), $sum_q(16), $sum_q(18)],
        ];
    
        // ════════════════════════════════════════════════════════════════════
        // CHART 2: Pertumbuhan koleksi — 2025 vs 2026
        // 2025: Judul Tercetak=19, Eksemplar Tercetak=20, Judul Digital=21, Eksemplar Digital=22
        // 2026: Judul Tercetak=23, Eksemplar Tercetak=24, Judul Digital=25, Eksemplar Digital=26
        // ════════════════════════════════════════════════════════════════════
        $pertumbuhan = [
            'labels'    => ['Judul Tercetak', 'Eksemplar Tercetak', 'Judul Digital', 'Eksemplar Digital'],
            'data_2025' => [$sum_q(19), $sum_q(20), $sum_q(21), $sum_q(22)],
            'data_2026' => [$sum_q(23), $sum_q(24), $sum_q(25), $sum_q(26)],
        ];
    
        // ════════════════════════════════════════════════════════════════════
        // CHART 3: Pengunjung & Akses — 2025 vs 2026
        // 2025: Onsite=45, Online=46, Akses Teknologi=49
        // 2026: Onsite=47, Online=48, Akses Teknologi=50
        // ════════════════════════════════════════════════════════════════════
        $pengunjung = [
            'labels'    => ['Onsite', 'Online', 'Akses Teknologi'],
            'data_2025' => [$sum_q(45), $sum_q(46), $sum_q(49)],
            'data_2026' => [$sum_q(47), $sum_q(48), $sum_q(50)],
        ];
    
        // ════════════════════════════════════════════════════════════════════
        // CHART 4: Pemanfaatan koleksi tercetak — 2025 vs 2026
        // 2025: Dibaca di Tempat (judul)=31, Dipinjam (judul)=33
        // 2026: Dibaca di Tempat (judul)=35, Dipinjam (judul)=37
        // ════════════════════════════════════════════════════════════════════
        $pemanfaatan = [
            'labels'    => ['Dibaca di Tempat', 'Dipinjam'],
            'data_2025' => [$sum_q(31), $sum_q(33)],
            'data_2026' => [$sum_q(35), $sum_q(37)],
        ];
    
        // ════════════════════════════════════════════════════════════════════
        // CHART 5: Distribusi jenis perpustakaan (id_pertanyaan = 4)
        // COUNT per nilai jawaban, join ke opsi untuk label yang benar
        // ════════════════════════════════════════════════════════════════════
        $this->db->select('j.jawaban AS nilai, o.label_opsi AS label, COUNT(*) AS total');
        $this->db->from('jawaban_koleksi j');
        $this->db->join('identitas_input_koleksi i', 'i.id_koleksi = j.id_koleksi');
        $this->db->join(
            'opsi_pertanyaan_koleksi o',
            "o.id_pertanyaan = j.id_pertanyaan AND o.nilai_opsi = j.jawaban",
            'left'
        );
        $this->db->where('j.id_pertanyaan', 4);
        $this->db->where('i.tahun_data', $tahun);
        if (!empty($nomor))     { $this->db->like('i.nomor_pokok', $nomor); }
        if (!empty($date_from)) { $this->db->where('DATE(i.tanggal_submit) >=', $date_from); }
        if (!empty($date_to))   { $this->db->where('DATE(i.tanggal_submit) <=', $date_to); }
        $this->db->group_by('j.jawaban');
        $this->db->order_by('total', 'DESC');
        $rows_jenis = $this->db->get()->result_array();
    
        $distribusi_jenis = [
            'labels' => array_map(fn($r) => $r['label'] ?: $r['nilai'], $rows_jenis),
            'data'   => array_map(fn($r) => (int)$r['total'], $rows_jenis),
        ];
    
        // ── Susun response ───────────────────────────────────────────────────
        $result = [
            'stat_cards'       => $stat_cards,
            'koleksi_saat_ini' => $koleksi_saat_ini,
            'pertumbuhan'      => $pertumbuhan,
            'pengunjung'       => $pengunjung,
            'pemanfaatan'      => $pemanfaatan,
            'distribusi_jenis' => $distribusi_jenis,
        ];
    
        $this->cache->save($cache_key, $result, 300);
    
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    // ============================================================================
    // CONSTRUCTOR & INITIALIZATION
    // ============================================================================

    public function __construct() {
        parent::__construct();
        $this->load->helper(['form', 'url', 'security']);
        $this->load->library(['session', 'form_validation']);
        $this->load->driver('cache', ['adapter' => 'file', 'backup' => 'file']);
        $this->load->database();
    }

    // ============================================================================
    // MAIN PUBLIC ROUTES (PRIMARY FEATURES)
    // ============================================================================

    /**
     * Halaman utama form pendataan
     */
    public function index() {
        $config = $this->check_survey_status(true);
        if (!$config) return;

        $data = [
            'title' => 'Pendataan Koleksi Perpustakaan',
            'description' => 'Form pendataan koleksi perpustakaan Jawa Tengah',
            'keywords' => 'pendataan, perpustakaan, koleksi, jawa tengah',
            'config' => $config
        ];

        $cache_key = 'pendataan/questions_with_options_v2';
        $pertanyaan = $this->cache->get($cache_key);

        if ($pertanyaan === FALSE) {
            $this->db->from('pertanyaan_koleksi')->where('aktif', 1)->order_by('section, urutan', 'ASC');
            $all_questions = $this->db->get()->result_array();

            $question_ids_with_options = [];
            foreach ($all_questions as $q) {
                if (in_array($q['tipe_jawaban'], ['select', 'radio', 'checkbox'])) {
                    $question_ids_with_options[] = $q['id_pertanyaan'];
                }
            }

            $options_by_question = [];
            if (!empty($question_ids_with_options)) {
                $this->db->where_in('id_pertanyaan', $question_ids_with_options)->where('aktif', 1)->order_by('id_pertanyaan, urutan');
                foreach ($this->db->get('opsi_pertanyaan_koleksi')->result_array() as $opt) {
                    $options_by_question[$opt['id_pertanyaan']][] = $opt;
                }
            }

            $pertanyaan = [];
            foreach ($all_questions as $row) {
                if (isset($options_by_question[$row['id_pertanyaan']])) {
                    $row['opsi'] = $options_by_question[$row['id_pertanyaan']];
                }
                $pertanyaan[$row['section']][] = $row;
            }

            $this->cache->save($cache_key, $pertanyaan, $this->cache_ttl['questions']);
        }

        $data['pertanyaan'] = $pertanyaan;
        $data['section_names'] = $this->get_section_names();
        $this->template->load(template().'/template', template().'/pendataan', $data);
    }

    /**
     * Submit form pendataan
     */
    public function submit() {
        $config = $this->check_survey_status(true);
        if (!$config) return;

        if (!$this->_check_honeypot()) {
            log_message('info', 'Honeypot triggered from IP: ' . $this->_get_ip_address());
            redirect('pendataan/terima_kasih');
        }
        
        if (!$this->_check_rate_limit()) {
            $this->session->set_flashdata('error', 'Anda sudah mengisi formulir ini. Setiap pengguna hanya dapat mengisi sekali per jam.');
            redirect('pendataan');
        }
        
        if (!$this->_check_double_submission()) {
            $this->session->set_flashdata('error', 'Data baru saja dikirim. Mohon tunggu beberapa saat.');
            redirect('pendataan');
        }
        
        if (!$this->input->post()) redirect('pendataan');

        $this->load->library('form_validation');
        $pertanyaan = $this->_get_pertanyaan_for_validation();

        foreach ($pertanyaan as $section => $questions) {
            foreach ($questions as $q) {
                $field_name = 'pertanyaan[' . $q['id_pertanyaan'] . ']';
                $rules = [];
                
                if ($q['wajib'] == 1) $rules[] = 'required';
                
                if ($q['tipe_jawaban'] == 'text' || $q['tipe_jawaban'] == 'textarea') {
                    $rules[] = 'max_length[1000]';
                } elseif ($q['tipe_jawaban'] == 'number') {
                    $rules[] = 'numeric';
                    if (isset($q['min_value'])) $rules[] = 'greater_than_equal_to[' . $q['min_value'] . ']';
                    if (isset($q['max_value'])) $rules[] = 'less_than_equal_to[' . $q['max_value'] . ']';
                }
                
                if (!empty($rules)) {
                    $this->form_validation->set_rules($field_name, $q['isi_pertanyaan'], implode('|', $rules));
                }
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('pendataan');
        }

        $jawaban_post = $this->input->post('pertanyaan');
        if (empty($jawaban_post)) {
            $this->session->set_flashdata('error', 'Form tidak boleh kosong');
            redirect('pendataan');
        }

        $jawaban_post = $this->_sanitize_input($jawaban_post);
        $this->db->trans_begin();

        $this->db->insert('identitas_input_koleksi', [
            'nomor_pokok' => '-',
            'tahun_data' => $config['tahun_aktif'] ?: date('Y'),
            'ip_address' => $this->_get_ip_address(),
            'session_id' => session_id()
        ]);

        $id_koleksi = $this->db->insert_id();
        if (!$id_koleksi) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Gagal menyimpan data');
            redirect('pendataan');
        }

        $batch_insert = [];
        $nomor_pokok = null;
        
        foreach ($jawaban_post as $id_pertanyaan => $jawaban) {
            if ($jawaban === '' || $jawaban === null) continue;
            
            if (is_array($jawaban)) $jawaban = implode(', ', $jawaban);
            if (strlen($jawaban) > 1000) $jawaban = substr($jawaban, 0, 1000);
            
            $batch_insert[] = [
                'id_koleksi' => $id_koleksi,
                'id_pertanyaan' => (int)$id_pertanyaan,
                'jawaban' => trim($jawaban)
            ];
            
            if ($id_pertanyaan == 1) $nomor_pokok = trim($jawaban);
        }

        if (!empty($batch_insert)) {
            $this->db->insert_batch('jawaban_koleksi', $batch_insert);
        }
        
        if ($nomor_pokok) {
            $this->db->update('identitas_input_koleksi', ['nomor_pokok' => $nomor_pokok], ['id_koleksi' => $id_koleksi]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Terjadi kesalahan penyimpanan');
            redirect('pendataan');
        }

        $this->db->trans_commit();
        $this->_update_rate_limit();
        $this->_mark_submission();
        $this->clear_submission_cache();
        redirect('pendataan/terima_kasih');
    }

    // ============================================================================
    // REKAP & REPORTING FEATURES
    // ============================================================================

    /**
     * Halaman rekap data
     */
    public function rekap() {
        $data = [
            'title' => 'Rekap Pendataan',
            'description' => 'Rekap data perpustakaan',
            'keywords' => 'rekap, pendataan'
        ];
        
        $cache_key = 'pendataan/list_tahun';
        $list_tahun = $this->cache->get($cache_key);
        
        if ($list_tahun === FALSE) {
            $list_tahun = $this->db->select('DISTINCT(tahun_data) as tahun')
                ->from('identitas_input_koleksi')
                ->order_by('tahun_data', 'DESC')
                ->get()
                ->result_array();
            $this->cache->save($cache_key, $list_tahun, $this->cache_ttl['years']);
        }
        
        $data['list_tahun'] = $list_tahun;
        $this->template->load(template().'/template', template().'/pendataan_rekap', $data);
    }

    /**
     * Get rekap data via AJAX with enhanced filtering and sorting
     */
    public function get_rekap_data() {
        $page = max(1, (int)$this->input->get('page'));
        $per_page = max(10, min(100, (int)$this->input->get('per_page'))); // Limit between 10-100
        
        // Sanitize and validate year
        $tahun = $this->input->get('tahun');
        if (!preg_match('/^(19|20)\d{2}$/', $tahun)) $tahun = date('Y');
        
        // Sanitize nomor pokok
        $nomor_pokok = preg_replace('/[^a-zA-Z0-9\-\/\s]/', '', $this->input->get('nomor_pokok'));
        
        // Sanitize and validate date range
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        if ($date_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) $date_from = '';
        if ($date_to && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) $date_to = '';
        
        // Validate and sanitize sort order
        $sort_by = $this->input->get('sort_by');
        $allowed_sorts = [
            'tanggal_desc' => ['i.tanggal_submit', 'DESC'],
            'tanggal_asc' => ['i.tanggal_submit', 'ASC'],
            'nomor_asc' => ['i.nomor_pokok', 'ASC'],
            'nomor_desc' => ['i.nomor_pokok', 'DESC'],
            'jawaban_desc' => ['jumlah_jawaban', 'DESC'],
            'jawaban_asc' => ['jumlah_jawaban', 'ASC']
        ];
        
        if (!isset($allowed_sorts[$sort_by])) {
            $sort_by = 'tanggal_desc';
        }
        
        $offset = ($page - 1) * $per_page;
        $filter_hash = md5($tahun . '|' . $nomor_pokok . '|' . $date_from . '|' . $date_to . '|' . $sort_by . '|' . $per_page);
        $cache_key = "pendataan/rekap_{$filter_hash}_page_{$page}";
        
        $result = $this->cache->get($cache_key);
        
        if ($result === FALSE) {
            // Build query with filters
            $this->db->from('identitas_input_koleksi i')->where('i.tahun_data', $tahun);
            
            if (!empty($nomor_pokok)) {
                $this->db->like('i.nomor_pokok', $nomor_pokok);
            }
            
            if (!empty($date_from)) {
                $this->db->where('DATE(i.tanggal_submit) >=', $date_from);
            }
            
            if (!empty($date_to)) {
                $this->db->where('DATE(i.tanggal_submit) <=', $date_to);
            }
            
            $total_records = $this->db->count_all_results('', FALSE);
            
            // Get data with sorting
            $this->db->select('i.id_koleksi, i.nomor_pokok, i.tahun_data, i.tanggal_submit, COUNT(j.id_jawaban) as jumlah_jawaban');
            $this->db->join('jawaban_koleksi j', 'i.id_koleksi = j.id_koleksi', 'left');
            $this->db->group_by('i.id_koleksi');
            $this->db->order_by($allowed_sorts[$sort_by][0], $allowed_sorts[$sort_by][1]);
            $this->db->limit($per_page, $offset);
            $data = $this->db->get()->result_array();
            
            // Sanitize output
            foreach ($data as &$row) {
                $row['nomor_pokok'] = htmlspecialchars($row['nomor_pokok'], ENT_QUOTES, 'UTF-8');
                $row['tahun_data'] = htmlspecialchars($row['tahun_data'], ENT_QUOTES, 'UTF-8');
            }
            
            $result = [
                'data' => $data,
                'total' => $total_records,
                'page' => $page,
                'per_page' => $per_page,
                'total_pages' => ceil($total_records / $per_page)
            ];
            
            $this->cache->save($cache_key, $result, $this->cache_ttl['rekap_data']);
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    /**
     * Detail data submission
     */
    public function detail($id_koleksi = null) {
        if (!$id_koleksi || !is_numeric($id_koleksi) || $id_koleksi <= 0) {
            $this->session->set_flashdata('error', 'ID tidak valid');
            redirect('pendataan/rekap');
        }
        
        $id_koleksi = (int)$id_koleksi;
        $cache_key = 'pendataan/detail_' . $id_koleksi;
        $data = $this->cache->get($cache_key);
        
        if ($data === FALSE) {
            $identitas = $this->db->where('id_koleksi', $id_koleksi)->get('identitas_input_koleksi')->row_array();
            
            if (!$identitas) {
                $this->session->set_flashdata('error', 'Data tidak ditemukan');
                redirect('pendataan/rekap');
            }

            $nama = $this->db->select('jawaban')
                ->where('id_koleksi', $id_koleksi)
                ->where('id_pertanyaan', 2)
                ->get('jawaban_koleksi')
                ->row_array();
            
            $identitas['nama_perpustakaan'] = $nama ? $nama['jawaban'] : $identitas['nomor_pokok'];

            $this->db->select('j.*, p.isi_pertanyaan, p.section, p.tipe_jawaban')->from('jawaban_koleksi j');
            $this->db->join('pertanyaan_koleksi p', 'j.id_pertanyaan = p.id_pertanyaan');
            $this->db->where('j.id_koleksi', $id_koleksi)->order_by('p.section, p.urutan');
            $jawaban = $this->db->get()->result_array();

            foreach ($jawaban as &$row) {
                $row['jawaban'] = htmlspecialchars($row['jawaban'], ENT_QUOTES, 'UTF-8');
                $row['isi_pertanyaan'] = htmlspecialchars($row['isi_pertanyaan'], ENT_QUOTES, 'UTF-8');
            }

            $jawaban_grouped = [];
            foreach ($jawaban as $row) {
                $jawaban_grouped[$row['section']][] = $row;
            }

            $identitas['nomor_pokok'] = htmlspecialchars($identitas['nomor_pokok'], ENT_QUOTES, 'UTF-8');
            $identitas['nama_perpustakaan'] = htmlspecialchars($identitas['nama_perpustakaan'], ENT_QUOTES, 'UTF-8');

            $data = [
                'title' => 'Detail Pendataan - ' . $identitas['nama_perpustakaan'],
                'description' => 'Detail data perpustakaan',
                'keywords' => 'detail, pendataan',
                'identitas' => $identitas,
                'jawaban_grouped' => $jawaban_grouped,
                'section_names' => $this->get_section_names()
            ];
            
            $this->cache->save($cache_key, $data, $this->cache_ttl['detail']);
        }

        $this->template->load(template().'/template', template().'/pendataan_detail', $data);
    }

    /**
     * Export data ke Excel (CSV) dengan streaming untuk handle unlimited records
     * Supports bulk export of selected items
     */
    public function export_excel() {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');
        
        if (!$this->_check_export_rate_limit()) {
            $this->session->set_flashdata('error', 'Terlalu banyak permintaan export. Coba lagi nanti.');
            redirect('pendataan/rekap');
        }

        // Check if this is a bulk export (selected items)
        $selected_ids = $this->input->post('selected_ids');
        $is_bulk_export = false;
        
        if (is_array($selected_ids) && !empty($selected_ids)) {
            $is_bulk_export = true;
            // Sanitize and validate IDs
            $selected_ids = array_filter(array_map('intval', $selected_ids), function($id) {
                return $id > 0;
            });
            
            // Limit to prevent abuse (max 1000 items at once)
            if (count($selected_ids) > 1000) {
                $this->session->set_flashdata('error', 'Maksimal 1000 data dapat diekspor sekaligus.');
                redirect('pendataan/rekap');
            }
            
            if (empty($selected_ids)) {
                $this->session->set_flashdata('error', 'Tidak ada data valid yang dipilih.');
                redirect('pendataan/rekap');
            }
        }

        // Get filters for regular export
        $tahun = $this->input->get('tahun');
        if (!preg_match('/^(19|20)\d{2}$/', $tahun)) $tahun = date('Y');
        
        $nomor_pokok = preg_replace('/[^a-zA-Z0-9\-\/\s]/', '', $this->input->get('nomor_pokok'));
        
        // Sanitize and validate date range
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        if ($date_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from)) $date_from = '';
        if ($date_to && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) $date_to = '';
        
        // Validate sort order
        $sort_by = $this->input->get('sort_by');
        $allowed_sorts = [
            'tanggal_desc' => ['i.tanggal_submit', 'DESC'],
            'tanggal_asc' => ['i.tanggal_submit', 'ASC'],
            'nomor_asc' => ['i.nomor_pokok', 'ASC'],
            'nomor_desc' => ['i.nomor_pokok', 'DESC'],
            'jawaban_desc' => ['jumlah_jawaban', 'DESC'],
            'jawaban_asc' => ['jumlah_jawaban', 'ASC']
        ];
        
        if (!isset($allowed_sorts[$sort_by])) {
            $sort_by = 'tanggal_desc';
        }

        // Build query based on export type
        if ($is_bulk_export) {
            $this->db->from('identitas_input_koleksi i');
            $this->db->where_in('i.id_koleksi', $selected_ids);
        } else {
            $this->db->from('identitas_input_koleksi i')->where('i.tahun_data', $tahun);
            if (!empty($nomor_pokok)) $this->db->like('i.nomor_pokok', $nomor_pokok);
            if (!empty($date_from)) $this->db->where('DATE(i.tanggal_submit) >=', $date_from);
            if (!empty($date_to)) $this->db->where('DATE(i.tanggal_submit) <=', $date_to);
        }
        
        $total_records = $this->db->count_all_results();

        if ($total_records == 0) {
            $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor');
            redirect('pendataan/rekap');
        }

        // Get pertanyaan list
        $cache_key = 'pendataan/questions_export_list';
        $pertanyaan_list = $this->cache->get($cache_key);
        
        if ($pertanyaan_list === FALSE) {
            $pertanyaan_list = $this->db->select('id_pertanyaan, isi_pertanyaan')
                ->where('aktif', 1)
                ->order_by('section, urutan')
                ->get('pertanyaan_koleksi')
                ->result_array();
            $this->cache->save($cache_key, $pertanyaan_list, $this->cache_ttl['questions']);
        }

        // Set headers for CSV download
        $filename = $is_bulk_export ? 
            'rekap_pendataan_selected_' . date('Y-m-d_His') . '.csv' : 
            'rekap_pendataan_' . $tahun . '.csv';
            
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $output = fopen('php://output', 'w');
        
        // Header row
        $header = ['ID', 'Nomor Pokok', 'Tahun', 'Tanggal'];
        foreach ($pertanyaan_list as $p) {
            $header[] = $p['isi_pertanyaan'];
        }
        fputcsv($output, $header);
        
        // Streaming data in chunks
        $chunk_size = 50;
        $offset = 0;
        
        while ($offset < $total_records) {
            // Rebuild query for chunk
            if ($is_bulk_export) {
                $this->db->select('i.*')->from('identitas_input_koleksi i');
                $this->db->where_in('i.id_koleksi', $selected_ids);
                $this->db->order_by('i.tanggal_submit', 'DESC');
            } else {
                $this->db->select('i.*')->from('identitas_input_koleksi i')->where('i.tahun_data', $tahun);
                if (!empty($nomor_pokok)) $this->db->like('i.nomor_pokok', $nomor_pokok);
                if (!empty($date_from)) $this->db->where('DATE(i.tanggal_submit) >=', $date_from);
                if (!empty($date_to)) $this->db->where('DATE(i.tanggal_submit) <=', $date_to);
                $this->db->order_by($allowed_sorts[$sort_by][0], $allowed_sorts[$sort_by][1]);
            }
            
            $this->db->limit($chunk_size, $offset);
            $rekap_chunk = $this->db->get()->result_array();
            
            if (empty($rekap_chunk)) break;
            
            $id_list = array_column($rekap_chunk, 'id_koleksi');
            $jawaban_chunk = $this->db->select('id_koleksi, id_pertanyaan, jawaban')
                ->where_in('id_koleksi', $id_list)
                ->get('jawaban_koleksi')
                ->result_array();
            
            $jawaban_by_koleksi = [];
            foreach ($jawaban_chunk as $j) {
                $jawaban_by_koleksi[$j['id_koleksi']][$j['id_pertanyaan']] = $j['jawaban'];
            }
            
            foreach ($rekap_chunk as $row) {
                $row_data = [
                    $row['id_koleksi'],
                    $this->_sanitize_csv_value($row['nomor_pokok']),
                    $row['tahun_data'],
                    $row['tanggal_submit']
                ];
                
                $jawaban_map = isset($jawaban_by_koleksi[$row['id_koleksi']]) ? $jawaban_by_koleksi[$row['id_koleksi']] : [];
                
                foreach ($pertanyaan_list as $p) {
                    $value = isset($jawaban_map[$p['id_pertanyaan']]) ? $jawaban_map[$p['id_pertanyaan']] : '';
                    $row_data[] = $this->_sanitize_csv_value($value);
                }
                
                fputcsv($output, $row_data);
            }
            
            $offset += $chunk_size;
            if (ob_get_level() > 0) ob_flush();
            flush();
        }
        
        fclose($output);
        $this->_update_export_rate_limit();
        exit;
    }

    // ============================================================================
    // SECONDARY PUBLIC ROUTES
    // ============================================================================

    /**
     * Halaman terima kasih setelah submit
     */
    public function terima_kasih() {
        $data = [
            'title' => 'Terima Kasih',
            'description' => 'Terima kasih telah mengisi pendataan',
            'keywords' => 'pendataan, perpustakaan'
        ];
        $this->template->load(template().'/template', template().'/pendataan_terima_kasih', $data);
    }

    // ============================================================================
    // CONFIGURATION & STATUS
    // ============================================================================

    /**
     * Check apakah survey sedang aktif atau tidak
     */
    private function check_survey_status($redirect_if_closed = true) {
        $config = $this->db->where('id', 1)->limit(1)->get('config_pendataan')->row_array();
        
        if (!$config) {
            $config = [
                'id' => 1,
                'tahun_aktif' => date('Y'),
                'status_pendataan' => 'nonaktif',
                'pesan_nonaktif' => 'Pendataan saat ini sedang tidak dibuka.'
            ];
            $this->db->insert('config_pendataan', $config);
        }
        
        if ($redirect_if_closed && $config['status_pendataan'] !== 'aktif') {
            $this->show_survey_closed($config);
            return false;
        }
        
        return $config;
    }

    /**
     * Tampilkan halaman survey closed
     */
    private function show_survey_closed($config) {
        $data = [
            'title' => 'Pendataan Ditutup',
            'description' => 'Pendataan koleksi perpustakaan sedang tidak dibuka',
            'keywords' => 'pendataan, perpustakaan, ditutup',
            'config' => $config,
            'pesan' => $config['pesan_nonaktif'] ?? 'Pendataan saat ini sedang ditutup.'
        ];
        $this->template->load(template().'/template', template().'/pendataan_closed', $data);
    }

    // ============================================================================
    // SECURITY & VALIDATION
    // ============================================================================

    /**
     * Check honeypot untuk spam protection
     */
    private function _check_honeypot() {
        return empty($this->input->post('website_url'));
    }

    /**
     * Check rate limit untuk mencegah spam submission
     */
    private function _check_rate_limit() {
        $sid = session_id();
        if ($this->cache->get('rate_limit_session_' . md5($sid)) !== FALSE) {
            return false;
        }
        
        $ip = $this->_get_ip_address();
        $attempts = $this->cache->get('rate_limit_ip_' . md5($ip)) ?: 0;
        
        return $attempts < 50;
    }

    /**
     * Update rate limit counter
     */
    private function _update_rate_limit() {
        $sid = session_id();
        $this->cache->save('rate_limit_session_' . md5($sid), 1, $this->cache_ttl['rate_limit']);
        
        $ip = $this->_get_ip_address();
        $attempts = $this->cache->get('rate_limit_ip_' . md5($ip)) ?: 0;
        $this->cache->save('rate_limit_ip_' . md5($ip), $attempts + 1, $this->cache_ttl['rate_limit']);
    }

    /**
     * Check double submission
     */
    private function _check_double_submission() {
        return $this->cache->get('double_submit_' . md5(session_id())) === FALSE;
    }

    /**
     * Mark submission untuk prevent double submit
     */
    private function _mark_submission() {
        $this->cache->save('double_submit_' . md5(session_id()), time(), 30);
    }

    /**
     * Check export rate limit
     */
    private function _check_export_rate_limit() {
        $sid = session_id();
        $s_attempts = $this->cache->get('export_limit_session_' . md5($sid)) ?: 0;
        if ($s_attempts >= 3) return false;
        
        $ip = $this->_get_ip_address();
        $ip_attempts = $this->cache->get('export_limit_ip_' . md5($ip)) ?: 0;
        
        return $ip_attempts < 20;
    }

    /**
     * Update export rate limit
     */
    private function _update_export_rate_limit() {
        $sid = session_id();
        $s_attempts = $this->cache->get('export_limit_session_' . md5($sid)) ?: 0;
        $this->cache->save('export_limit_session_' . md5($sid), $s_attempts + 1, $this->cache_ttl['rate_limit']);
        
        $ip = $this->_get_ip_address();
        $ip_attempts = $this->cache->get('export_limit_ip_' . md5($ip)) ?: 0;
        $this->cache->save('export_limit_ip_' . md5($ip), $ip_attempts + 1, $this->cache_ttl['rate_limit']);
    }

    // ============================================================================
    // DATA HELPERS
    // ============================================================================

    /**
     * Get pertanyaan untuk validasi
     */
    private function _get_pertanyaan_for_validation() {
        $cache_key = 'pendataan/questions_validation_v2';
        $pertanyaan = $this->cache->get($cache_key);
        
        if ($pertanyaan === FALSE) {
            $pertanyaan = [];
            foreach ($this->db->where('aktif', 1)->order_by('section, urutan')->get('pertanyaan_koleksi')->result_array() as $row) {
                $pertanyaan[$row['section']][] = $row;
            }
            $this->cache->save($cache_key, $pertanyaan, $this->cache_ttl['questions']);
        }
        
        return $pertanyaan;
    }

    /**
     * Get section names
     */
    private function get_section_names() {
        return [
            1 => 'KELEMBAGAAN',
            2 => 'DATA PENGISI',
            3 => 'JUMLAH KOLEKSI',
            4 => 'PENAMBAHAN KOLEKSI',
            5 => 'ANGGARAN',
            6 => 'PEMANFAATAN TERCETAK',
            7 => 'PEMANFAATAN DIGITAL',
            8 => 'PENGUNJUNG',
            9 => 'TEKNOLOGI',
            10 => 'ANGGOTA',
            11 => 'SDM'
        ];
    }

    // ============================================================================
    // CACHE MANAGEMENT
    // ============================================================================

    /**
     * Clear submission cache
     */
    private function clear_submission_cache() {
        $cache_path = APPPATH . 'cache/';
        if (is_dir($cache_path)) {
            foreach (glob($cache_path . '*pendataan+rekap_*') as $file) {
                if (is_file($file)) @unlink($file);
            }
            foreach (glob($cache_path . '*pendataan+detail_*') as $file) {
                if (is_file($file)) @unlink($file);
            }
        }
    }

    // ============================================================================
    // INPUT SANITIZATION & UTILITIES
    // ============================================================================

    /**
     * Sanitize input array
     */
    private function _sanitize_input($input) {
        $sanitized = [];
        foreach ($input as $key => $value) {
            $sanitized[$key] = is_array($value) 
                ? array_map([$this, '_clean_input'], $value) 
                : $this->_clean_input($value);
        }
        return $sanitized;
    }

    /**
     * Clean input value
     */
    private function _clean_input($value) {
        $value = str_replace(chr(0), '', $value);
        $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $value);
        $value = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        return preg_replace('/\s+/', ' ', trim(strip_tags($value)));
    }

    /**
     * Sanitize CSV value untuk export
     */
    private function _sanitize_csv_value($value) {
        $value = str_replace(["\r\n\r\n", "\r\n", "\r", "\n", "\t"], '   ', $value);
        $value = preg_replace(['/\|+/', '/\s+/', '/\s*\|\s*/'], [' ', ' ', '   '], $value);
        $value = trim($value);
        
        // Prevent CSV injection
        if (preg_match('/^[=+\-@]/', $value)) {
            $value = "'" . $value;
        }
        
        return $value;
    }

    /**
     * Get IP address dengan fallback yang aman
     */
    private function _get_ip_address() {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        return '0.0.0.0';
    }
}