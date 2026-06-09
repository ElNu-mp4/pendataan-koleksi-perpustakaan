# Library Data Collection System - Sequence Diagram Documentation

## Overview

This document explains the complete **PlantUML Sequence Diagram** for the Library Data Collection System. The diagram covers 5 critical user workflows across the web application, showing interactions between the Browser (frontend), Server/Router, Controller, Model, and Database layers.

---

## Diagram Structure

### Participants (Left to Right)

1. **User** - Human actor (Library staff or Admin)
2. **Browser** - Frontend (HTML/CSS/JavaScript, fetch API)
3. **Web Server** - Apache/Nginx receiving HTTP requests
4. **Router** - CodeIgniter routing system (routes.php)
5. **Controller** - `Pendataan` controller (MVC Controller layer)
6. **Model** - Database access layer (CI_Model)
7. **Database** - MySQL database server

### Message Types

- **→** Synchronous call (HTTP request, function call, SQL query)
- **←** Return message (HTTP response, function return, query result)
- **activate / deactivate** - Shows when each participant is processing

---

## Flow 1: User Authentication (Login)

### Scenario
User submits login credentials through an HTML form.

### Steps

| Step | Participant | Action | Details |
|------|-------------|--------|---------|
| 1-2 | User → Browser | Click Login Button | Form submission |
| 2-3 | Browser → Server | POST /login | Send credentials |
| 3-4 | Server → Router | Route request | HTTP routing |
| 4-5 | Router → Controller | Call login() | Instantiate controller |
| 5-6 | Controller → Model | Check credentials() | Query user table |
| 6-7 | Model → Database | SELECT user | Execute SQL query |

### Security Checks

```php
// Controller validates:
✓ CSRF token
✓ HTTP method (POST only)
✓ Input sanitization (xss_clean)
✓ Request from legitimate source
```

### Database Query

```sql
-- Step 6-7: Fetch user by username
SELECT id, username, password_hash, role, email
FROM users
WHERE username = ?
LIMIT 1
```

### Response Handling

**If credentials match:**
```
Database → Model → Controller → Browser
Return user object → Create session → Redirect to /dashboard
```

**If credentials don't match:**
```
Database → Model → Controller → Browser
Return FALSE → Send error JSON → Redirect to /login
```

### Session Management

```php
// Controller creates session on success
$_SESSION['user_id']     = $user['id'];
$_SESSION['user_role']   = $user['role'];
$_SESSION['login_time']  = time();

// Browser stores session cookie
Set-Cookie: PHPSESSID=abc123xyz...; Path=/; HttpOnly
```

---

## Flow 2: Library Data Entry (Form Submission)

### Scenario
Operator fills a multi-section survey form and clicks Submit. JavaScript sends AJAX request.

### Key Features

✅ **AJAX Request**
- X-Requested-With: XMLHttpRequest header identifies AJAX
- POST method with form data
- JSON response (no page reload)

✅ **Multi-Step Validation**
1. Honeypot check (detect spam bots)
2. Rate limiting (1 submission per hour per session)
3. Double submission prevention (30-second window)
4. Form field validation
5. Data sanitization

### Steps

| Step | Participant | Security Check | Purpose |
|------|-------------|---|---------|
| 15-16 | Browser → Server | POST /pendataan/submit | Send form data |
| 16-18 | Router → Controller | Validate AJAX request | Check X-Requested-With header |
| 18-19 | Controller | Honeypot check | Detect spam bots (empty website_url) |
| 18-20 | Controller | Rate limit check | Cache lookup for 'rate_limit_session_*' |
| 18-21 | Controller | Double submit check | Cache lookup for 'double_submit_*' |
| 21-25 | Controller | Form validation | Check required, type, length constraints |
| 25-28 | Controller | Input sanitization | Remove null bytes, scripts, event handlers |
| 28-31 | Controller → Model | Begin transaction | START TRANSACTION |
| 31-36 | Model → Database | Insert identitas row | INSERT into identitas_input_koleksi |
| 36-40 | Model → Database | Insert answers (batch) | INSERT into jawaban_koleksi (up to 100 rows) |
| 40-43 | Controller | Finalize submission | Commit transaction, update cache |

### Form Data Structure

