 <div class="container">
<div class="row">
<div class="col-md-12 center">
     <div class="modal-container" id="modalContainer">
    <div class="modal-content">
      <span class="close" id="closeModal">&otimes;</span>
      <iframe id="videoFrame" width="400" height="700" src="https://esurya.organisasi.jatengprov.go.id/webperpustakaanarpus" frameborder="3" allowfullscreen></iframe>
    </div>
  </div>
</div>
</div>
</div>

<div style="margin-bottom:80px">
	<?php include "slide.php"; ?>
</div><!-- /.container -->

<div class="container">
<div class="row">
<div class="col-md-12 center">
<div class="heading heading-border heading-middle-border heading-middle-border-center">
<h2 class="mb-lg">LINK <strong>TERKAIT</strong></h2>
</div>
<iframe src="sliderlogo/" width="100%" frameBorder="0"></iframe>

</div>
</div>
</div>

<section class="section section-no-background m-none">
	<div class="container">
		<div class="row">
			
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 welcome padding-left-none padding-bottom-40 scroll_effect fadeInUp">
				<h2 class="margin-bottom-25 margin-top-none">BERITA <strong>TERBARU</strong></h2>
				<div id="myCarousel" class="carousel slide" data-ride="carousel">
					<ol class="carousel-indicators">
					<?php
					
					foreach ($terbaru->result_array() as $ban1){ 
						if($no == 0){
						?>
							<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
						<?php
						}else{
						?>
							<li data-target="#myCarousel" data-slide-to="<?php echo $no; ?>" class=""></li>
						<?php
						}
						$no++;
					}
					?>
					</ol>
					<!-- Wrapper for slides -->
					<div class="carousel-inner">
					<?php
					$no = 0;
					foreach ($terbaru->result_array() as $ban2){ 
						if($no == 0){
						?>
							<div class="item active"><a href="#" target ="_blank"><img  class="max-height:270px;" src="asset/foto_berita/<?php echo $ban2['gambar']; ?>" alt="<?php echo $ban2['judul'];?>" caption="false"/>
							
							<div class="carousel-caption animated fadeInLeft" style="top:73%;width:100%;>
								<span  data-animation="animated bounceInLeft" style="bottom: 30px; position: fixed; min-width: 350px;left: 20px;">
									<?php echo "<a style='color:white;' href='" . base_url() . "berita/detail/$ban2[judul_seo]'>" . $ban2['judul'] . "</a>";?>
								</span>
							</div>
							
							</a></div>
						<?php
						}else{
						?>
							<div class="item"><a href="#" target ="_blank"><img  class="max-height:270px;" src="asset/foto_berita/<?php echo $ban2['gambar']; ?>" alt="<?php echo $ban2['judul']; ?>" caption="false"/>
							<div class="carousel-caption animated fadeInLeft" style="top:73%;width:100%;">
								<span data-animation="animated bounceInLeft" style="bottom: 30px; position: fixed; min-width: 350px;left: 20px;">
									<?php echo "<a style='color:white;' href='" . base_url() . "berita/detail/$ban2[judul_seo]'>" . $ban2['judul'] . "</a>";?>
								</span>
							</div>
							
							</a></div>
						<?php
						}
						$no++;
						
					}
					?>
						   
					</div>
				<!-- Left and right controls --> 
				<a class="left carousel-control" href="#myCarousel" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span> <span class="sr-only">Previous</span> </a> <a class="right carousel-control" href="#myCarousel" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span> <span class="sr-only">Next</span> </a>
			</div>
		</div>
		
		<div class="col-md-3" data-wow-delay=".4s">
			<h2 class="mb-lg">PENGUMUMAN</h2>
			<?php
			 foreach ($pengumuman->result_array() as $h) {
			?>
					<div class="recent-posts">
						<article class="post">
							<p><span><i class="fa fa-volume-up"></i></span> &nbsp;<?php echo $h['info']; ?></p>
						</article>
					</div>
			<?php
			 }
			?>
		</div>
		
		<div class="col-md-4" data-wow-delay=".4s">
			<h2 class="mb-lg">AGENDA <strong>KEGIATAN</strong></h2>
			<?php
			 foreach ($agenda->result_array() as $h) {
			?>
					<div class="recent-posts">
						<article class="post">
							<div class="post-meta">
								<span><i class="fa fa-calendar"></i> <?php echo $h['tgl_mulai']; ?> </span>
							</div>
							<h5><a href="<?php echo base_url() . "agenda/detail/" . $h['tema_seo']; ?>"><?php echo $h['tema']; ?></a></h5>
						</article>
					</div>
			<?php
			 }
			?>
		</div>
		</div>
	</div>
</section>


