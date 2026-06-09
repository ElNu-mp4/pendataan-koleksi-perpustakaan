<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Main extends CI_Controller {
	public function index(){
		$data['title'] = title();
		$data['description'] = description();
		$data['keywords'] = keywords();
		
		$data['agenda'] = $this->model_utama->view_join('agenda','users','username','id_agenda','DESC',0,4);
		$data['pengumuman'] = $this->model_utama->view_where_ordering_limit('sekilasinfo',"aktif='Y'",'id_sekilas','DESC',0,3);
		$data['video1'] = $this->model_utama->view_ordering_limit('video','id_video','DESC',0,1);  
		$data['video2'] = $this->model_utama->view_ordering_limit('video','id_video','DESC',1,1);  
		$data['terbaru'] = $this->model_utama->view_where_ordering_limit('berita',"status='Y'",'id_berita','DESC',0,5);
		
		$data['berita1'] = $this->model_utama->view_where_ordering_limit('berita',array('id_kategori'=>61,'status'=>'Y'),'id_berita','DESC',0,5);
		$data['berita2'] = $this->model_utama->view_where_ordering_limit('berita',array('id_kategori'=>62,'status'=>'Y'),'id_berita','DESC',0,5);
		$data['berita3'] = $this->model_utama->view_where_ordering_limit('berita',array('id_kategori'=>63,'status'=>'Y'),'id_berita','DESC',0,5);
		$data['pertanyaan'] = $this->model_utama->view_where_ordering_limit('poling',"status='Pertanyaan'",'id_poling','DESC',0,1);
		$data['jawaban'] = $this->model_utama->view_where_ordering_limit('poling',"status='Jawaban'",'id_poling','ASC',0,10);
		
		
		
		$this->template->load(template().'/template',template().'/content',$data);
	}
}