```javascript
// Frontend sends:
POST /pendataan/submit
Content-Type: application/x-www-form-urlencoded
X-Requested-With: XMLHttpRequest

Body:
pertanyaan[1]=J4.1.1&
pertanyaan[2]=Perpustakaan%20XYZ&
pertanyaan[15]=5000&
pertanyaan[16]=2500&
pertanyaan[17]=8000&
pertanyaan[18]=3500&
... [up to 100+ fields]
```

### Honeypot Detection

```php
// Hidden form field that spam bots might fill
<input type="text" name="website_url" style="display:none;" />

// Controller checks if empty (legitimate) or filled (bot)
if (!empty($this->input->post('website_url'))) {
    // Bot detected - silently accept but don't save
    log_message('info', 'Honeypot triggered');
    redirect('pendataan/terima_kasih');
}
```

### Rate Limiting

```php
// Check session-based rate limit
$sid = session_id();
$cache_key = 'rate_limit_session_' . md5($sid);
if ($this->cache->get($cache_key) !== FALSE) {
    // User already submitted in last hour - reject
    return false;
}

// Update limit after successful submission
$this->cache->save($cache_key, 1, 3600); // 1 hour TTL
```

### Double Submission Prevention

```php
// 30-second window to prevent accidental double-clicks
$cache_key = 'double_submit_' . md5(session_id());
if ($this->cache->get($cache_key) !== FALSE) {
    // Submission detected within 30 seconds - reject
    return false;
}

// Mark submission
$this->cache->save($cache_key, time(), 30);
```

### Form Validation Rules

```php
foreach ($pertanyaan as $section => $questions) {
    foreach ($questions as $q) {
        $field_name = 'pertanyaan[' . $q['id_pertanyaan'] . ']';
        
        if ($q['wajib'] == 1) {
            // Required field
            $rules[] = 'required';
        }
        
        if ($q['tipe_jawaban'] == 'number') {
            // Numeric validation with range
            $rules[] = 'numeric';
            if (isset($q['min_value'])) 
                $rules[] = 'greater_than_equal_to[' . $q['min_value'] . ']';
            if (isset($q['max_value'])) 
                $rules[] = 'less_than_equal_to[' . $q['max_value'] . ']';
        }
        
        if ($q['tipe_jawaban'] == 'text') {
            // Max length 1000 chars
            $rules[] = 'max_length[1000]';
        }
        
        $this->form_validation->set_rules($field_name, $q['isi_pertanyaan'], implode('|', $rules));
    }
}

if ($this->form_validation->run() == FALSE) {
    return json_error(validation_errors());
}
```

### Input Sanitization

```php
private function _clean_input($value) {
    // Remove null bytes (security)
    $value = str_replace(chr(0), '', $value);
    
    // Remove script tags
    $value = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $value);
    
    // Remove event handlers (onclick, onload, etc.)
    $value = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
    
    // Normalize whitespace
    return preg_replace('/\s+/', ' ', trim(strip_tags($value)));
}
```

### Database Transaction (Atomicity)

```php
// All-or-nothing principle: either all data saves or none
$this->db->trans_begin();

try {
    // Step 1: Insert parent record
    $this->db->insert('identitas_input_koleksi', [
        'nomor_pokok' => '-',           // Will update with actual value
        'tahun_data' => $config['tahun_aktif'],
        'ip_address' => $this->_get_ip_address(),
        'session_id' => session_id()
    ]);
    $id_koleksi = $this->db->insert_id();
    
    // Step 2: Batch insert all answers
    $batch = [];
    foreach ($jawaban_post as $id_pertanyaan => $jawaban) {
        if ($jawaban === '' || $jawaban === null) continue;
        
        if (is_array($jawaban)) $jawaban = implode(', ', $jawaban);
        if (strlen($jawaban) > 1000) $jawaban = substr($jawaban, 0, 1000);
        
        $batch[] = [
            'id_koleksi' => $id_koleksi,
            'id_pertanyaan' => (int)$id_pertanyaan,
            'jawaban' => trim($jawaban)
        ];
    }
    
    if (!empty($batch)) {
        $this->db->insert_batch('jawaban_koleksi', $batch);
    }
    
    // Step 3: Update nomor_pokok
    if ($nomor_pokok) {
        $this->db->update('identitas_input_koleksi', 
            ['nomor_pokok' => $nomor_pokok], 
            ['id_koleksi' => $id_koleksi]);
    }
    
    // Commit if all succeed
    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return json_error('Failed to save');
    }
    
    $this->db->trans_commit();
    
} catch (Exception $e) {
    $this->db->trans_rollback();
    return json_error($e->getMessage());
}
```

