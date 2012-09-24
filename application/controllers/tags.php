<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Controller {

    public function index($tag_id) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $attached_events = $this->db->query('SELECT events.id, name FROM `events` INNER JOIN event_tag ON events.id = event_id INNER JOIN tags ON tags.id = tag_id WHERE tags.id = "' . $tag_id . '"');

            if($attached_events->num_rows() > 0) {
                $meta = request_status('tag_query_succeed');
                $data = $attached_events->result();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } else {
                $meta = request_status('tag_no_events');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function add() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        //$data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
            //succeed
            $tag = $_POST['tag'];
            $query_tag = $this->db->query('SELECT id, tag FROM `tags` WHERE tag = "' . $tag . '"');

            if($query_tag->num_rows() == 0) {
                $this->db->query('INSERT INTO tags (tag) VALUES ("' . $tag . '")');
                $added_tag = $this->db->query('SELECT id, tag FROM `tags` WHERE tag = "' . $tag . '"');
                $meta = request_status('tag_add_succeed');
                //$data = $added_tag->row();
            } else {
                $meta = request_status('tag_add_fail');
                //$data = $query_tag->row();
            }
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            //echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

}
