<div class="container survey-container">
    <div class="row">
        <div class="col-md-12">
            
            <!-- Back Button -->
            <div style="margin-bottom: 20px;">
                <a href="<?php echo site_url('pendataan/rekap'); ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali ke Rekap
                </a>
            </div>
            
            <!-- Page Header -->
            <div class="survey-intro">
                <h4>Detail Pendataan Koleksi Perpustakaan</h4>
                <p>
                    Detail lengkap data pendataan yang telah diinput.
                </p>
            </div>
            
            <!-- Identitas Section -->
            <div class="section-header">
                INFORMASI PENDATAAN
            </div>
            
            <div class="question-item">
                <table class="info-table">
                    <tr>
                        <td width="200"><strong>ID Pendataan</strong></td>
                        <td>: <?php echo $identitas['id_koleksi']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Perpustakaan</strong></td>
                        <td>: <span class="highlight"><?php echo $identitas['nama_perpustakaan']; ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Nomor Pokok</strong></td>
                        <td>: <?php echo $identitas['nomor_pokok']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tahun Data</strong></td>
                        <td>: <?php echo $identitas['tahun_data']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Input</strong></td>
                        <td>: <?php echo date('d F Y, H:i:s', strtotime($identitas['tanggal_submit'])); ?></td>
                    </tr>
                </table>
            </div>
            
            <!-- Jawaban per Section -->
            <?php if(!empty($jawaban_grouped)): ?>
                <?php foreach($jawaban_grouped as $section => $jawaban_list): ?>
                    
                    <div class="section-header">
                        <?php echo isset($section_names[$section]) ? $section_names[$section] : 'SECTION ' . $section; ?>
                    </div>
                    
                    <?php foreach($jawaban_list as $jawaban): ?>
                        <div class="question-item">
                            <div class="detail-question">
                                <?php echo $jawaban['isi_pertanyaan']; ?>
                            </div>
                            <div class="detail-answer">
                                <?php 
                                // Format jawaban berdasarkan tipe
                                if($jawaban['tipe_jawaban'] == 'textarea') {
                                    echo nl2br(htmlspecialchars($jawaban['jawaban']));
                                } else {
                                    echo htmlspecialchars($jawaban['jawaban']);
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="question-item">
                    <em>Tidak ada jawaban yang tersimpan.</em>
                </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div class="text-center" style="margin-top: 30px;">
                <a href="<?php echo site_url('pendataan/rekap'); ?>" class="btn-submit">
                    <i class="fa fa-arrow-left"></i> Kembali ke Rekap
                </a>
            </div>
            
        </div>
    </div>
</div>

<style>
.survey-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 20px;
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

.info-table {
    width: 100%;
}

.info-table td {
    padding: 8px 0;
    line-height: 1.6;
}

.highlight {
    color: #234885;
    font-weight: bold;
    font-size: 18px;
}

.detail-question {
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.6;
    padding-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
}

.detail-answer {
    color: #555;
    line-height: 1.8;
    padding: 10px 0;
    font-size: 15px;
}

.btn-back, .btn-submit, .btn-delete-detail {
    padding: 12px 30px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    margin: 0 5px;
}

.btn-back {
    background: #6c757d;
    color: white;
}

.btn-back:hover {
    background: #5a6268;
}

.btn-submit {
    background: #234885;
    color: white;
}

.btn-submit:hover {
    background: #006699;
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
}

.text-center {
    text-align: center;
}
</style>