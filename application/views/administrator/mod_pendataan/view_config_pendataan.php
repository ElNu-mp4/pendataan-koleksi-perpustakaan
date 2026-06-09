<div class="col-xs-12">  
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Konfigurasi Pendataan</h3>
    </div>
    
    <div class="box-body">
      <form method="POST" action="">
        <div class="form-group">
          <label>Tahun Aktif</label>
          <input type="text" name="tahun_aktif" class="form-control" 
                 value="<?php echo $rows['tahun_aktif']; ?>" 
                 placeholder="2025" maxlength="4" required>
          <small class="text-muted">Tahun pendataan yang sedang aktif</small>
        </div>
        
        <div class="form-group">
          <label>Status Pendataan</label>
          <select name="status_pendataan" class="form-control" required>
            <option value="aktif" <?php if($rows['status_pendataan']=='aktif') echo 'selected'; ?>>
              Aktif (Pendataan dibuka)
            </option>
            <option value="nonaktif" <?php if($rows['status_pendataan']=='nonaktif') echo 'selected'; ?>>
              Non-Aktif (Pendataan ditutup)
            </option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Pesan Ketika Non-Aktif</label>
          <textarea name="pesan_nonaktif" class="form-control" rows="4"><?php echo $rows['pesan_nonaktif']; ?></textarea>
          <small class="text-muted">Pesan yang akan ditampilkan kepada user ketika pendataan ditutup</small>
        </div>
        
        <div class="form-group">
          <button type="submit" name="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Simpan Konfigurasi
          </button>
          <a href="<?php echo base_url('administrator/pendataan'); ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
          </a>
        </div>
      </form>
    </div>
  </div>
</div>