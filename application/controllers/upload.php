<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	public function index()
	{
		//$this->load->view('upload_form', array('error' => ''));
		$this->do_upload();
	}

	private function do_upload()
	{
		$this->load->helper('url');
		
		$config['upload_path'] = 'uploads/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size'] = '5000';
		$config['encrypt_name'] = true;

		$this->load->library('upload', $config);

		$status = "";
		$msg = "";
		
		if (!$this->upload->do_upload())
 		{
  			$status = "error";
  			$msg = array('error' => $this->upload->display_errors());
  			//$this->load->view('upload_form', $error);
 		} else
 		{
  			$status = "success";
  			$data = $this->upload->data();
  			$msg = array('filename' => $data['file_name'], 'width' => $data['image_width'], 'height' => $data['image_height'], 'size' => $data['file_size']);
  			//$this->load->view('upload_success', $data);
 		}

	 	echo json_encode(array('status' => $status, 'msg' => $msg));
	}
	
}

?>