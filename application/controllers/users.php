<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

    public function index() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
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

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
            //修改当前登录用户的个人信息（仅对当前登录用户有效）
            if(self_check($userid, $_POST['username'], $_POST['token'])) {
                //succeed
                $this->db->query('UPDATE users SET email = "' . $_POST['email'] . '", avatar_url = "' . $_POST['avatar_url'] . '" WHERE id = "' . $userid . '"');
                $meta = request_status('info_change_succeed');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            } else {
                $meta = request_status('info_change_fail');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            if(self_check($userid, $_GET['username'], $_GET['token'])) {
                $user_info = $this->db->query('SELECT id, username, email, create_time, last_login, current_login, avatar_url FROM `users` WHERE id = "' . $userid . '"');
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
                $data = $user_info->row();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data, 'self' => $self));
            }
        }
    }

    public function organizing($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;
        $self;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $organizing = $this->db->query('SELECT events.id, name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled FROM `events` INNER JOIN `event_organizer` ON events.id = event_organizer.event_id WHERE event_organizer.organizer_id = ' . $userid);

            $data = $organizing->result();
            $meta = request_status('info_get_succeed');
            $self = self_check($userid, $_GET['username'], $_GET['token']);
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

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            if(self_check($userid, $_GET['username'], $_GET['token'])) {
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

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $watching = $this->db->query('SELECT events.id, name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled FROM `events` INNER JOIN `event_watcher` ON events.id = event_watcher.event_id WHERE event_watcher.watcher_id = ' . $userid);

            $data = $watching->result();
            $self = self_check($userid, $_GET['username'], $_GET['token']);
            $meta = request_status('info_get_succeed');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data, 'self' => $self));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function avatar($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        $username = $_POST['username'];
        $token = $_POST['token'];
        $avatar = $_POST['avatar_url'];

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && self_check($userid, $username, $token)) {
            $attending = $this->db->query('UPDATE users SET avatar_url = "' . $avatar . '" WHERE username = "' . $username . '"');
            $meta = request_status('info_change_succeed');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function password($userid) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        $username = $_POST['username'];
        $userpass = $_POST['userpass'];
        $new_pass = $_POST['newpass'];
        $old_token = $_POST['token'];

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && self_check($userid, $username, $old_token)) {
            $passcheck = $this->db->query('SELECT userpass FROM users WHERE username = "' . $username . '"');

            if(sha1($passcheck->row()->userpass) == $userpass) {//echo 'yes';
                $this->db->query('UPDATE users, user_token SET users.userpass = "' . sha1($new_pass) . '", user_token.expire_time = ' . $timing . ' WHERE users.username = "' . $username . '" AND user_token.username = "' . $username . '"');

                session_start();
                $shake = sha1(uniqid(session_id(), true));
                $token = session_id() . sha1(uniqid($username, true));
                session_destroy();
                $expire = $timing + 2592000;
                $this->db->query('INSERT INTO user_token (username, shake_string, token_string, generate_time, expire_time) VALUES ("' . $username . '", "' . $shake . '", "' . $token . '", ' . $timing . ', "' . $expire . '")');
                $meta = request_status('change_pass_succeed');
                $data = array('shake' => $shake);
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } else {
                $meta = request_status('wrong_password');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

}
