<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	function client_auth() {
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
        return base64_encode(rawurlencode($retrived_string) . IEVENT_AUTHENTICATION) == $_SERVER['HTTP_IEVENT_SIGNATURE'];
	}
