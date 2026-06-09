<header id="header" class="header-no-border-bottom" data-plugin-options="{'stickyEnabled': true, 'stickyEnableOnBoxed': true, 'stickyEnableOnMobile': true, 'stickyStartAt': 190, 'stickySetTop': '-190px', 'stickyChangeLogo': false}">
    <div class="header-body">
        <div class="header-container container">
            <div class="header-row">
                <div class="header-column">
                    <div style="text-align: center;"> 
						<?php 
							$logo = $this->model_utama->view_ordering_limit('logo','id_logo','DESC',0,1);
							foreach ($logo->result_array() as $row) {
							echo "<img style='width:70px;' src='".base_url()."asset/logo/$row[gambar]'/>";
							}
							?>
						<h4><b><span style=" color: #fff;">PERPUSDA JATENG</span></b></h4>
						<h4><b><span style=" color: #fff;">DINAS KEARSIPAN DAN PERPUSTAKAAN PROVINSI JAWA TENGAH</span></b></h4>
                    </div>
                </div>
				
                
            </div>
        </div>
        <div class="header-container header-nav header-nav-center header-nav-bar header-nav-bar-primary">

            <button class="btn header-btn-collapse-nav" data-toggle="collapse" data-target=".header-nav-main">
                <i class="fa fa-bars"></i>
            </button>
            <div class="header-nav-main header-nav-main-light header-nav-main-effect-1 header-nav-main-sub-effect-1 collapse" style="padding-top:3px;">
                <nav>
                    <ul class="nav nav-pills" id="mainNav">
                        <li class="">
                            <a href="<?php echo base_url(); ?>">
                                <i class="fa fa-home" style="font-size:25px;"></i>
                            </a>
                        </li>
						<?php include "main_menu.php"; ?>
								<a href="<?php echo base_url();?>hubungi/">
									Layanan Aduan Masyarakat
								</a>

							
						</li>
						<li class="hidden-xs hidden-sm" style="float:right;padding:10px 13px;">
						<form method="POST" action="<?php echo base_url();?>berita/index/">
                            <div class="input-group" style="position:absolute; width:150px; right:50px;">
							  <input name="kata" type="text" class="form-control" placeholder="Cari...">
							  <span class="input-group-btn">
								<button name="cari" class="btn btn-default" type="submit">
									<i class="fa fa-search"></i>
								</button>
							  </span>
							</div>
						</form>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</header>