<section class="section section-no-background m-none">
	<div class="container">
		<div class="row">
			
			
		
		<div class="col-md-4" data-wow-delay=".4s">
			<h2 class="mb-lg">WISATA <strong>JATENG</strong></h2>
			<?php
			 foreach ($berita1->result_array() as $h) {
			?>
					<div class="recent-posts">
					<div class="col-md-4">
					<a href="berita/detail/<?php echo $h['judul_seo']; ?>"><img  style="width:100%; min-height:70px;" src="<?php echo base_url() . "asset/foto_berita/" . $h['gambar'] ?>" alt="" data-rjs="2"></a>
					</div>
					<div class="col-md-8">
						<article class="post">
							<div class="post-meta">
								<span><i class="fa fa-calendar"></i> <?php echo $h['tanggal'] . " " . $h['jam']; ?> </span>
							</div>
							<p><span><i class="fa fa-volume-up"></i></span> &nbsp;<a style="color:#777;" href="berita/detail/<?php echo $h['judul_seo']; ?>"><?php echo $h['judul']; ?></a></p>
						</article>
					</div>
					</div>
			<?php
			 }
			?>
		</div>
		
		<div class="col-md-4" data-wow-delay=".4s">
			<h2 class="mb-lg">KULINER <strong>JATENG</strong></h2>
			<?php
			 foreach ($berita2->result_array() as $h) {
			?>
					<div class="recent-posts">
					<div class="col-md-4">
					<a href="berita/detail/<?php echo $h['judul_seo']; ?>"><img  style="width:100%; min-height:70px;" src="<?php echo base_url() . "asset/foto_berita/" . $h['gambar'] ?>" alt="" data-rjs="2"></a>
					</div>
					<div class="col-md-8">
						<article class="post">
							<div class="post-meta">
								<span><i class="fa fa-calendar"></i> <?php echo $h['tanggal'] . " " . $h['jam']; ?> </span>
							</div>
							<p><span><i class="fa fa-volume-up"></i></span> &nbsp;<a style="color:#777;" href="berita/detail/<?php echo $h['judul_seo']; ?>"><?php echo $h['judul']; ?></a></p>
						</article>
					</div>
					</div>
			<?php
			 }
			?>
		</div>
		
		<div class="col-md-4" data-wow-delay=".4s">
			<h2 class="mb-lg">BERITA <strong>UMUM</strong></h2>
			<?php
			 foreach ($berita3->result_array() as $h) {
			?>
					<div class="recent-posts">
					<div class="col-md-4">
					<a href="berita/detail/<?php echo $h['judul_seo']; ?>"><img  style="width:100%; min-height:70px;" src="<?php echo base_url() . "asset/foto_berita/" . $h['gambar'] ?>" alt="" data-rjs="2"></a>
					</div>
					<div class="col-md-8">
						<article class="post">
							<div class="post-meta">
								<span><i class="fa fa-calendar"></i> <?php echo $h['tanggal'] . " " . $h['jam']; ?> </span>
							</div>
							<p><span><i class="fa fa-volume-up"></i></span> &nbsp;<a style="color:#777;" href="berita/detail/<?php echo $h['judul_seo']; ?>"><?php echo $h['judul']; ?></a></p>
						</article>
					</div>
					</div>
			<?php
			 }
			?>
		</div>
		</div>
	</div>
</section>
<div class="clearfix"></div>

<section class="section section-no-background m-none">
	<div class="container">
		<div class="row">
			
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 welcome padding-left-none padding-bottom-40 scroll_effect fadeInUp">
			<h2 class="margin-bottom-25 margin-top-none">GALERI <strong>VIDEO</strong></h2>
			<?php
			foreach($video1->result_array() as $vid1){
			?>
			<iframe width="375" height="210" src="<?php echo str_replace('watch?v=', 'embed/',  $vid1['youtube']); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			<?php
			}
			?>
		</div>
		
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 welcome padding-left-none padding-bottom-40 scroll_effect fadeInUp">
				<h2 class="margin-bottom-25 margin-top-none"> <strong> &nbsp;</strong></h2>
				<?php
				foreach($video2->result_array() as $vid2){
				?>
				<iframe width="375" height="210" src="<?php echo str_replace('watch?v=', 'embed/',  $vid2['youtube']); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				<?php
				}
				?>
		</div>
		
		<div class="col-md-4" data-wow-delay=".4s">
			<h2 class="mb-lg"><strong>POLLING</strong></h2>
			<div class="recent-posts">
				<article class="post">
						<?php /*
									  $t = $this->model_utama->view_where('poling',array('aktif' => 'Y','status' => 'Pertanyaan'))->row_array();
									  echo " <div style='color:#000; font-weight:bold;'>$t[pilihan] <br></div>";
									  echo "<form method=POST action='".base_url()."polling/hasil'>";
										  $pilih = $this->model_utama->view_where('poling',array('aktif' => 'Y','status' => 'Jawaban'));
										  foreach ($pilih->result_array() as $p) {
										  echo "<input class=marginpoling type=radio name=pilihan value='$p[id_poling]'/>
												<class style=\"color:#666;font-size:12px;\">&nbsp;&nbsp;$p[pilihan]<br />";}
										  echo "<br><input style='width: 110px; padding:2px' type=submit class=simplebtn value='PILIH' />
									  </form>
									  <a href='".base_url()."polling'>
									  <input style='width: 110px; padding:2px;' type=button class=simplebtn value='LIHAT HASIL' /></a>";
									*/ ?>
				</article>
			
			
			
		</div>
		</div>
	</div>
</section>