### JSON Response

```json
// Success response
{
  "status": "success",
  "id_koleksi": 42,
  "message": "Data submitted successfully"
}

// Frontend JavaScript handles:
if (response.status === "success") {
  // Show thank you message
  // Redirect after 2 seconds
  window.location.href = '/pendataan/terima_kasih';
} else {
  // Show error message
  // Keep form visible for editing
}
```

### Cache Invalidation

After successful submission:
```php
// Clear all related cache entries
foreach (glob(APPPATH . 'cache/*pendataan+rekap_*') as $file) {
    @unlink($file);
}
foreach (glob(APPPATH . 'cache/*pendataan+detail_*') as $file) {
    @unlink($file);
}
```

---

## Flow 3: Data Retrieval (Read / List View)

### Scenario
User opens the recap/list page to view all submitted data.

### Page Load Sequence

| Step | Action | Purpose |
|------|--------|---------|
| 48-51 | Browser → Controller | GET /pendataan/rekap |
| 52-58 | Controller checks cache | Try to load years list from cache |
| 54-57 | Cache miss → Database | SELECT DISTINCT years if not cached |
| 59-60 | Render HTML view | Pass data to template |

### Caching Strategy

```php
// Check cache first (TTL: 1 hour)
$cache_key = 'pendataan/list_tahun';
$list_tahun = $this->cache->get($cache_key);

if ($list_tahun === FALSE) {
    // Cache miss: fetch from database
    $list_tahun = $this->db->select('DISTINCT(tahun_data) as tahun')
        ->from('identitas_input_koleksi')
        ->order_by('tahun_data', 'DESC')
        ->get()
        ->result_array();
    
    // Cache result for 1 hour
    $this->cache->save($cache_key, $list_tahun, 3600);
}
```

### Detail View (Optional)

When user clicks on a record:

```php
// Step 65-75: Load from cache or fetch from DB
$cache_key = 'pendataan/detail_' . $id_koleksi;
$data = $this->cache->get($cache_key);

if ($data === FALSE) {
    // Fetch identitas record
    $identitas = $this->db->where('id_koleksi', $id_koleksi)
        ->get('identitas_input_koleksi')->row_array();
    
    // Fetch all answers with question details
    $this->db->select('j.*, p.isi_pertanyaan, p.section, p.tipe_jawaban');
    $this->db->from('jawaban_koleksi j');
    $this->db->join('pertanyaan_koleksi p', 'j.id_pertanyaan = p.id_pertanyaan');
    $this->db->where('j.id_koleksi', $id_koleksi);
    $this->db->order_by('p.section, p.urutan');
    $jawaban = $this->db->get()->result_array();
    
    // Sanitize output for XSS prevention
    foreach ($jawaban as &$row) {
        $row['jawaban'] = htmlspecialchars($row['jawaban'], ENT_QUOTES, 'UTF-8');
    }
    
    // Group by section for display
    $jawaban_grouped = [];
    foreach ($jawaban as $row) {
        $jawaban_grouped[$row['section']][] = $row;
    }
    
    // Cache for 1 hour
    $data = ['identitas' => $identitas, 'jawaban_grouped' => $jawaban_grouped];
    $this->cache->save($cache_key, $data, 3600);
}
```

---

## Flow 4: Recap & Statistics (Charts with AJAX)

### Scenario
User views the recap page and charts load dynamically via AJAX.

### AJAX Request

```javascript
// Browser-side (fetch API)
fetch('/pendataan/get_chart_stats?tahun=2026&date_from=&date_to=', {
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => response.json())
.then(data => {
    // Initialize charts with returned data
    renderCharts(data);
});
```

### Controller Processing

```php
public function get_chart_stats() {
    // Step 1: Validate AJAX request
    if (!$this->input->is_ajax_request()) {
        return $this->output->set_status_header(403)
            ->set_output(json_encode(['error' => 'Forbidden']));
    }
    
    // Step 2: Sanitize parameters
    $tahun     = $this->security->xss_clean($this->input->get('tahun') ?? date('Y'));
    $nomor     = $this->security->xss_clean($this->input->get('nomor_pokok') ?? '');
    $date_from = $this->security->xss_clean($this->input->get('date_from') ?? '');
    $date_to   = $this->security->xss_clean($this->input->get('date_to') ?? '');
    
    // Step 3: Check cache
    $cache_key = 'chart_stats_v2_' . md5($tahun . $nomor . $date_from . $date_to);
    $cached = $this->cache->get($cache_key);
    if ($cached !== FALSE) {
        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($cached));
    }
    
    // Step 4: Execute aggregation queries
    // ... (see queries below)
    
    // Step 5: Cache and return
    $this->cache->save($cache_key, $result, 300); // 5 min TTL
    return $this->output->set_content_type('application/json')
        ->set_output(json_encode($result));
}
```

