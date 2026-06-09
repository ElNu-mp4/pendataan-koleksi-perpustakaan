<style>
    .pendataan-closed-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
        padding: 20px;
        background: #f8f9fa;
    }
    
    .pendataan-closed-box {
        background: #fff;
        padding: 50px 40px;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        text-align: center;
        max-width: 520px;
        width: 100%;
    }
    
    .icon-container {
        width: 100px;
        height: 100px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lock-icon {
        width: 50px;
        height: 50px;
        fill: #ff9800;
    }
    
    .pendataan-closed-box h2 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 26px;
        font-weight: 600;
    }
    
    .pendataan-closed-box p {
        color: #5a6c7d;
        line-height: 1.7;
        margin-bottom: 30px;
        font-size: 15px;
    }
    
    .pendataan-closed-box .btn {
        display: inline-block;
        padding: 13px 40px;
        background: #234885;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(35, 72, 133, 0.2);
    }
    
    .pendataan-closed-box .btn:hover {
        background: #1a3a6b;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(35, 72, 133, 0.3);
    }
    
    @media (max-width: 768px) {
        .pendataan-closed-box {
            padding: 40px 30px;
        }
        
        .icon-container {
            width: 80px;
            height: 80px;
        }
        
        .lock-icon {
            width: 40px;
            height: 40px;
        }
    }
</style>

<div class="pendataan-closed-wrapper">
    <div class="pendataan-closed-box">
        <div class="icon-container">
            <svg class="lock-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
            </svg>
        </div>
        <h2>Pendataan Sedang Ditutup</h2>
        <p><?= $pesan ?? 'Pendataan saat ini belum dibuka. Silakan kembali lagi nanti.' ?></p>
        <a href="<?= base_url(); ?>" class="btn">Kembali ke Beranda</a>
    </div>
</div>