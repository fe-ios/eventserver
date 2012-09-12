<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function client_check() {
        $CIevent =& get_instance();

        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $args_array = explode('&', $_SERVER['QUERY_STRING']);
        } else {
            $args_array = array();
            foreach($_POST as $pkey => $pvalue) {
                array_push($args_array, $pkey . '=' . $pvalue);
            }
        }
        sort($args_array, SORT_STRING);
        $retrived_string = implode('&', $args_array);

        return base64_encode($_SERVER['REQUEST_METHOD'] . ':' . rawurlencode($CIevent->uri->uri_string() . '?' . $retrived_string) . IEVENT_AUTHENTICATION) == $_SERVER['HTTP_SIGNATURE'];
    }

    function token_check($username, $token) {
        $CIevent =& get_instance();

        $query = $CIevent->db->query('SELECT 1 FROM `user_token` WHERE username = "' . $username . '" AND token_string = "' . $token . '" AND expire_time > ' . $_SERVER['REQUEST_TIME']);

        if($query->num_rows() == 0) {
            return false;
        } else {
            return true;
        }
    }

    function self_check($userid, $username, $token) {
        $CIevent =& get_instance();

        $query = $CIevent->db->query('SELECT 1 FROM `users` INNER JOIN `user_token` ON users.username = user_token.username WHERE users.id = ' . $userid . ' AND users.username = "' . $username . '" AND user_token.token_string = "' . $token . '" AND user_token.expire_time > ' . $_SERVER['REQUEST_TIME']);

        if($query->num_rows() == 0) {
            return false;
        } else {
            return true;
        }
    }