### Aggregation Queries

```sql
-- Chart 1: Current Collection (4 numeric questions)
-- IDs: 15=Judul Tercetak, 16=Judul Digital, 17=Eksemplar Tercetak, 18=Eksemplar Digital

SELECT 
  SUM(CAST(j.jawaban AS UNSIGNED)) AS total
FROM jawaban_koleksi j
JOIN identitas_input_koleksi i ON i.id_koleksi = j.id_koleksi
WHERE j.id_pertanyaan = 15
  AND i.tahun_data = 2026
  AND j.jawaban REGEXP '^[0-9]+$'  -- Only numeric values
  [AND other filters...]
;

-- Chart 2: Growth Comparison (2025 vs 2026)
-- 2025: 19, 20, 21, 22 | 2026: 23, 24, 25, 26

SELECT 
  tahun_data,
  SUM(CASE WHEN id_pertanyaan=19 THEN CAST(jawaban AS UNSIGNED) ELSE 0 END) as judul_tercetak,
  SUM(CASE WHEN id_pertanyaan=20 THEN CAST(jawaban AS UNSIGNED) ELSE 0 END) as eksemplar_tercetak,
  ...
FROM jawaban_koleksi j
JOIN identitas_input_koleksi i ON ...
GROUP BY tahun_data
;

-- Chart 5: Distribution by Library Type (question ID 4)
SELECT 
  j.jawaban AS nilai,
  o.label_opsi AS label,
  COUNT(*) AS total
FROM jawaban_koleksi j
JOIN identitas_input_koleksi i ON ...
JOIN opsi_pertanyaan_koleksi o 
  ON o.id_pertanyaan = j.id_pertanyaan 
  AND o.nilai_opsi = j.jawaban
WHERE j.id_pertanyaan = 4
GROUP BY j.jawaban
ORDER BY total DESC
;
```

### Response Structure

```json
{
  "stat_cards": {
    "total_laporan": 1250,
    "total_judul_tercetak": 5432100,
    "total_judul_digital": 2345600,
    "total_anggota_2025": 567890
  },
  "koleksi_saat_ini": {
    "labels": ["Judul Tercetak", "Eksemplar Tercetak", "Judul Digital", "Eksemplar Digital"],
    "data": [5432100, 8765432, 2345600, 3456789]
  },
  "pertumbuhan": {
    "labels": ["Judul Tercetak", "Eksemplar Tercetak", "Judul Digital", "Eksemplar Digital"],
    "data_2025": [5100000, 8500000, 2100000, 3200000],
    "data_2026": [5432100, 8765432, 2345600, 3456789]
  },
  "pengunjung": {
    "labels": ["Onsite", "Online", "Akses Teknologi"],
    "data_2025": [456000, 234000, 123000],
    "data_2026": [512000, 289000, 156000]
  },
  "pemanfaatan": {
    "labels": ["Dibaca di Tempat", "Dipinjam"],
    "data_2025": [234000, 145000],
    "data_2026": [267000, 178000]
  },
  "distribusi_jenis": {
    "labels": ["Perpustakaan Umum", "Perpustakaan Khusus", "Perpustakaan Sekolah"],
    "data": [450, 120, 680]
  }
}
```

### Frontend Chart Rendering

```javascript
// Chart.js library initialization
const ctx = document.getElementById('koleksi-chart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: response.koleksi_saat_ini.labels,
        datasets: [{
            label: 'Koleksi Saat Ini',
            data: response.koleksi_saat_ini.data,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
```

---

## Flow 5: Report / Export (CSV Download)

### Scenario
User requests to export filtered data as CSV file.

### Export Request

```http
POST /pendataan/export_excel HTTP/1.1
Content-Type: application/x-www-form-urlencoded

tahun=2026&nomor_pokok=J&date_from=&date_to=&sort_by=tanggal_desc
```

### Resource Setup

