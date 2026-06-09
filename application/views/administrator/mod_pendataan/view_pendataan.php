<div class="col-xs-12">  
  <div class="box">

    <!-- BOX HEADER -->
    <div class="box-header">
      <h3 class="box-title">Data Pendataan Koleksi Perpustakaan</h3>

      <?php if (!empty($config)): ?>
        <!-- Status Badge -->
        <span class="label label-<?php echo $config['status_pendataan'] === 'aktif' ? 'success' : 'danger'; ?> pull-right"
              style="margin-left:10px;">
          <i class="fa fa-<?php echo $config['status_pendataan'] === 'aktif' ? 'check-circle' : 'times-circle'; ?>"></i>
          <?php echo $config['status_pendataan'] === 'aktif'
              ? 'PENDATAAN DIBUKA'
              : 'PENDATAAN DITUTUP'; ?>
        </span>
      <?php endif; ?>

      <!-- Export Button -->
      <?php if (!empty($record)): ?>
        <a class="pull-right btn btn-success btn-sm"
           style="margin-right:5px"
           href="<?php echo base_url('administrator/export_pendataan'); ?>">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </a>
      <?php endif; ?>

      <!-- Admin Config Button -->
      <?php if ($this->session->level === 'admin'): ?>
        <a class="pull-right btn btn-warning btn-sm"
           style="margin-right:5px"
           href="<?php echo base_url('administrator/config_pendataan'); ?>">
          <i class="fa fa-cog"></i> Konfigurasi
        </a>
      <?php endif; ?>

      <!-- Add Button (ONLY IF ACTIVE) -->
      <?php if (!empty($config) && $config['status_pendataan'] === 'aktif'): ?>
        <a class="pull-right btn btn-primary btn-sm"
           style="margin-right:5px"
           href="<?php echo base_url('administrator/tambah_pendataan'); ?>">
          <i class="fa fa-plus"></i> Tambahkan Data
        </a>
      <?php endif; ?>
    </div><!-- /.box-header -->

    <!-- BOX BODY -->
    <div class="box-body">

      <!-- ALERT JIKA DITUTUP -->
      <?php if (!empty($config) && $config['status_pendataan'] === 'nonaktif'): ?>
        <div class="alert alert-warning">
          <h4><i class="fa fa-warning"></i> Pendataan Ditutup</h4>
          <p>
            Input data baru untuk tahun
            <strong><?php echo $config['tahun_aktif']; ?></strong>
            saat ini tidak tersedia.
          </p>
          <p><em><?php echo $config['pesan_nonaktif']; ?></em></p>
        </div>
      <?php endif; ?>

      <!-- DATA TABLE -->
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="width:40px">No</th>
            <th>Nomor Pokok</th>
            <th>Nama Perpustakaan</th>
            <th>Tahun Data</th>
            <th>Tanggal Input</th>
            <th>IP Address</th>
            <th>Jumlah Jawaban</th>
            <th style="width:120px">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $no = 1;
          if (empty($record)) {
            echo "<tr><td colspan='8' class='text-center'>Tidak ada data</td></tr>";
          } else {
            foreach ($record as $row) {
              $tanggal = date('d/m/Y H:i', strtotime($row['tanggal_submit']));
              echo "
                <tr>
                  <td>{$no}</td>
                  <td>{$row['nomor_pokok']}</td>
                  <td>{$row['nama_perpustakaan']}</td>
                  <td>{$row['tahun_data']}</td>
                  <td>{$tanggal}</td>
                  <td>{$row['ip_address']}</td>
                  <td class='text-center'>
                    <span class='badge bg-blue'>{$row['jumlah_jawaban']}</span>
                  </td>
                  <td class='text-center'>
                    <a class='btn btn-info btn-xs'
                       href='".base_url("administrator/detail_pendataan/{$row['id_koleksi']}")."'>
                       <i class='glyphicon glyphicon-eye-open'></i>
                    </a>
                    <a class='btn btn-success btn-xs'
                       href='".base_url("administrator/edit_pendataan/{$row['id_koleksi']}")."'>
                       <i class='glyphicon glyphicon-edit'></i>
                    </a>
                    <a class='btn btn-danger btn-xs'
                       href='".base_url("administrator/delete_pendataan/{$row['id_koleksi']}")."'
                       onclick=\"return confirm('Yakin menghapus data ini?')\">
                       <i class='glyphicon glyphicon-remove'></i>
                    </a>
                  </td>
                </tr>";
              $no++;
            }
          }
        ?>
        </tbody>
      </table>

    </div><!-- /.box-body -->
  </div><!-- /.box -->
</div>