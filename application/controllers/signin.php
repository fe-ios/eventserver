<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signin extends CI_Controller {

    public function index() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            // 1st pass
            $username = $_GET['username'];
            $passone = $this->db->query('SELECT shake_string, token_string, expire_time FROM user_token INNER JOIN users ON users.username = user_token.username WHERE users.username = "' . $username . '" ORDER BY expire_time DESC LIMIT 1');

            if($passone->num_rows() == 0) {
                $meta = request_status('signin_fail1');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            } elseif($passone->row()->expire_time > $timing) {
                $meta = request_status('signin_succeed1');
                $data = array('shake' => $passone->row()->shake_string);
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } else {
                session_start();
                $shake = sha1(uniqid(session_id(), true));
                $token = session_id() . sha1(uniqid($username, true));
                session_destroy();
                $expire = $timing + 2592000;
                $this->db->query('INSERT INTO user_token (username, shake_string, token_string, generate_time, expire_time) VALUES ("' . $username . '", "' . $shake . '", "' . $token . '", "' . $timing . '", "' . $expire . '")');
                $meta = request_status('signin_succeed1');
                $data = array('shake' => $shake);
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            }
            // end 1st pass
        } else {
            // 2nd pass
            $username = $_POST['username'];
            $userpass = $_POST['userpass'];
            $passtwo = $this->db->query('SELECT shake_string, token_string, expire_time, userpass, current_login FROM user_token INNER JOIN users ON users.username = user_token.username WHERE users.username = "' . $username . '" ORDER BY expire_time DESC LIMIT 1');

            if($userpass == sha1($passtwo->row()->shake_string . $passtwo->row()->userpass)) {
                $meta = request_status('signin_succeed2');
                $data = array('token' => $passtwo->row()->token_string, 'expire' => $passtwo->row()->expire_time);
                $this->db->query('UPDATE users SET last_login = "' . $passtwo->row()->current_login . '", current_login = "' . $timing . '" WHERE username = "' . $username . '"');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } else {
                $meta = request_status('signin_fail2');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
            // end 2nd pass
        }
    }

}