```php
// Step 112: Prepare for large dataset export
@set_time_limit(600);              // 10 minutes
@ini_set('memory_limit', '512M');  // 512MB memory
```

### Rate Limiting for Export

```php
// Step 113: Check export limits
private function _check_export_rate_limit() {
    // Per session: max 3 exports per hour
    $sid = session_id();
    $s_attempts = $this->cache->get('export_limit_session_' . md5($sid)) ?: 0;
    if ($s_attempts >= 3) return false;
    
    // Per IP: max 20 exports per hour
    $ip = $this->_get_ip_address();
    $ip_attempts = $this->cache->get('export_limit_ip_' . md5($ip)) ?: 0;
    return $ip_attempts < 20;
}
```

### HTTP Headers for Download

```php
// Step 126: Set CSV download headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=rekap_pendataan_2026.csv');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";
```

### Streaming Export Strategy

```php
// Step 127-137: Process data in 50-record chunks to manage memory
$chunk_size = 50;
$offset = 0;

while ($offset < $total_records) {
    // Fetch 50 records
    $this->db->limit($chunk_size, $offset);
    $rekap_chunk = $this->db->get('identitas_input_koleksi')->result_array();
    
    if (empty($rekap_chunk)) break;
    
    // Get answers for these 50 records (bulk fetch)
    $id_list = array_column($rekap_chunk, 'id_koleksi');
    $jawaban_chunk = $this->db->where_in('id_koleksi', $id_list)
        ->get('jawaban_koleksi')->result_array();
    
    // Map answers
    $jawaban_by_koleksi = [];
    foreach ($jawaban_chunk as $j) {
        $jawaban_by_koleksi[$j['id_koleksi']][$j['id_pertanyaan']] = $j['jawaban'];
    }
    
    // Write CSV rows
    foreach ($rekap_chunk as $row) {
        $row_data = [
            $row['id_koleksi'],
            htmlspecialchars($row['nomor_pokok']),
            $row['tahun_data'],
            $row['tanggal_submit']
        ];
        
        foreach ($pertanyaan_list as $p) {
            $value = $jawaban_by_koleksi[$row['id_koleksi']][$p['id_pertanyaan']] ?? '';
            $row_data[] = $this->_sanitize_csv_value($value);
        }
        
        fputcsv($output, $row_data);
    }
    
    // Flush output buffer to prevent memory overflow
    if (ob_get_level() > 0) ob_flush();
    flush();
    
    $offset += $chunk_size;
}
```

### CSV Injection Prevention

```php
private function _sanitize_csv_value($value) {
    // Remove newlines and normalize whitespace
    $value = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $value);
    $value = trim($value);
    
    // Prevent CSV injection by prepending quote
    if (preg_match('/^[=+\-@]/', $value)) {
        $value = "'" . $value;  // Formula prefix escaped
    }
    
    return $value;
}
```

### Output Example (CSV Format)

```
ID,Nomor Pokok,Tahun,Tanggal,Jenis Perpustakaan,Nama Perpustakaan,Judul Tercetak,Judul Digital,...
42,J4.1.1,2026,2026-05-05,1,Perpustakaan XYZ,5000,2500,...
43,J4.2.1,2026,2026-05-06,2,Perpustakaan ABC,4500,2200,...
44,J4.3.1,2026,2026-05-07,1,Perpustakaan DEF,5800,2900,...
...
```

---

## Caching Architecture

### Multi-Level Cache TTLs

| Cache Key | TTL | Purpose | Size |
|-----------|-----|---------|------|
| `questions_v2` | 1 day | All Q&A with options | Small |
| `list_tahun` | 1 hour | Available years | Tiny |
| `rekap_*` | 5 min | Recap list (filtered) | Medium |
| `detail_*` | 1 hour | Individual submissions | Medium |
| `chart_stats_v2_*` | 5 min | Aggregated stats | Medium |
| `rate_limit_*` | 1 hour | Rate limit counters | Tiny |
| `double_submit_*` | 30 sec | Double submit prevention | Tiny |
| `export_limit_*` | 1 hour | Export rate counters | Tiny |

### Cache Key Generation

```php
// Deterministic key based on parameters
$cache_key = 'chart_stats_v2_' . md5(
    $tahun . '|' . $nomor . '|' . $date_from . '|' . $date_to
);

// Filter cache:
$filter_hash = md5(
    $tahun . '|' . $nomor . '|' . $date_from . '|' . 
    $date_to . '|' . $sort_by . '|' . $per_page
);
$cache_key = "pendataan/rekap_{$filter_hash}_page_{$page}";
```

