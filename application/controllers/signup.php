<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function index() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $userpass = $_POST['userpass'];
            $email = $_POST['email'];
            $passone = $this->db->query('SELECT 1 FROM users WHERE username = "' . $username . '" OR email = "' . $email . '"');

            if($passone->num_rows() > 0) {
                $meta = request_status('signup_fail1');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            } else {
                $passtwo = $this->db->query('INSERT INTO users (username, userpass, email, create_time, last_login, current_login) VALUES ("' . $username . '", "' . sha1($userpass) . '", "' . $email . '", "' . $timing . '", "' . $timing . '", "' . $timing . '")');

                if($this->db->affected_rows() > 0) {
                    session_start();
                    $shake = sha1(uniqid(session_id(), true));
                    $token = session_id() . sha1(uniqid($username, true));
                    session_destroy();
                    $expire = $timing + 2592000;
                    $this->db->query('INSERT INTO user_token (username, shake_string, token_string, generate_time, expire_time) VALUES ("' . $username . '", "' . $shake . '", "' . $token . '", "' . $timing . '", "' . $expire . '")');
                    $meta = request_status('signup_succeed');
                    $data = array('token' => $token, 'expire' => $expire);
                    echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
                } else {
                    $meta = request_status('signup_fail2');
                    echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                }
            }
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

}
