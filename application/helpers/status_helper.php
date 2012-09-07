<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function request_status($code) {
        $s;
        $m;

        switch($code) {
            case 'auth_fail': //authentication fail
                $s = 'ERROR';
                $m = 'Unauthenticated client. Request failed.';
                break;
            case 'request_deny': //request denied
                $s = 'ERROR';
                $m = 'Request denied.';
                break;
            case 'signup_fail1': //register fail 1
                $s = 'ERROR';
                $m = 'Username or email already exists!';
                break;
            case 'signup_fail2': //register fail 2
                $s = 'ERROR';
                $m = 'Register failed! Please try again.';
                break;
            case 'signup_succeed': //register succeed
                $s = 'OK';
                $m = 'Register succeed! Now you are logged in with your new account.';
                break;
            case 'signin_fail1': //signin 1st pass fail
                $s = 'ERROR';
                $m = 'This username does not exist.';
                break;
            case 'signin_fail2': //signin 2nd pass fail
                $s = 'ERROR';
                $m = 'Signin failed! Please try again.';
                break;
            case 'wrong_password': //change info fail
                $s = 'ERROR';
                $m = 'The old password you entered is not correct.';
                break;
            case 'signin_succeed1': //signin 1st pass succeed
                $s = 'OK';
                $m = 'Continue the 2nd step with the given shake string.';
                break;
            case 'signin_succeed2': //signin 2nd pass succeed
                $s = 'OK';
                $m = 'Great! Now you are logged in.';
                break;
            case 'tag_no_events': //no events found with the tag
                $s = 'ERROR';
                $m = 'No event tagged.';
                break;
            case 'tag_query_succeed': //query events with a tag
                $s = 'OK';
                $m = 'Successfully get all the events attached with the queried tag.';
                break;
            case 'tag_add_succeed': //add tag succeed
                $s = 'OK';
                $m = 'The new tag is successfully added.';
                break;
            case 'tag_add_fail': //add tag fail
                $s = 'ERROR';
                $m = 'The tag you are adding already exists. Query instead.';
                break;
            case 'user_list_succeed': //get info succeed
                $s = 'OK';
                $m = 'Get user list succeed.';
                break;
            case 'info_get_succeed': //get info succeed
                $s = 'OK';
                $m = 'Get user information succeed.';
                break;
            case 'info_get_fail': //get info fail
                $s = 'ERROR';
                $m = 'Get user information fail.';
                break;
            case 'info_change_succeed': //change info info succeed
                $s = 'OK';
                $m = 'Change to your information is successfully done.';
                break;
            case 'info_change_fail': //change info fail
                $s = 'ERROR';
                $m = 'Your can only change information of yourself.';
                break;
            case 'change_pass_succeed': //change info fail
                $s = 'OK';
                $m = 'Password changed successfully. Log in again.';
                break;
        }

        return array('s' => $s, 'm' => $m);
    }
