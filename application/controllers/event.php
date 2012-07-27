<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller
{
	
	
	public function create_event()
	{
		$status = "";
		$msg = "";
		//echo print_r($_POST);

		if(empty($_POST["user_id"]) || empty($_POST["password"]) || empty($_POST["event_name"]) || empty($_POST["start_date"]))
		{
			$status = "error";
			$msg = "Post data error.";
		}else{
			$user_id = $this->input->post('user_id');
			$password = $this->input->post('password');
			$query = $this->db->query('SELECT * FROM user WHERE user_id = '.$this->db->escape($user_id).' AND password = '.$this->db->escape($password).'');
			if($query->num_rows() == 0)
			{
				$status = "error";
				$msg = "Invalid user.";
			}else
			{
				$data = array(
					'owner_id' => $this->input->post('user_id'),
					'name' => $this->input->post('event_name'), 
					'start_date' => $this->input->post('start_date'),
					'end_date' => $this->input->post('end_date'),
					'desc' => $this->input->post('desc'),
					'detail' => $this->input->post('detail'),
					'type' => $this->input->post('type'),
					'venue' => $this->input->post('venue'),
					'address' => $this->input->post('address'),
					'logo' => $this->input->post('logo'),
					'ticket_total' => $this->input->post('ticket_total')
				);
				try {
					$this->db->insert('event', $data);
					$event_id = $this->db->insert_id();
					$status = "success";
					$event = array('event_id' => $event_id);
					echo json_encode(array('status' => $status, 'event' => $event));
					return;
				} catch (Exception $e) {
					$status = "error";
					$msg = "Database error.";
				}
			}	
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	public function delete_event()
	{
		if(empty($_POST["event_id"]) || empty($_POST["user_id"]) || empty($_POST["password"]))
		{
			$status = "error";
			$msg = "Post data error.";
			echo json_encode(array('status' => $status, 'msg' => $msg));
		}else{
			$event_id = $this->input->post("event_id");
			$user_id = $this->input->post("user_id");
			$password = $this->input->post('password');
			$query = $this->db->query('SELECT * FROM user WHERE user_id = '.$this->db->escape($user_id).' AND password = '.$this->db->escape($password).'');
			if($query->num_rows() == 0)
			{
				$status = "error";
				$msg = "Invalid user.";
			}else{
				$result = $this->db->delete("event", array('event_id' => $event_id, 'owner_id' => $user_id));
				$status = "success";
				echo json_encode(array('status' => $status, 'result' => $result));
			}
		}
	}

	public function user_event()
	{
		if(empty($_POST["user_id"]) || empty($_POST["password"]))
		{
			$status = "error";
			$msg = "Params error.";
			echo json_encode(array('status' => $status, 'msg' => $msg));
		}else{
			$user_id = $this->input->post("user_id");
			$password = $this->input->post('password');
			$query = $this->db->query('SELECT * FROM user WHERE user_id = '.$this->db->escape($user_id).' AND password = '.$this->db->escape($password).'');
			if($query->num_rows() == 0)
			{
				$status = "error";
				$msg = "Invalid user.";
			}else{
				$query = $this->db->query("SELECT * from event WHERE event_id in (SELECT event_id from event_attendee WHERE attendee_id = ".$this->db->escape($user_id).")");
				$status = "success";
				$event = $query->result();
				echo json_encode(array('status' => $status, 'event' => $event));
			}
		}
	}

	public function owner_event()
	{
		if(empty($_POST["owner_id"]) || empty($_POST["password"]))
		{
			$status = "error";
			$msg = "Params error.";
			echo json_encode(array('status' => $status, 'msg' => $msg));
		}else{
			$user_id = $this->input->post("owner_id");
			$password = $this->input->post('password');
			$query = $this->db->query('SELECT * FROM user WHERE user_id = '.$this->db->escape($user_id).' AND password = '.$this->db->escape($password).'');
			if($query->num_rows() == 0)
			{
				$status = "error";
				$msg = "Invalid user.";
			}else{
				$query = $this->db->query("SELECT * from event WHERE owner_id = ".$this->db->escape($user_id)."");
				$status = "success";
				$event = $query->result();
				echo json_encode(array('status' => $status, 'event' => $event));
			}
		}
	}

	public function public_event()
	{
		$start = 0;
		$count = 10;
		if(!empty($_GET["start"])){
			$start = (int)$this->input->get("start");
		}
		if(!empty($_GET["count"])){
			$count = (int)$this->input->get("count");
		}else{
			$count = 0;
		}
		if($count > 100) $count = 100;

		if($count == 0){
			$query = $this->db->query("SELECT * from event WHERE event_id > ".$this->db->escape($start)." order by event_id DESC");
		}else{
			$query = $this->db->query("SELECT * from event order by event_id DESC LIMIT ".$this->db->escape($start).", ".$this->db->escape($count)."");
		}

		$status = "success";
		$event = $query->result();
		echo json_encode(array('status' => $status, 'event' => $event));
	}
	
}

?>