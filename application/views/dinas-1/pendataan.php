<!-- Hero Banner -->
<div class="hero-banner">
    <img src="<?php echo base_url('asset/images/Foto Perpus Jateng.jpeg'); ?>" alt="Perpustakaan Jawa Tengah">
    <div class="hero-overlay">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fa fa-book"></i>
            </div>
            <h2>Pendataan Koleksi Perpustakaan Jawa Tengah</h2>
            <p class="hero-description">
                Mohon isi formulir pendataan berikut dengan lengkap dan akurat. 
                Data yang Anda berikan akan digunakan untuk keperluan statistik dan 
                pengembangan perpustakaan di Jawa Tengah.
            </p>
        </div>
    </div>
</div>

<div class="container survey-container">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Alert Messages - SECURED: Output escaping -->
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i>
                    <?php echo htmlspecialchars($this->session->flashdata('success'), ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <!-- Survey Notice -->
            <div class="survey-notice">
                <i class="fa fa-info-circle"></i>
                <strong>Catatan Penting:</strong> Kolom yang bertanda <span class="required-mark">*</span> wajib diisi.
            </div>
            
            <!-- Survey Form - SECURED: No CSRF, using Honeypot instead -->
            <form id="survey-form" method="post" action="<?php echo site_url('pendataan/submit'); ?>">
            
            <?php 
            $current_section = 0;
            foreach($pertanyaan as $section => $questions): 
            ?>
                
                <!-- Section Header - SECURED: section is from controller, safe -->
                <div class="section-header">
                    <?php echo isset($section_names[$section]) ? htmlspecialchars($section_names[$section], ENT_QUOTES, 'UTF-8') : 'SECTION ' . (int)$section; ?>
                </div>
                
                <?php foreach($questions as $q): ?>
                    <div class="question-item">
                        <!-- SECURED: Escape question text -->
                        <label class="question-label">
                            <?php echo htmlspecialchars($q['isi_pertanyaan'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if($q['wajib'] == 1): ?>
                                <span class="required-mark">*</span>
                            <?php endif; ?>
                        </label>
                        
                        <?php
                        // SECURED: Sanitize field identifiers
                        $field_name = 'pertanyaan[' . (int)$q['id_pertanyaan'] . ']';
                        $field_id = 'pertanyaan_' . (int)$q['id_pertanyaan'];
                        $required = ($q['wajib'] == 1) ? 'required' : '';
                        
                        switch($q['tipe_jawaban']):
                            case 'text':
                        ?>
                                <!-- SECURED: Added maxlength, pattern, autocomplete -->
                                <input 
                                    type="text" 
                                    name="<?php echo $field_name; ?>" 
                                    id="<?php echo $field_id; ?>" 
                                    class="form-control"
                                    <?php echo $required; ?>
                                    maxlength="1000"
                                    autocomplete="off"
                                    value="<?php echo htmlspecialchars(set_value($field_name), ENT_QUOTES, 'UTF-8'); ?>"
                                >
                        <?php
                            break;
                            
                            case 'number':
                        ?>
                                <!-- SECURED: Strict number validation -->
                                <input 
                                    type="number" 
                                    name="<?php echo $field_name; ?>" 
                                    id="<?php echo $field_id; ?>" 
                                    class="form-control"
                                    <?php echo $required; ?>
                                    <?php if(isset($q['min_value'])): ?>min="<?php echo (int)$q['min_value']; ?>"<?php endif; ?>
                                    <?php if(isset($q['max_value'])): ?>max="<?php echo (int)$q['max_value']; ?>"<?php endif; ?>
                                    step="1"
                                    value="<?php echo htmlspecialchars(set_value($field_name), ENT_QUOTES, 'UTF-8'); ?>"
                                >
                        <?php
                            break;
                            
                            case 'textarea':
                        ?>
                                <!-- SECURED: Added maxlength -->
                                <textarea 
                                    name="<?php echo $field_name; ?>" 
                                    id="<?php echo $field_id; ?>" 
                                    class="form-control"
                                    <?php echo $required; ?>
                                    rows="4"
                                    maxlength="1000"
                                ><?php echo htmlspecialchars(set_value($field_name), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php
                            break;
                            
                            case 'radio':
                                if(isset($q['opsi']) && !empty($q['opsi'])):
                                    foreach($q['opsi'] as $opsi):
                        ?>
                                        <div class="form-check">
                                            <!-- SECURED: Escape option values and labels -->
                                            <input 
                                                type="radio" 
                                                name="<?php echo $field_name; ?>" 
                                                id="<?php echo $field_id . '_' . (int)$opsi['id_opsi']; ?>" 
                                                value="<?php echo htmlspecialchars($opsi['nilai_opsi'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="form-check-input"
                                                <?php echo $required; ?>
                                                <?php echo set_radio($field_name, $opsi['nilai_opsi']); ?>
                                            >
                                            <label class="form-check-label" for="<?php echo $field_id . '_' . (int)$opsi['id_opsi']; ?>">
                                                <?php echo htmlspecialchars($opsi['label_opsi'], ENT_QUOTES, 'UTF-8'); ?>
                                            </label>
                                        </div>
                        <?php
                                    endforeach;
                                endif;
                            break;
                            
                            case 'checkbox':
                                if(isset($q['opsi']) && !empty($q['opsi'])):
                                    foreach($q['opsi'] as $opsi):
                        ?>
                                        <div class="form-check">
                                            <!-- SECURED: Escape option values and labels -->
                                            <input 
                                                type="checkbox" 
                                                name="<?php echo $field_name; ?>[]" 
                                                id="<?php echo $field_id . '_' . (int)$opsi['id_opsi']; ?>" 
                                                value="<?php echo htmlspecialchars($opsi['nilai_opsi'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="form-check-input"
                                                <?php echo set_checkbox($field_name . '[]', $opsi['nilai_opsi']); ?>
                                            >
                                            <label class="form-check-label" for="<?php echo $field_id . '_' . (int)$opsi['id_opsi']; ?>">
                                                <?php echo htmlspecialchars($opsi['label_opsi'], ENT_QUOTES, 'UTF-8'); ?>
                                            </label>
                                        </div>
                        <?php
                                    endforeach;
                                endif;
                            break;
                            
                            case 'select':
                                if(isset($q['opsi']) && !empty($q['opsi'])):
                        ?>
                                    <select 
                                        name="<?php echo $field_name; ?>" 
                                        id="<?php echo $field_id; ?>" 
                                        class="form-control"
                                        <?php echo $required; ?>
                                    >
                                        <option value="">-- Pilih Salah Satu --</option>
                                        <?php foreach($q['opsi'] as $opsi): ?>
                                            <!-- SECURED: Escape option values and labels -->
                                            <option 
                                                value="<?php echo htmlspecialchars($opsi['nilai_opsi'], ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php echo set_select($field_name, $opsi['nilai_opsi']); ?>
                                            >
                                                <?php echo htmlspecialchars($opsi['label_opsi'], ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                        <?php
                                endif;
                            break;
                        endswitch;
                        ?>
                    </div>
                <?php endforeach; ?>
                
            <?php endforeach; ?>
            
            <!-- Honeypot Field - SECURITY: Bot detection -->
            <div style="position: absolute; left: -9999px;">
                <label for="website_url">Website (leave blank)</label>
                <input type="text" name="website_url" id="website_url" value="" tabindex="-1" autocomplete="off">
            </div>
            
            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn-submit" id="submit-btn">
                    <i class="fa fa-paper-plane"></i> Kirim Data
                </button>
            </div>
            
            </form>
            
        </div>
    </div>
</div>

<script>
// SECURITY: Prevent multiple submissions
var isSubmitting = false;

// SECURITY: Enhanced form validation
document.getElementById('survey-form').addEventListener('submit', function(e) {
    // Prevent double submission
    if (isSubmitting) {
        e.preventDefault();
        return false;
    }
    
    // SECURITY: Check honeypot
    var honeypot = document.getElementById('website_url');
    if (honeypot && honeypot.value !== '') {
        e.preventDefault();
        console.log('Bot detected');
        return false;
    }
    
    var requiredFields = this.querySelectorAll('[required]');
    var isEmpty = false;
    var errorMessages = [];
    
    requiredFields.forEach(function(field) {
        var value = field.value.trim();
        
        if (!value) {
            isEmpty = true;
            field.style.borderColor = '#d9534f';
            errorMessages.push('Kolom "' + getFieldLabel(field) + '" wajib diisi');
        } else {
            field.style.borderColor = '';
            
            // SECURITY: Additional validation for number fields
            if (field.type === 'number') {
                var numValue = parseFloat(value);
                if (isNaN(numValue)) {
                    isEmpty = true;
                    field.style.borderColor = '#d9534f';
                    errorMessages.push('Kolom "' + getFieldLabel(field) + '" harus berisi angka');
                }
                
                if (field.hasAttribute('min') && numValue < parseFloat(field.getAttribute('min'))) {
                    isEmpty = true;
                    field.style.borderColor = '#d9534f';
                    errorMessages.push('Kolom "' + getFieldLabel(field) + '" minimal ' + field.getAttribute('min'));
                }
                
                if (field.hasAttribute('max') && numValue > parseFloat(field.getAttribute('max'))) {
                    isEmpty = true;
                    field.style.borderColor = '#d9534f';
                    errorMessages.push('Kolom "' + getFieldLabel(field) + '" maksimal ' + field.getAttribute('max'));
                }
            }
            
            // SECURITY: Length validation
            if (field.hasAttribute('maxlength')) {
                var maxLength = parseInt(field.getAttribute('maxlength'));
                if (value.length > maxLength) {
                    isEmpty = true;
                    field.style.borderColor = '#d9534f';
                    errorMessages.push('Kolom "' + getFieldLabel(field) + '" maksimal ' + maxLength + ' karakter');
                }
            }
        }
    });
    
    if (isEmpty) {
        e.preventDefault();
        
        // Show first 3 errors
        var displayErrors = errorMessages.slice(0, 3);
        if (errorMessages.length > 3) {
            displayErrors.push('... dan ' + (errorMessages.length - 3) + ' kesalahan lainnya');
        }
        
        alert('Mohon perbaiki kesalahan berikut:\n\n' + displayErrors.join('\n'));
        window.scrollTo({top: 0, behavior: 'smooth'});
        return false;
    }
    
    // SECURITY: Sanitize inputs before submit
    sanitizeFormInputs(this);
    
    // Konfirmasi sebelum submit
    if (!confirm('Apakah anda yakin data yang diisi sudah benar?')) {
        e.preventDefault();
        return false;
    }
    
    // Mark as submitting
    isSubmitting = true;
    
    // Disable submit button
    var submitBtn = document.getElementById('submit-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...';
    }
    
    // Re-enable after 5 seconds (in case of error)
    setTimeout(function() {
        isSubmitting = false;
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Kirim Data';
        }
    }, 5000);
});

// SECURITY: Get field label for error messages
function getFieldLabel(field) {
    var label = field.parentElement.querySelector('.question-label');
    if (label) {
        return label.textContent.replace('*', '').trim();
    }
    return field.name;
}

// SECURITY: Client-side input sanitization
function sanitizeFormInputs(form) {
    var textInputs = form.querySelectorAll('input[type="text"], textarea');
    
    textInputs.forEach(function(input) {
        var value = input.value;
        
        // Remove null bytes
        value = value.replace(/\0/g, '');
        
        // Remove excessive whitespace
        value = value.replace(/\s+/g, ' ').trim();
        
        // Limit length
        if (input.hasAttribute('maxlength')) {
            var maxLength = parseInt(input.getAttribute('maxlength'));
            value = value.substring(0, maxLength);
        }
        
        input.value = value;
    });
}

// Remove red border on input
document.querySelectorAll('.form-control, .form-check-input').forEach(function(element) {
    element.addEventListener('input', function() {
        this.style.borderColor = '';
    });
    element.addEventListener('change', function() {
        this.style.borderColor = '';
    });
});

// SECURITY: Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// SECURITY: Clear form on successful submission (if redirected back)
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        isSubmitting = false;
        var submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Kirim Data';
        }
    }
});
</script>

