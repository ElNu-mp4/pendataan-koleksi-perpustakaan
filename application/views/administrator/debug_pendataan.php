<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Pendataan</title>";
echo "<style>
    body { font-family: monospace; padding: 20px; }
    .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #333; }
    .success { border-left-color: green; }
    .error { border-left-color: red; background: #ffe0e0; }
    .warning { border-left-color: orange; background: #fff8dc; }
    pre { background: white; padding: 10px; overflow: auto; }
    h2 { margin-top: 0; }
</style>";
echo "</head><body>";

echo "<h1>🔍 Debug Pendataan System</h1>";

// 1. Check Database Connection
echo "<div class='section'>";
echo "<h2>1. Database Connection</h2>";
try {
    $db = $this->db;
    echo "<p class='success'>✓ Database connected successfully</p>";
    echo "<p>Database: " . $this->db->database . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 2. Check config_pendataan table
echo "<div class='section'>";
echo "<h2>2. Config Pendataan Table</h2>";
try {
    $config = $this->db->get('config_pendataan')->result_array();
    echo "<p class='success'>✓ Table exists</p>";
    echo "<p>Records found: " . count($config) . "</p>";
    echo "<pre>" . print_r($config, true) . "</pre>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 3. Check identitas_input_koleksi table
echo "<div class='section'>";
echo "<h2>3. Identitas Input Koleksi Table</h2>";
try {
    $query = "SELECT * FROM identitas_input_koleksi LIMIT 5";
    $result = $this->db->query($query)->result_array();
    echo "<p class='success'>✓ Table exists</p>";
    echo "<p>Sample records: " . count($result) . "</p>";
    
    if (!empty($result)) {
        echo "<p><strong>Columns in table:</strong></p>";
        echo "<pre>" . implode(", ", array_keys($result[0])) . "</pre>";
        echo "<p><strong>First record:</strong></p>";
        echo "<pre>" . print_r($result[0], true) . "</pre>";
    } else {
        echo "<p class='warning'>⚠ No records found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Check jawaban_koleksi table
echo "<div class='section'>";
echo "<h2>4. Jawaban Koleksi Table</h2>";
try {
    $query = "SELECT * FROM jawaban_koleksi LIMIT 5";
    $result = $this->db->query($query)->result_array();
    echo "<p class='success'>✓ Table exists</p>";
    echo "<p>Sample records: " . count($result) . "</p>";
    
    if (!empty($result)) {
        echo "<p><strong>Columns in table:</strong></p>";
        echo "<pre>" . implode(", ", array_keys($result[0])) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Test the actual query from your controller
echo "<div class='section'>";
echo "<h2>5. Testing Main Query</h2>";
try {
    $tahun = date('Y');
    
    $this->db->select('i.*, COUNT(j.id_jawaban) as jumlah_jawaban');
    $this->db->from('identitas_input_koleksi i');
    $this->db->join('jawaban_koleksi j', 'i.id_koleksi = j.id_koleksi', 'left');
    $this->db->where('i.tahun_data', $tahun);
    $this->db->group_by('i.id_koleksi');
    $this->db->order_by('i.tanggal_submit', 'DESC');
    
    $record = $this->db->get()->result_array();
    
    echo "<p class='success'>✓ Query executed successfully</p>";
    echo "<p>Records found for year {$tahun}: " . count($record) . "</p>";
    
    if (!empty($record)) {
        echo "<p><strong>Columns returned:</strong></p>";
        echo "<pre>" . implode(", ", array_keys($record[0])) . "</pre>";
        
        echo "<p><strong>First record data:</strong></p>";
        echo "<pre>" . print_r($record[0], true) . "</pre>";
        
        // Test nama_perpustakaan lookup
        $test_id = $record[0]['id_koleksi'];
        $nama = $this->db
            ->select('jawaban')
            ->where('id_koleksi', $test_id)
            ->where('id_pertanyaan', 2)
            ->get('jawaban_koleksi')
            ->row_array();
        
        echo "<p><strong>Nama Perpustakaan lookup test:</strong></p>";
        echo "<pre>" . print_r($nama, true) . "</pre>";
    } else {
        echo "<p class='warning'>⚠ No records found for year {$tahun}</p>";
        
        // Check other years
        $all_years = $this->db
            ->select('DISTINCT(tahun_data) as tahun, COUNT(*) as count')
            ->from('identitas_input_koleksi')
            ->group_by('tahun_data')
            ->order_by('tahun_data', 'DESC')
            ->get()
            ->result_array();
        
        echo "<p><strong>Available years:</strong></p>";
        echo "<pre>" . print_r($all_years, true) . "</pre>";
    }
    
    echo "<p><strong>SQL Query:</strong></p>";
    echo "<pre>" . $this->db->last_query() . "</pre>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Check session
echo "<div class='section'>";
echo "<h2>6. Session Information</h2>";
echo "<pre>";
print_r($this->session->userdata());
echo "</pre>";
echo "</div>";

// 7. Final record structure test
echo "<div class='section'>";
echo "<h2>7. Final Record Structure for DataTables</h2>";
if (!empty($record)) {
    foreach ($record as &$row) {
        $nama = $this->db
            ->select('jawaban')
            ->where('id_koleksi', $row['id_koleksi'])
            ->where('id_pertanyaan', 2)
            ->get('jawaban_koleksi')
            ->row_array();
        
        $row['nama_perpustakaan'] = $nama ? $nama['jawaban'] : $row['nomor_pokok'];
    }
    
    echo "<p class='success'>✓ Records processed</p>";
    echo "<p><strong>Final columns available:</strong></p>";
    echo "<pre>" . implode(", ", array_keys($record[0])) . "</pre>";
    
    echo "<p><strong>Expected columns for DataTables:</strong></p>";
    echo "<pre>0: No (generated)
1: nomor_pokok
2: nama_perpustakaan
3: tahun_data
4: tanggal_submit
5: ip_address
6: jumlah_jawaban
7: Action (buttons)</pre>";
    
    echo "<p><strong>First complete record:</strong></p>";
    echo "<pre>" . print_r($record[0], true) . "</pre>";
}
echo "</div>";

echo "</body></html>";
?>