<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sliderlogo extends CI_Controller {
	public function index(){
		$data['x'] = 1;
		$this->load->view(template().'/slidelogo',$data);
	}

}