<style>
/* =========================
   HERO BANNER
   ========================= */

.hero-banner {
    position: relative;
    width: 100%;
    height: 420px;
    overflow: hidden;
    margin-bottom: 40px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.hero-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    filter: brightness(0.7);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(35, 72, 133, 0.92) 0%,
        rgba(25, 52, 103, 0.88) 35%,
        rgba(0, 102, 153, 0.85) 70%,
        rgba(0, 88, 133, 0.90) 100%
    );
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-content {
    text-align: center;
    padding: 30px;
    max-width: 900px;
    color: #ffffff;
    animation: fadeInUp 0.8s ease-out;
}

.hero-icon,
.hero-icon i {
    font-size: 60px;
    margin-bottom: 20px;
    color: #ffffff;
    opacity: 0.95;
}

.hero-icon i {
    animation: pulse 2s ease-in-out infinite;
}

.hero-content h2 {
    font-size: 46px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #ffffff;
    text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.hero-description {
    font-size: 18px;
    line-height: 1.7;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    max-width: 800px;
    margin: 0 auto;
    opacity: 0.95;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Responsive */
@media (max-width: 768px) {
    .hero-banner {
        height: 320px;
    }

    .hero-icon,
    .hero-icon i {
        font-size: 45px;
        margin-bottom: 15px;
    }

    .hero-content h2 {
        font-size: 26px;
        margin-bottom: 15px;
    }

    .hero-description {
        font-size: 15px;
        line-height: 1.6;
    }

    .hero-content {
        padding: 20px;
    }
}

/* Survey Notice Box */
.survey-notice {
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    border-left: 4px solid #ffc107;
    padding: 15px 20px;
    margin-bottom: 30px;
    border-radius: 6px;
    color: #856404;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
}

.survey-notice i {
    margin-right: 8px;
    font-size: 18px;
}

/* Existing Styles */
.survey-container {
    max-width: 900px;
    margin: 0 auto 40px;
    padding: 20px;
}

.survey-intro {
    background: #f0f0f0;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 30px;
}

.survey-intro h4 {
    color: #234885;
    margin-bottom: 10px;
}

.survey-intro p {
    color: #666;
    line-height: 1.6;
    margin-bottom: 10px;
}

.section-header {
    background: #234885;
    color: white;
    padding: 15px 20px;
    margin-top: 30px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 18px;
}

.section-header:first-of-type {
    margin-top: 0;
}

.question-item {
    background: #f9f9f9;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    border-left: 4px solid #234885;
}

.question-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.6;
    display: block;
}

.required-mark {
    color: #d9534f;
    margin-left: 3px;
}

.form-control {
    margin-top: 8px;
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #234885;
    box-shadow: 0 0 0 0.2rem rgba(35, 72, 133, 0.25);
}

.form-check {
    margin-bottom: 8px;
    margin-top: 8px;
}

.form-check-input {
    margin-right: 8px;
    cursor: pointer;
}

.form-check-label {
    margin-left: 5px;
    font-weight: normal;
    cursor: pointer;
    user-select: none;
}

.btn-submit {
    background: #234885;
    color: white;
    padding: 12px 40px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
    transition: background 0.3s;
}

.btn-submit:hover:not(:disabled) {
    background: #006699;
}

.btn-submit:disabled {
    background: #6c757d;
    cursor: not-allowed;
    opacity: 0.65;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-danger {
    background: #f2dede;
    border: 1px solid #ebccd1;
    color: #a94442;
}

.alert-success {
    background: #dff0d8;
    border: 1px solid #d6e9c6;
    color: #3c763d;
}

.text-center {
    text-align: center;
}

/* SECURITY: Hide honeypot field completely */
input[name="website_url"] {
    position: absolute !important;
    left: -9999px !important;
    width: 1px !important;
    height: 1px !important;
    opacity: 0 !important;
}
</style>