---

## Security Features Summary

| Feature | Implementation | Purpose |
|---------|---|---------|
| **Honeypot** | Hidden form field | Detect spam bots |
| **Rate Limiting** | Session + IP-based cache | Prevent brute force attacks |
| **Double Submit** | 30-second window | Prevent accidental duplicates |
| **XSS Prevention** | `xss_clean()` + `htmlspecialchars()` | Sanitize user input |
| **SQL Injection** | Parameterized queries | Prevent SQL attacks |
| **CSRF Protection** | Session tokens | Validate origin of requests |
| **Input Validation** | Form validation library | Ensure data integrity |
| **CSV Injection** | Prefix formula characters | Prevent macro execution |
| **Session Management** | Timeout + regeneration | Secure session handling |
| **Transactions** | Database atomicity | Ensure data consistency |

---

## Performance Optimizations

### Database Indexes

```sql
-- Index for fast lookups
CREATE INDEX idx_koleksi_tahun ON identitas_input_koleksi(tahun_data);
CREATE INDEX idx_koleksi_submit ON identitas_input_koleksi(tahun_data, tanggal_submit);
CREATE INDEX idx_jawaban_koleksi ON jawaban_koleksi(id_koleksi);
CREATE INDEX idx_jawaban_pertanyaan ON jawaban_koleksi(id_pertanyaan);
```

### Query Optimization

- **Selective queries**: Only fetch needed columns
- **Batch operations**: Insert 50+ rows in single statement
- **JOIN optimization**: Use indexed foreign keys
- **GROUP BY**: For aggregations (Chart 5)
- **LIMIT/OFFSET**: For pagination (50 records per page)

### Caching Strategy

- **File-based cache** (default): Suitable for < 1000 concurrent users
- **Memcached upgrade**: For high-traffic scenarios
- **Cache invalidation**: Clear on new submissions
- **TTL tuning**: Balance between freshness and performance

### Streaming Export

- **Chunk processing**: 50 records at a time
- **Buffer flushing**: Prevent memory overflow
- **Time management**: 10-minute timeout for large exports
- **No in-memory array**: Direct output stream to browser

---

## Error Handling

### Controller-Level

```php
// Graceful error responses
if ($this->form_validation->run() == FALSE) {
    return $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => 'error',
            'errors' => validation_errors()
        ]));
}
```

### Transaction Rollback

```php
// Automatic rollback on error
if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
    return json_encode(['error' => 'Database error']);
}
```

### Session Management

```php
// Rate limit exceeded
if (!$this->_check_rate_limit()) {
    $this->session->set_flashdata('error', 
        'You can only submit once per hour');
    redirect('pendataan');
}
```

---

## User Experience Flow

### Submission Success Path

1. User fills form (10-30 fields)
2. Clicks Submit button
3. JavaScript sends AJAX request
4. Loading spinner appears
5. Validation completes (< 1 second)
6. Data inserts to database (< 2 seconds)
7. JSON response received
8. UI shows "Thank You" message
9. Auto-redirect to confirmation page (after 2 seconds)

**Total time: < 5 seconds, no page reload**

### Chart Loading Path

1. User opens /pendataan/rekap
2. Page renders with filter form
3. JavaScript triggers AJAX for charts
4. Loading placeholders shown
5. Aggregation queries execute (< 3 seconds)
6. Charts render with animations
7. User can filter anytime (cached within 5 min)

**Total time: 3-5 seconds per filter, fully responsive**

---

## Future Enhancements

1. **WebSocket for Real-time Updates** - Live data refresh
2. **PDF Export** - In addition to CSV
3. **Email Notifications** - Alert admins of new submissions
4. **Advanced Analytics** - Trend analysis, forecasting
5. **Mobile Optimization** - Responsive design improvements
6. **API Authentication** - JWT tokens for third-party access
7. **Audit Logging** - Track all user actions
8. **Data Encryption** - At-rest and in-transit
9. **Load Balancing** - Horizontal scaling
10. **CDN Integration** - Static asset distribution

---

**Generated:** May 5, 2026  
**System:** Library Data Collection Platform (Dinas Kearsipan dan Perpustakaan Jawa Tengah)  
**Version:** 1.0

