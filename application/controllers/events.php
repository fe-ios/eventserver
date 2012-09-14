<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller {

    public function index() {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page = $_GET['page'];
            $event_list = $this->db->query('SELECT * FROM `events` ORDER BY start_time DESC LIMIT ' . ($page - 1) * 25 . ', 25');
            $meta = request_status('event_list_succeed');
            $data = $event_list->result();
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
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
                $event_id = $this->db->insert_id();
                $this->db->query('INSERT INTO event_organizer SET event_id = ' . $event_id . ', organizer_id = ' . $creator_id . ', organizer_type = "creator"');

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

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            //获取活动信息
            $event_list = $this->db->query('SELECT * FROM `events` WHERE id = ' . $event_id);
            $meta = request_status('info_get_succeed');
            $data = $event_list->result();
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
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

    public function logo($event_id) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
            //修改活动议程
            $logo_url = $_POST['logo_url'];

            $this->db->query('UPDATE events SET logo_url = "' . $logo_url . '" WHERE id = "' . $event_id . '"');

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

    public function agenda($event_id) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
            //获取活动议程
            $event_list = $this->db->query('SELECT agenda FROM `events` WHERE id = ' . $event_id);
            $meta = request_status('info_get_succeed');
            $data = $event_list->result();
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
            //修改活动议程
            $agenda = $_POST['agenda'];

            $this->db->query('UPDATE events SET agenda = "' . $agenda . '" WHERE id = "' . $event_id . '"');

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

    public function organizers($event_id, $user_id = 0) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($user_id == 0) {
            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                //获取组织者列表
                $page = $_GET['page'];
                $organizer_list = $this->db->query('SELECT users.id, users.username, event_organizer.organizer_type FROM `users` INNER JOIN `event_organizer` ON event_organizer.organizer_id = users.id INNER JOIN `events` ON events.id = event_organizer.event_id WHERE events.id = ' . $event_id . ' AND organizer_type != "false" ORDER BY users.username ASC LIMIT ' . ($page - 1) * 25 . ', 25');
                $meta = request_status('info_get_succeed');
                $data = $organizer_list->result();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
                //添加活动组织者
                $organizer_id = $_POST['organizer'];

                $query_organizer = $this->db->query('SELECT 1 FROM event_organizer WHERE event_id = ' . $event_id . ' AND organizer_id = ' . $organizer_id);

                if($query_organizer->num_rows() == 0) {
                    $this->db->query('INSERT INTO event_organizer SET event_id = ' . $event_id . ', organizer_id = ' . $organizer_id . ', organizer_type = "crew"');

                    if($this->db->affected_rows() > 0) {
                        $meta = request_status('modify_event_succeed');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    } else {
                        $meta = request_status('modify_event_fail');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    }
                } else {
                    $meta = request_status('modify_event_fail');
                    echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                }
            } else {
                $meta = request_status('request_deny');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {

                $this->db->query('DELETE FROM event_organizer WHERE event_id = ' . $event_id . ' AND organizer_id = ' . $user_id);

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
    }

    public function watchers($event_id, $user_id = 0) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($user_id == 0) {
            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                //获取关注者列表
                $page = $_GET['page'];
                $watcher_list = $this->db->query('SELECT users.id, users.username FROM `users` INNER JOIN `event_watcher` ON event_watcher.watcher_id = users.id INNER JOIN `events` ON events.id = event_watcher.event_id WHERE events.id = ' . $event_id . ' ORDER BY users.username ASC LIMIT ' . ($page - 1) * 25 . ', 25');
                $meta = request_status('info_get_succeed');
                $data = $watcher_list->result();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
                //添加活动关注者
                $watcher_id = $_POST['watcher'];

                $query_watcher = $this->db->query('SELECT 1 FROM event_watcher WHERE event_id = ' . $event_id . ' AND watcher_id = ' . $watcher_id);

                if($query_watcher->num_rows() == 0) {
                    $this->db->query('INSERT INTO event_watcher SET event_id = ' . $event_id . ', watcher_id = ' . $watcher_id);

                    if($this->db->affected_rows() > 0) {
                        $meta = request_status('modify_event_succeed');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    } else {
                        $meta = request_status('modify_event_fail');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    }
                } else {
                    $meta = request_status('modify_event_fail');
                    echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                }
            } else {
                $meta = request_status('request_deny');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && self_check($user_id, $_POST['username'], $_POST['token'])) {

                $this->db->query('DELETE FROM event_watcher WHERE event_id = ' . $event_id . ' AND watcher_id = ' . $user_id);

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
    }

    public function attendees($event_id, $user_id = 0) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($user_id == 0) {
            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                //获取参与者列表
                $page = $_GET['page'];
                $attendee_list = $this->db->query('SELECT users.id, users.username FROM `users` INNER JOIN `event_attendee` ON event_attendee.attendee_id = users.id INNER JOIN `events` ON events.id = event_attendee.event_id WHERE events.id = ' . $event_id . ' AND event_attendee.attendee_status = "ok" ORDER BY users.username ASC LIMIT ' . ($page - 1) * 25 . ', 25');
                $meta = request_status('info_get_succeed');
                $data = $attendee_list->result();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
                //添加活动参与者
                $attendee_id = $_POST['attendee'];

                $query_attendee = $this->db->query('SELECT 1 FROM event_attendee WHERE event_id = ' . $event_id . ' AND attendee_id = ' . $attendee_id);

                if($query_attendee->num_rows() == 0) {
                    $this->db->query('INSERT INTO event_attendee SET event_id = ' . $event_id . ', attendee_id = ' . $attendee_id . ', attendee_status = "pending"');

                    if($this->db->affected_rows() > 0) {
                        $meta = request_status('modify_event_succeed');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    } else {
                        $meta = request_status('modify_event_fail');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    }
                } else {
                    $meta = request_status('modify_event_fail');
                    echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                }
            } else {
                $meta = request_status('request_deny');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && self_check($user_id, $_POST['username'], $_POST['token'])) {

                $this->db->query('UPDATE event_attendee SET attendee_status = "' . $_POST['status'] . '" WHERE event_id = ' . $event_id . ' AND attendee_id = ' . $user_id);

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
    }

    public function tags($event_id, $tag_id = 0) {
        $timing = $_SERVER['REQUEST_TIME'];
        $meta;
        $data;

        if( !client_check() ) {
            $meta = request_status('auth_fail');
            echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
        } elseif($tag_id == 0) {
            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                //获取组织者列表
                $page = $_GET['page'];
                $event_list = $this->db->query('SELECT tags.id, tags.tag FROM `tags` INNER JOIN `event_tag` ON event_tag.tag_id = tags.id INNER JOIN `events` ON events.id = event_tag.event_id WHERE events.id = ' . $event_id . ' ORDER BY tags.tag ASC LIMIT ' . ($page - 1) * 25 . ', 25');
                $meta = request_status('info_get_succeed');
                $data = $event_list->result();
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m'], 'data' => $data));
            } elseif($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {
                //添加活动组织者
                $tagid = $_POST['tagid'];

                $query_tag = $this->db->query('SELECT 1 FROM event_tag WHERE event_id = ' . $event_id . ' AND tag_id = ' . $tagid);

                if($query_tag->num_rows() == 0) {
                    $this->db->query('INSERT INTO event_tag SET event_id = ' . $event_id . ', tag_id = ' . $tagid);

                    if($this->db->affected_rows() > 0) {
                        $meta = request_status('modify_event_succeed');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    } else {
                        $meta = request_status('modify_event_fail');
                        echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                    }
                } else {
                    $meta = request_status('modify_event_fail');
                    echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
                }
            } else {
                $meta = request_status('request_deny');
                echo json_encode(array('status' => $meta['s'], 'msg' => $meta['m']));
            }
        } else {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && token_check($_POST['username'], $_POST['token'])) {

                $this->db->query('DELETE FROM event_tag WHERE event_id = ' . $event_id . ' AND tag_id = ' . $tag_id);

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
    }

}
