<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signup extends CI_Controller {

    public function index() {

        $timing = $_SERVER['REQUEST_TIME'];
        $status;
        $msg;
        $data;

        if( !client_auth() ) {
            $status = 'ERROR';
            $msg = 'Unauthenticated client. Request failed.';
            echo json_encode(array('status' => $status, 'msg' => $msg));
        } else {
            $username = $_POST['username'];
            $userpass = $_POST['userpass'];
            $email = $_POST['email'];
            $passone = $this->db->query('SELECT 1 FROM users WHERE username = "' . $username . '" OR email = "' . $email . '"');

            if($passone->num_rows() > 0) {
                $status = 'ERROR';
                $msg = 'Username or email already exists!';
                echo json_encode(array('status' => $status, 'msg' => $msg));
            } else {
                $passtwo = $this->db->query('INSERT INTO users (username, userpass, email, create_time, last_login, current_login) VALUES ("' . $username . '", "' . sha1($userpass) . '", "' . $email . '", "' . $timing . '", "' . $timing . '", "' . $timing . '")');

                if($this->db->affected_rows() > 0) {
                    session_start();
                    $shake = sha1(uniqid(session_id(), true));
                    $token = session_id() . sha1(uniqid($username, true));
                    session_destroy();
                    $expire = $timing + 2592000;
                    $this->db->query('INSERT INTO user_token (username, shake_string, token_string, generate_time, expire_time) VALUES ("' . $username . '", "' . $shake . '", "' . $token . '", "' . $timing . '", "' . $expire . '")');
                    $status = 'OK';
                    $msg = 'Register succeed! Now you are logged in with your new account.';
                    $data = array('token' => $token, 'expire' => $expire);
                    echo json_encode(array('status' => $status, 'msg' => $msg, 'data' => $data));
                } else {
                    $status = 'ERROR';
                    $msg = 'Register failed! Please try again.';
                    echo json_encode(array('status' => $status, 'msg' => $msg));
                }
            }
        }

    }

}
