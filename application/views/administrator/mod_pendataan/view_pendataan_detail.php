<div class="col-xs-12">  
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Detail Pendataan: <?php echo $identitas['nama_perpustakaan']; ?></h3>
      <div class="pull-right">
        <a class='btn btn-warning btn-sm' href='<?php echo base_url(); ?>administrator/edit_pendataan/<?php echo $identitas['id_koleksi']; ?>'>
          <i class="fa fa-edit"></i> Edit Data
        </a>
        <a class='btn btn-default btn-sm' href='<?php echo base_url(); ?>administrator/pendataan'>
          <i class="fa fa-arrow-left"></i> Kembali
        </a>
      </div>
    </div>
    
    <div class="box-body">
      <!-- Informasi Identitas -->
      <div class="panel panel-info">
        <div class="panel-heading">
          <h4 class="panel-title">Informasi Input</h4>
        </div>
        <div class="panel-body">
          <table class="table table-bordered">
            <tr>
              <th width="200">ID Koleksi</th>
              <td><?php echo $identitas['id_koleksi']; ?></td>
            </tr>
            <tr>
              <th>Nomor Pokok</th>
              <td><?php echo $identitas['nomor_pokok']; ?></td>
            </tr>
            <tr>
              <th>Nama Perpustakaan</th>
              <td><strong><?php echo $identitas['nama_perpustakaan']; ?></strong></td>
            </tr>
            <tr>
              <th>Tahun Data</th>
              <td><?php echo $identitas['tahun_data']; ?></td>
            </tr>
            <tr>
              <th>Tanggal Input</th>
              <td><?php echo date('d F Y, H:i:s', strtotime($identitas['tanggal_submit'])); ?></td>
            </tr>
            <tr>
              <th>IP Address</th>
              <td><?php echo $identitas['ip_address']; ?></td>
            </tr>
            <tr>
              <th>Session ID</th>
              <td><small><?php echo $identitas['session_id']; ?></small></td>
            </tr>
          </table>
        </div>
      </div>
      
      <!-- Jawaban Grouped by Section -->
      <?php foreach ($jawaban_grouped as $section => $jawaban_list): ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title">
              <strong>Section <?php echo $section; ?>:</strong> 
              <?php echo isset($section_names[$section]) ? $section_names[$section] : 'Section ' . $section; ?>
            </h4>
          </div>
          <div class="panel-body">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th width="50%">Pertanyaan</th>
                  <th>Jawaban</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($jawaban_list as $j): ?>
                  <tr>
                    <td><?php echo $j['isi_pertanyaan']; ?></td>
                    <td>
                      <?php 
                        if ($j['tipe_jawaban'] == 'textarea') {
                            echo nl2br(htmlspecialchars($j['jawaban']));
                        } else {
                            echo htmlspecialchars($j['jawaban']);
                        }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>
      
      <?php if (empty($jawaban_grouped)): ?>
        <div class="alert alert-warning">
          <i class="fa fa-warning"></i> Tidak ada jawaban yang tersimpan untuk data ini.
        </div>
      <?php endif; ?>
      
    </div>
    
    <div class="box-footer">
      <a class='btn btn-warning' href='<?php echo base_url(); ?>administrator/edit_pendataan/<?php echo $identitas['id_koleksi']; ?>'>
        <i class="fa fa-edit"></i> Edit Data
      </a>
      <a class='btn btn-danger' 
         href='<?php echo base_url(); ?>administrator/delete_pendataan/<?php echo $identitas['id_koleksi']; ?>' 
         onclick="return confirm('Apa anda yakin untuk hapus Data ini?')">
        <i class="fa fa-trash"></i> Hapus Data
      </a>
      <a class='btn btn-default pull-right' href='<?php echo base_url(); ?>administrator/pendataan'>
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar
      </a>
    </div>
  </div>
</div>