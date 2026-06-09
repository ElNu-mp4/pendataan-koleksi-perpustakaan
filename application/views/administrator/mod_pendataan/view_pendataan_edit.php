<div class="col-xs-12">  
  <div class="box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Edit Data Pendataan Koleksi</h3>
      <a class='pull-right btn btn-default btn-sm' href='<?php echo base_url(); ?>administrator/pendataan'>
        <i class="fa fa-arrow-left"></i> Kembali
      </a>
    </div>
    
    <form method="POST" action="" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?php echo $rows['id_koleksi']; ?>">
      
      <div class="box-body">
        
        <?php if ($this->session->flashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fa fa-ban"></i> <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>
        
        <!-- Info Alert -->
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> 
          Mengubah data untuk ID Koleksi: <strong><?php echo $rows['id_koleksi']; ?></strong> | 
          Nomor Pokok: <strong><?php echo $rows['nomor_pokok']; ?></strong>
        </div>
        
        <!-- Tahun Data -->
        <div class="form-group">
          <label>Tahun Data <span class="text-red">*</span></label>
          <input type="number" name="tahun_data" class="form-control" 
                 value="<?php echo $rows['tahun_data']; ?>" min="2020" max="2099" required>
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
                <?php
                // Get existing value
                $existing_value = isset($jawaban_existing[$p['id_pertanyaan']]) ? $jawaban_existing[$p['id_pertanyaan']] : '';
                ?>
                
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
                          echo "<input type='text' name='{$name}' class='form-control' value='" . htmlspecialchars($existing_value) . "' {$required}>";
                          break;
                          
                      case 'number':
                          echo "<input type='number' name='{$name}' class='form-control' value='" . htmlspecialchars($existing_value) . "' {$required}>";
                          break;
                          
                      case 'textarea':
                          echo "<textarea name='{$name}' class='form-control' rows='4' {$required}>" . htmlspecialchars($existing_value) . "</textarea>";
                          break;
                          
                      case 'select':
                          echo "<select name='{$name}' class='form-control' {$required}>";
                          echo "<option value=''>-- Pilih --</option>";
                          if (isset($p['opsi'])) {
                              foreach ($p['opsi'] as $opsi) {
                                  $selected = ($existing_value == $opsi['nilai_opsi']) ? 'selected' : '';
                                  echo "<option value='{$opsi['nilai_opsi']}' {$selected}>{$opsi['label_opsi']}</option>";
                              }
                          }
                          echo "</select>";
                          break;
                          
                      case 'radio':
                          if (isset($p['opsi'])) {
                              foreach ($p['opsi'] as $opsi) {
                                  $checked = ($existing_value == $opsi['nilai_opsi']) ? 'checked' : '';
                                  echo "<div class='radio'>";
                                  echo "<label>";
                                  echo "<input type='radio' name='{$name}' value='{$opsi['nilai_opsi']}' {$checked} {$required}> ";
                                  echo $opsi['label_opsi'];
                                  echo "</label>";
                                  echo "</div>";
                              }
                          }
                          break;
                          
                      case 'checkbox':
                          $checkbox_name = "pertanyaan[{$p['id_pertanyaan']}][]";
                          $existing_array = explode(', ', $existing_value);
                          if (isset($p['opsi'])) {
                              foreach ($p['opsi'] as $opsi) {
                                  $checked = in_array($opsi['nilai_opsi'], $existing_array) ? 'checked' : '';
                                  echo "<div class='checkbox'>";
                                  echo "<label>";
                                  echo "<input type='checkbox' name='{$checkbox_name}' value='{$opsi['nilai_opsi']}' {$checked}> ";
                                  echo $opsi['label_opsi'];
                                  echo "</label>";
                                  echo "</div>";
                              }
                          }
                          break;
                          
                      case 'date':
                          echo "<input type='date' name='{$name}' class='form-control' value='" . htmlspecialchars($existing_value) . "' {$required}>";
                          break;
                          
                      default:
                          echo "<input type='text' name='{$name}' class='form-control' value='" . htmlspecialchars($existing_value) . "' {$required}>";
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
          <i class="fa fa-save"></i> Update Data
        </button>
        <a href="<?php echo base_url(); ?>administrator/pendataan" class="btn btn-default">
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
    
    // Confirm update
    if (!confirm('Apa anda yakin akan mengupdate data ini?')) {
      e.preventDefault();
      return false;
    }
  });
});
</script>