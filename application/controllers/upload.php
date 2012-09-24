<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {
    function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->do_upload();
    }

    private function do_upload() {
        $type = $_POST['type'];
        $index = $_POST['index'];

        if(!file_exists($type . '/' . $index)) {
            @mkdir($type . '/' . $index, 0777, true);
        }
        if(!file_exists($type . '/' . $index . '/index.html')) {
            @copy($type . '/index.html', $type . '/' . $index . '/index.html');
        }

        $config['upload_path'] = $type . '/' . $index;
        $config['allowed_types'] = 'gif|jpeg|jpg|png';
        $config['max_size'] = '5000';
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        $meta;
        $data;

        if (!$this->upload->do_upload()) {
            $meta = request_status('upload_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } else {
            $return = $this->upload->data();

            $thumb_config = array(
                'source_image'    => $return['full_path'],
                'new_image'       => $type . '/',
                'maintain_ratio' => true,
                'width'           => 110,
                'height'          => 110
            );
            $this->load->library('image_lib', $thumb_config);
            $this->image_lib->resize();

            $meta = request_status('upload_succeed');
            $data = array('type' => $type, 'index' => $index, 'filename' => $return['file_name'], 'width' => $return['image_width'], 'height' => $return['image_height'], 'size' => $return['file_size']);
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        }
    }

}
