<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Controller {

    public function index() {

        $timing = $_SERVER['REQUEST_TIME'];
        $status;
        $msg;
        $data;

       if( !client_auth() ) {
           $status = 'ERROR';
           $msg = 'Unauthenticated client. Request failed.';
           echo json_encode(array('status' => $status, 'msg' => $msg));
       } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $tag_id = $_GET['tagid'];
            $attached_events = $this->db->query('SELECT events.id, name FROM `events` INNER JOIN event_tag ON events.id = event_id INNER JOIN tags ON tags.id = tag_id WHERE tags.id = "' . $tag_id . '"');
            // in development
            foreach($attached_events->result() as $result) {
                echo $result->id . ' : ' . $result->name . '<br>';
            }
       } else {
            // create new tag
       }

    }

}
