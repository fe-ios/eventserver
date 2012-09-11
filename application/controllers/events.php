<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller {

    public function index() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page = $_GET['page'];
            $event_list = $this->db->query('SELECT * FROM `events` ORDER BY start_time DESC LIMIT ' . ($page - 1) * 25 . ', 25');
            $meta = request_status('event_list_succeed');
            $data = $event_list->result();
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_match($_POST['username'], $_POST['token'])) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $detail = $_POST['detail'];
            $type = $_POST['type'];
            $verify = $_POST['verify'];
            $creator_id = $_POST['creator'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $venue = $_POST['venue'];
            $address = $_POST['address'];
            $logo_url = $_POST['logo_url'];
            $tickets_total = $_POST['tickets_total'];

            $this->db->query('INSERT INTO events (name, description, detail, type, verify, creator_id, create_time, start_time, end_time, venue, address, logo_url, tickets_total, tickets_remain, canceled, num_attendees, num_watchers) VALUES ("' . $name . '", "' . $description . '", "' . $detail . '", "' . $type . '", "' . $verify . '", ' . $creator_id . ', ' . $timing . ', ' . $start_time . ', ' . $end_time . ', "' . $venue . '", "' . $address . '", "' . $logo_url . '", ' . $tickets_total . ', ' . $tickets_total . ', "false", 0, 0)');

            if($this->db->affected_rows() > 0) {
                $meta = request_status('add_event_succeed');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            } else {
                $meta = request_status('add_event_fail');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }

    public function info($event_id) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_auth() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            //获取活动信息
            $event_list = $this->db->query('SELECT * FROM `events` WHERE id = ' . $event_id);
            $meta = request_status('info_get_succeed');
            $data = $event_list->result();
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_match($_POST['username'], $_POST['token'])) {
            //修改活动信息
            $name = $_POST['name'];
            $description = $_POST['description'];
            $detail = $_POST['detail'];
            $type = $_POST['type'];
            $verify = $_POST['verify'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $venue = $_POST['venue'];
            $address = $_POST['address'];
            $logo_url = $_POST['logo_url'];
            $tickets_total = $_POST['tickets_total'];
            $canceled = $_POST['canceled'];

            $this->db->query('UPDATE events SET name = "' . $name . '", description = "' . $description . '", detail = "' . $detail . '", type = "' . $type . '", verify = "' . $verify . '", start_time = "' . $start_time . '", end_time = "' . $end_time . '", venue = "' . $venue . '", address = "' . $address . '", logo_url = "' . $logo_url . '", tickets_total = "' . $tickets_total . '", canceled = "' . $canceled . '" WHERE id = "' . $event_id . '"');

            if($this->db->affected_rows() > 0) {
                $meta = request_status('modify_event_succeed');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            } else {
                $meta = request_status('modify_event_fail');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            $meta = request_status('request_deny');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        }
    }



/*
$route['events/(:num)/(:any)'] = "events/$2/$1";
$route['events/(:num)/(:any)/(:num)'] = "events/$2/$1/$3";

/events/(id)/logo						POST		上传活动logo（仅允许组织活动的用户提交）
/events/(id)/agenda						GET			获取活动议程（对组织活动的用户显示编辑入口）
/events/(id)/agenda						POST		修改活动议程（仅允许组织活动的用户提交）
/events/(id)/organizers					GET			获取活动组织者列表（对组织活动的用户显示编辑入口）
/events/(id)/organizers					POST		添加活动组织者（仅允许组织活动的用户提交）
/events/(id)/organizers/(id)			POST		移除一个活动组织者（仅允许组织活动的用户提交）
/events/(id)/attendees					GET			获取报名成功的参加者列表（对组织活动的用户显示等待审核列表和编辑入口）
/events/(id)/attendees					POST		向一个非活动组织者的用户发出邀请（仅允许组织活动的用户提交）
/events/(id)/attendees/(id)				POST		审核用户报名申请（仅允许组织活动的用户提交）
/events/(id)/attendees/(id)				POST		报名/取消报名/接受邀请/拒绝邀请（仅允许非活动组织者的用户提交）
/events/(id)/watchers					GET			获取活动关注者列表
/events/(id)/watchers					POST		关注该活动
/events/(id)/watchers/(id)				POST		取消关注（仅允许传入登录用户自己的id）
/events/(id)/tags						GET			获取活动标签列表（对组织活动的用户显示编辑入口）
/events/(id)/tags						POST		增加一个标签（仅允许组织活动的用户提交）
/events/(id)/tags/(id)					POST		移除一个标签（仅允许组织活动的用户提交）
*/

}
