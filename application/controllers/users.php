<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

    public function index() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page = $_GET['page'];
            $user_list = $this->db->query('SELECT id, username, avatar_url FROM `users` ORDER BY username ASC LIMIT ' . ($page - 1) * 25 . ', 25');
            $meta = request_status('user_list_succeed');
            $data = $user_list->result();
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function info($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;
        $self;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
            //修改当前登录用户的个人信息（仅对当前登录用户有效）
            if(is_self($userid, $_POST['username'], $_POST['token'])) {
                //succeed
                $this->db->query('UPDATE users SET email = "' . $_POST['email'] . '", avatar_url = "' . $_POST['avatar_url'] . '" WHERE id = "' . $userid . '"');
                $meta = request_status('info_change_succeed');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            } else {
                $meta = request_status('info_change_fail');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            if(is_self($userid, $_GET['username'], $_GET['token'])) {
                $user_info = $this->db->query('SELECT * FROM `users` WHERE id = "' . $userid . '"');
                $self = true;
            } else {
                $user_info = $this->db->query('SELECT id, username, avatar_url FROM `users` WHERE id = "' . $userid . '"');
                $self = false;
            }

            if($user_info->num_rows() == 0) {
                $meta = request_status('info_get_fail');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'self' => $self));
            } else {
                $meta = request_status('info_get_succeed');
                $data = $user_info->result();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data, 'self' => $self));
            }
        }
    }

    public function organizing($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;
        $self;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $organizing = $this->db->query('SELECT events.id, name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled FROM `events` INNER JOIN `event_organizer` ON events.id = event_organizer.event_id WHERE event_organizer.organizer_id = ' . $userid);

            $data = $organizing->result();
            $meta = request_status('info_get_succeed');
            $self = is_self($userid, $_GET['username'], $_GET['token']);
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data, 'self' => $self));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function attending($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;
        $self;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            if(is_self($userid, $_GET['username'], $_GET['token'])) {
                $attending = $this->db->query('SELECT events.id, name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled FROM `events` INNER JOIN `event_attendee` ON events.id = event_attendee.event_id WHERE event_attendee.attendee_id = ' . $userid . ' AND event_attendee.attendee_status = "ok"');
                $self = true;
            } else {
                $attending = $this->db->query('SELECT events.id, name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled FROM `events` INNER JOIN `event_attendee` ON events.id = event_attendee.event_id WHERE event_attendee.attendee_id = ' . $userid);
                $self = false;
            }
            $data = $attending->result();
            $meta = request_status('info_get_succeed');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data, 'self' => $self));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function watching($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;
        $self;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $watching = $this->db->query('SELECT events.id, name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled FROM `events` INNER JOIN `event_watcher` ON events.id = event_watcher.event_id WHERE event_watcher.watcher_id = ' . $userid);

            $data = $watching->result();
            $self = is_self($userid, $_GET['username'], $_GET['token']);
            $meta = request_status('info_get_succeed');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data, 'self' => $self));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function avatar($userid) {
        //users/(id)/avatar          POST    上传头像图片（仅对当前登录用户有效）    
    }

    public function password($userid) {
        //users/(id)/password        POST    修改密码（仅对当前登录用户有效）    
    }

}
