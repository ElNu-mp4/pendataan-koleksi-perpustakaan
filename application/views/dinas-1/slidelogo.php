<script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/css/sliderlogo.css">

<div class="container">
   <section class="customer-logos slider">
   <?php
		$linkterkait = $this->model_utama->view_ordering_limit('iklantengah','id_iklantengah','ASC',0,10);            
		$no = 0;
		foreach ($linkterkait->result_array() as $r){ 
   ?>
      <div class="slide"><a href="<?php echo $r['url'] ;?>"><img src="<?php echo base_url() . "asset/foto_iklantengah/" . $r['gambar'] ?>"></a></div>
   <?php
		}
	?>   
   </section>
</div>

<script>
$(document).ready(function(){
    $('.customer-logos').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 1500,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                slidesToShow: 4
            }
        }, {
            breakpoint: 520,
            settings: {
                slidesToShow: 3
            }
        }]
    });
});
</script>