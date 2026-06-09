<div class="col-xs-12">  
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Tambah Data Pendataan Koleksi</h3>
      <a class='pull-right btn btn-default btn-sm' href='<?php echo base_url(); ?>administrator/pendataan'>
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
    </div>
    
    <form method="POST" action="" enctype="multipart/form-data">
      <div class="box-body">
        
        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fa fa-ban"></i> <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>
        
        <!-- Tahun Data -->
        <div class="form-group">
          <label>Tahun Data <span class="text-red">*</span></label>
          <input type="number" name="tahun_data" class="form-control" 
                 value="<?php echo date('Y'); ?>" min="2020" max="2099" required>
          <small class="text-muted">Tahun periode data yang diinput</small>
        </div>
        
        <hr>
        
        <!-- Loop Sections -->
        <?php foreach ($pertanyaan as $section => $list_pertanyaan): ?>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="panel-title">
                <strong>Section <?php echo $section; ?>:</strong> 
                <?php echo isset($section_names[$section]) ? $section_names[$section] : 'Section ' . $section; ?>
              </h4>
            </div>
            <div class="panel-body">
              
              <?php foreach ($list_pertanyaan as $p): ?>
                <div class="form-group">
                  <label>
                    <?php echo $p['isi_pertanyaan']; ?>
                    <?php if ($p['wajib'] == 1): ?>
                      <span class="text-red">*</span>
                    <?php endif; ?>
                  </label>
                  
                  <?php
                  $name = "pertanyaan[{$p['id_pertanyaan']}]";
                  $required = ($p['wajib'] == 1) ? 'required' : '';
                  
                  switch ($p['tipe_jawaban']) {
                      case 'text':
                          echo "<input type='text' name='{$name}' class='form-control' {$required}>";
                          break;
                          
                      case 'number':
                          echo "<input type='number' name='{$name}' class='form-control' {$required}>";
                          break;
                          
                      case 'textarea':
                          echo "<textarea name='{$name}' class='form-control' rows='4' {$required}></textarea>";
                          break;
                          
                      case 'select':
                          echo "<select name='{$name}' class='form-control' {$required}>";
                          echo "<option value=''>-- Pilih --</option>";
                          if (isset($p['opsi'])) {
                              foreach ($p['opsi'] as $opsi) {
                                  echo "<option value='{$opsi['nilai_opsi']}'>{$opsi['label_opsi']}</option>";
                              }
                          }
                          echo "</select>";
                          break;
                          
                      case 'radio':
                          if (isset($p['opsi'])) {
                              foreach ($p['opsi'] as $opsi) {
                                  echo "<div class='radio'>";
                                  echo "<label>";
                                  echo "<input type='radio' name='{$name}' value='{$opsi['nilai_opsi']}' {$required}> ";
                                  echo $opsi['label_opsi'];
                                  echo "</label>";
                                  echo "</div>";
                              }
                          }
                          break;
                          
                      case 'checkbox':
                          $checkbox_name = "pertanyaan[{$p['id_pertanyaan']}][]";
                          if (isset($p['opsi'])) {
                              foreach ($p['opsi'] as $opsi) {
                                  echo "<div class='checkbox'>";
                                  echo "<label>";
                                  echo "<input type='checkbox' name='{$checkbox_name}' value='{$opsi['nilai_opsi']}'> ";
                                  echo $opsi['label_opsi'];
                                  echo "</label>";
                                  echo "</div>";
                              }
                          }
                          break;
                          
                      case 'date':
                          echo "<input type='date' name='{$name}' class='form-control' {$required}>";
                          break;
                          
                      default:
                          echo "<input type='text' name='{$name}' class='form-control' {$required}>";
                  }
                  ?>
                  
                  <?php if (!empty($p['keterangan'])): ?>
                    <small class="text-muted"><?php echo $p['keterangan']; ?></small>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
              
            </div>
          </div>
        <?php endforeach; ?>
        
      </div>
      
      <div class="box-footer">
        <button type="submit" name="submit" class="btn btn-primary">
          <i class="fa fa-save"></i> Simpan Data
        </button>
        <a href="<?php echo base_url(); ?>administrator/admin_pendataan" class="btn btn-default">
          <i class="fa fa-times"></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>

<script>
$(function() {
  // Validation
  $('form').submit(function(e) {
    var isValid = true;
    var errorMsg = '';
    
    // Check required fields
    $(this).find('[required]').each(function() {
      if ($(this).is(':checkbox') || $(this).is(':radio')) {
        var name = $(this).attr('name');
        if ($('input[name="' + name + '"]:checked').length === 0) {
          isValid = false;
          errorMsg = 'Mohon isi semua field yang wajib diisi (bertanda *)';
          return false;
        }
      } else if ($(this).val() === '' || $(this).val() === null) {
        isValid = false;
        errorMsg = 'Mohon isi semua field yang wajib diisi (bertanda *)';
        return false;
      }
    });
    
    if (!isValid) {
      e.preventDefault();
      alert(errorMsg);
      return false;
    }
  });
});
</script>