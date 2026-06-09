
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <!-- start: Meta -->
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta name="author" content="perpus.jatengprov.go.id">
    <meta name="robots" content="all,index,follow">
    <meta http-equiv="Content-Language" content="id-ID">
    <meta NAME="Distribution" CONTENT="Global">
    <meta NAME="Rating" CONTENT="General">
    <link rel="canonical" href="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>"/>
    <?php if ($this->uri->segment(1)=='berita' AND $this->uri->segment(2)=='detail'){ $rows = $this->model_utama->view_where('berita',array('judul_seo' => $this->uri->segment(3)))->row_array();
       echo '<meta property="og:title" content="'.$title.'" />
             <meta property="og:type" content="article" />
             <meta property="og:url" content="'.base_url().''.$this->uri->segment(3).'" />
             <meta property="og:image" content="'.base_url().'asset/foto_berita/'.$rows['gambar'].'" />
             <meta property="og:description" content="'.$description.'"/>';
    } ?>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>asset/images/<?php echo favicon(); ?>" />

   
    <!-- Web Fonts  -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/animate/animate.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/simple-line-icons/css/simple-line-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/owl.carousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/owl.carousel/assets/owl.theme.default.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/asset/admin/plugins/datatables/dataTables.bootstrap.css">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/css/theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/css/theme-elements.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/css/theme-blog.css">

    <!-- Skin CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/css/skins/default.css">

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/css/custom.css">

    <!-- Head Libs -->
    <script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/modernizr/modernizr.min.js"></script>

    <!-- Slider Bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/slider-bootstrap/slider-bootstrap.css">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-20166082-2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-20166082-2');
    </script>
    
    <style>
    #closeModal {
        font-size: 24px; /* Ubah ukuran font sesuai kebutuhan */
    }
</style>
    
    
    
</head>

<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/id_ID/sdk.js#xfbml=1&version=v3.2&appId=576455719229966&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>


    <div class="body">
        
       
        
    
	<?php include "header.php"; ?>


<div class="main" role="main">
<?php echo $contents; ?>
</div>

        

<!--Footer Start-->
<footer class="color color-quaternary" id="footer">
<?php
include "footer.php";
?>
</footer>

    </div>

    


  <!-- Vendor -->
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/jquery.appear/jquery.appear.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/jquery.easing/jquery.easing.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/jquery-cookie/jquery-cookie.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/common/common.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/jquery.validation/jquery.validation.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/jquery.gmap/jquery.gmap.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/isotope/jquery.isotope.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/vide/vide.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/owl.carousel/owl.carousel.min.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/slider-bootstrap/slider-bootstrap.js"></script>
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/vendor/dataTables/datatables.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url(); ?>asset/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>asset/admin/plugins/datatables/dataTables.bootstrap.min.js"></script>

<!-- Theme Base, Components and Settings -->
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/js/theme.js"></script>

<!-- Theme Custom -->
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/js/custom.js"></script>

<!-- Theme Initialization Files -->
<script src="<?php echo base_url(); ?>template/<?php echo template(); ?>/js/theme.init.js"></script>

<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
  const modalContainer = document.getElementById("modalContainer");
  const closeModal = document.getElementById("closeModal");
  const body = document.body;

  // Menonaktifkan latar belakang halaman utama saat modal terbuka
  function disableBackground() {
    body.style.overflow = "hidden";
  }

  // Mengaktifkan latar belakang halaman utama setelah modal ditutup
  function enableBackground() {
    body.style.overflow = "visible";
  }

  // Tampilkan pop-up modal saat halaman dimuat
  modalContainer.style.display = "flex";
  disableBackground();

  // Tutup pop-up modal saat tombol 'x' diklik
  closeModal.addEventListener("click", function () {
    modalContainer.style.display = "none";
    enableBackground();
  });

  // Kembali ke halaman utama saat tombol 'Back' dalam iframe diklik
  const videoFrame = document.getElementById("videoFrame");
  videoFrame.addEventListener("load", function () {
    const backButton = videoFrame.contentWindow.document.querySelector(".ytp-button-back");
    if (backButton) {
      backButton.addEventListener("click", function () {
        window.location.href = "http://profile.perpus.jatengprov.go.id/"; // Ganti dengan halaman utama Anda
      });
    }
  });
});

</script>


<script type="text/javascript">
    function langSwitch(content, langId) {
        if (content != '') {
            var url = "/" + langId + "/" + content;
            //console.log(url);
            window.location = url;
        }
        else
        {
            if (langId == "id") {
                alert("Maaf, halaman ini tidak tersedia untuk bahasa inggris");
                return false;
            }
            else
            {
            alert("Sorry, this content not available in English.");
                return false;
            }

       }
    }

    $(document).ready(function(){
        $('#tabel-data').DataTable();
    });

    $('a#btnDownload').each(function () {
        $(this).click(function(e) {
            e.preventDefault();
            //alert('clicked');
            var getdocName = $(this).data('doc');
            var lang = $(this).data('lang');
            console.log(getdocName);
            $.post({
                type: "GET",
                url: window.location.origin + "/Ajax/getDownloadDoc?doc=" + getdocName + "&lang=" + lang,
                //data: { docName: "'" + getdocName + "'" },
                contentType: "application/json",
                success: function (response) {
                    console.log(window.location.origin + response);
                    window.location = window.location.origin + response;
                },
                failure: function (response) {
                    alert('Terjadi kesalahan!');
                }
            });
            
        });
    });
</script>
<?php
$this->model_utama->kunjungan(); 
?>
<!-- Default Statcounter code for Perpustakaan Jawa Tengah
https://perpus.jatengprov.go.id -->
<script type="text/javascript">
var sc_project=12796320; 
var sc_invisible=1; 
var sc_security="76aa0657"; 
</script>
<script type="text/javascript"
src="https://www.statcounter.com/counter/counter.js"
async></script>
<noscript><div class="statcounter"><a title="Web Analytics"
href="https://statcounter.com/" target="_blank"><img
class="statcounter"
src="https://c.statcounter.com/12796320/0/76aa0657/1/"
alt="Web Analytics"
referrerPolicy="no-referrer-when-downgrade"></a></div></noscript>
<!-- End of Statcounter Code -->
</body>
</html>