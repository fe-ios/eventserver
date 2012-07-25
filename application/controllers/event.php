<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller
{
	
	
	public function create()
	{
		$status = "";
		$msg = "";

		echo print_r($_POST);

		if(empty($_POST["owner_id"]) || empty($_POST["password"]) || empty($_POST["event_name"]) || empty($_POST["start_date"]))
		{
			$status = "error";
			$msg = "Post data error.";
		}else{
			$user_id = $this->input->post('owner_id');
			$password = $this->input->post('password');
			$query = $this->db->query('SELECT * FROM user WHERE user_id = '.$this->db->escape($user_id).' AND password = '.$this->db->escape($password).'');
			if($query->num_rows() == 0)
			{
				$status = "error";
				$msg = "Invalid user.";
			}else
			{
				$data = array(
					'owner_id' => $this->input->post('owner_id'),
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

	public function delete()
	{
		$status = "";
		$msg = "";

		if(empty($_POST["event_id"]) || empty($_POST["owner_id"]))
		{
			$status = "error";
			$msg = "Post data error.";
		}else{
			$event_id = $this->input->post("event_id");
			$owner_id = $this->input->post("owner_id");
			$result = $this->db->delete("event", array('event_id' => $event_id, 'owner_id' => $owner_id));
			$status = "success";
			$msg = $result;
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	public function get_user_events()
	{
		$status = "";
		$msg = "";

		if(empty($_GET["user_id"]))
		{
			$status = "error";
			$msg = "Params error.";
		}else{
			$attendee_id = $this->input->get("user_id");
			$query = $this->db->query("SELECT * from event WHERE event_id in (SELECT event_id from event_attendee WHERE attendee_id = ".$attendee_id.")");
			$status = "success";
			$msg = $query->result();
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	public function get_owner_events()
	{
		$status = "";
		$msg = "";

		if(empty($_GET["owner_id"]))
		{
			$status = "error";
			$msg = "Params error.";
		}else{
			$owner_id = $this->input->get("owner_id");
			$query = $this->db->query("SELECT * from event WHERE owner_id = ".$owner_id."");
			$status = "success";
			$msg = $query->result();
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}
}

?>