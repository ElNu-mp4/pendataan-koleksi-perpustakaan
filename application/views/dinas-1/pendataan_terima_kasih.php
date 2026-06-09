<style>
.thank-you-container {
    max-width: 700px;
    margin: 80px auto;
    padding: 40px;
    text-align: center;
}

.thank-you-box {
    background: #fff;
    padding: 50px 30px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.thank-you-icon {
    font-size: 80px;
    color: #5cb85c;
    margin-bottom: 20px;
}

.thank-you-box h2 {
    color: #234885;
    margin-bottom: 20px;
    font-size: 32px;
}

.thank-you-box p {
    color: #666;
    font-size: 16px;
    line-height: 1.8;
    margin-bottom: 15px;
}

.btn-home {
    display: inline-block;
    background: #234885;
    color: white;
    padding: 12px 30px;
    margin-top: 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    transition: background 0.3s;
}

.btn-home:hover {
    background: #006699;
    color: white;
    text-decoration: none;
}

.btn-fill-again {
    display: inline-block;
    background: #f0f0f0;
    color: #333;
    padding: 12px 30px;
    margin-top: 20px;
    margin-left: 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px;
    transition: background 0.3s;
}

.btn-fill-again:hover {
    background: #e0e0e0;
    color: #333;
    text-decoration: none;
}
</style>

<div class="container thank-you-container">
    <div class="thank-you-box">
        <div class="thank-you-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        
        <h2>Terima Kasih!</h2>
        
        <p>
            Data Anda telah berhasil disimpan dan akan digunakan untuk 
            keperluan statistik perpustakaan.
        </p>
        
        <p>
            Kami sangat menghargai waktu dan partisipasi Anda dalam 
            pengisian formulir pendataan koleksi perpustakaan ini.
        </p>
        
        <p>
            <strong>Dinas Perpustakaan dan Kearsipan Provinsi Jawa Tengah</strong>
        </p>
        
        <div style="margin-top: 30px;">
            <a href="<?php echo base_url(); ?>" class="btn-home">
                <i class="fa fa-home"></i> Kembali ke Beranda
            </a>
            
            <!-- Uncomment jika ingin bisa isi lagi -->
            <!-- <a href="<?php echo base_url('pendataan'); ?>" class="btn-fill-again">
                <i class="fa fa-edit"></i> Isi Lagi
            </a> -->
        </div>
    </div>
</div>