<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Controller {

    public function index($tag_id = NULL) {

        $timing = $_SERVER['REQUEST_TIME'];
        $status;
        $msg;
        $data;

        if( !client_auth() ) {
            $status = 'ERROR';
            $msg = 'Unauthenticated client. Request failed.';
            echo json_encode(array('status' => $status, 'msg' => $msg));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $attached_events = $this->db->query('SELECT events.id, name FROM `events` INNER JOIN event_tag ON events.id = event_id INNER JOIN tags ON tags.id = tag_id WHERE tags.id = "' . $tag_id . '"');

            $status = 'OK';
            $msg = 'Successfully get all the events attached with the queried tag.';
            $data = $attached_events->result();
            echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
        } else {
            $tag = $_POST['tag'];
            $query_tag = $this->db->query('SELECT id, tag FROM `tags` WHERE tag = "' . $tag . '"');

            if($query_tag->num_rows() == 0) {
                $this->db->query('INSERT INTO tags (tag) VALUES ("' . $tag . '")');
                $added_tag = $this->db->query('SELECT id, tag FROM `tags` WHERE tag = "' . $tag . '"');
                $status = 'OK';
                $msg = 'The new tag is successfully added.';
                $data = $added_tag->row();
            } else {
                $status = 'OK';
                $msg = 'The tag you are adding already exists.';
                $data = $query_tag->row();
            }
            echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
        }

    }

}
