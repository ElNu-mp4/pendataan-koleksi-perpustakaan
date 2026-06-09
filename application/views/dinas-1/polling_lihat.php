<div class="container">
	<div class="row">
		<div class="main--content col-md-8" data-sticky-content="true">
			<div class="sticky-content-inner">
				<div class="post--item post--single post--title-largest pd--30-0">
							<div class="column9">
								<?php
								echo "<center style='margin-top:5%; margin-bottom:5%;'><h4>Berikut Adalah hasil Perhitungan sementara Poling yang masuk. <br>
											Silahkan untuk selalu mengunjungi halaman ini untuk melihat hasil terbarunya.<br>
											Terima kasih,..</center></h4>";
											
								 echo "<table height=50% style='height:200px; border: 0pt dashed #CCC;padding: 10px;'>";
										  foreach ($polling->result_array() as $s) {
										  $prosentase = sprintf("%2.1f",(($s['rating']/$rows['jml_vote'])*100));
										  $gbr_vote   = $prosentase * 3;
									  		echo "<tr>
												<td width='40%'>
												  <b>$s[pilihan] <span class style=\"color:#EA1C1C;\">($s[rating])</span> </b></td><td width=200> 
												  <img src=".base_url()."asset/images/red.jpg width=$gbr_vote  width='200' height='18' border=0>   
												  <span class style=\"color:#EA1C1C;\"><b>$prosentase % </b> </span> <hr style='margin:3px 0px 3px 0px'>
												</td>
											</tr>";
										  }
								  echo "</table>
								  <br/><h4>Jumlah Pemilih: <class style=\"color:#EA1C1C;\">$rows[jml_vote]</h4>";
								?>
							</div>
						
				</div>
				</div>
				</div>
				 <div class="col-md-3" style="padding-top:25px;">
					<aside class="siderbar">
						<h4 class="heading-primary">Latest News</h4>
						<ul class="nav nav-list mb-xlg">
						<?php
						$pilihan = $this->model_utama->view_join_two('berita','users','kategori','username','id_kategori',array('berita.aktif' => 'Y','status' => 'Y'),'id_berita','DESC',0,8);
						foreach ($pilihan->result_array() as $row) {   
								echo "<li><a href='$row[judul_seo]'>$row[judul]</a></li>";
						}
						?>
						</ul>
						<hr />
						
					</aside>
				</div>
			</div>
		</div>