<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signin extends CI_Controller {

    public function index() {

        $timing = $_SERVER['REQUEST_TIME'];
        $status;
        $msg;
        $data;

        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            if( !client_auth() ) {
                $status = 'ERROR';
                $msg = 'Unauthenticated client. Request failed.';
                echo json_encode(array('status' => $status, 'msg' => $msg));
            } else {
                // 1st pass
                $username = $_GET['username'];
                $passone = $this->db->query('SELECT shake_string, token_string, expire_time FROM user_token INNER JOIN users ON users.username = user_token.username WHERE users.username = "' . $username . '" ORDER BY expire_time DESC LIMIT 1');

                if($passone->num_rows() == 0) {
                    $status = 'ERROR';
                    $msg = 'This username does not exist.';
                    echo json_encode(array('status' => $status, 'msg' => $msg));
                } elseif($passone->row()->expire_time > $timing) {
                    $status = 'OK';
                    $msg = 'Continue the 2nd step with the given shake string.';
                    $data = array('shake' => $passone->row()->shake_string);
                    echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
                } else {
                    session_start();
                    $shake = sha1(uniqid(session_id(), true));
                    $token = session_id() . sha1(uniqid($username, true));
                    session_destroy();
                    $expire = $timing + 2592000;
                    $this->db->query('INSERT INTO user_token (username, shake_string, token_string, generate_time, expire_time) VALUES ("' . $username . '", "' . $shake . '", "' . $token . '", "' . $timing . '", "' . $expire . '")');
                    $status = 'OK';
                    $msg = 'Token expired. Continue the 2nd step with new shake string.';
                    $data = array('shake' => $shake);
                    echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
                }
                // end 1st pass
            }
        } else {
            if( !client_auth() ) {
                $status = 'ERROR';
                $msg = 'Unauthenticated client. Request failed.';
                echo json_encode(array('status' => $status, 'msg' => $msg));
            } else {
                // 2nd pass
                $status = 'ERROR';
                $msg = 'Login failed! Please try again.';
                $username = $_POST['username'];
                $userpass = $_POST['userpass'];
                $passtwo = $this->db->query('SELECT shake_string, token_string, expire_time, userpass, current_login FROM user_token INNER JOIN users ON users.username = user_token.username WHERE users.username = "' . $username . '" ORDER BY expire_time DESC LIMIT 1');

                if($userpass == sha1($passtwo->row()->shake_string . $passtwo->row()->userpass)) {
                    $status = 'OK';
                    $msg = 'Great! Now you are logged in.';
                    $data = array('token' => $passtwo->row()->token_string, 'expire' => $passtwo->row()->expire_time);
                    $this->db->query('UPDATE users SET last_login = "' . $passtwo->row()->current_login . '", current_login = "' . $timing . '" WHERE username = "' . $username . '"');
                    echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
                } else {
                    $status = 'ERROR';
                    $msg = 'Wrong password! Please try again.';
                    echo json_encode(array('status' => $status, 'msg' => $msg));
                }
                // end 2nd pass
            }
        }

    }

}
