<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class User extends CI_Controller
{
	
	public function register()
	{
		$status = "";
		$msg = "";

		if(empty($_POST['username']) || empty($_POST['password']))
		{
			$status = "error";
			$msg = "Post data error.";
		}else{
			$result = $this->db->query("SELECT * FROM user WHERE username = ".$this->db->escape($this->input->post('username'))."");
			if($result->num_rows() > 0){
				$status = "error";
				$msg = "User already exist.";
			}else{
				$data = array(
					'username' => $this->input->post('username'), 
					'password' => $this->input->post('password'),
					//'password' => md5($this->input->post('password')),
					'create_date' => date('Y-m-d H:i:s')
				);
				try {
					$this->db->insert('user', $data);
					$userid = $this->db->insert_id();
					$status = "success";
					$user = array(
						'userid' => $userid, 
						'username' => $data['username'],
						'password' => $data['password']
					);
					echo json_encode(array('status' => $status, 'user' => $user));
					return;
				} catch (Exception $e) {
					$status = "error";
					$msg = "Database error.";
				}
			}
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	public function login()
	{
		$status = "";
		$msg = "";

		if(empty($_POST['username']) || empty($_POST['password']))
		{
			$status = "error";
			$msg = "Post data error.";
		}else{
			try {
				$username = $this->input->post('username');
				$password = $this->input->post('password');
				//$password = md5($this->input->post('password'));
				$date = date("Y-m-d H:i:s");
				$session = $this->encrypt->encode($username.$password.time());
				$query = $this->db->query('SELECT * FROM user WHERE username = '.$this->db->escape($username).' AND password = '.$this->db->escape($password).'');
				if($query->num_rows() > 0)
				{
					$this->db->query('UPDATE user set login_time = '.$this->db->escape($date).', session = '.$this->db->escape($session).' WHERE username = '.$this->db->escape($username).'');
					$result = $query->row();
					$status = "success";
					$user = array(
						'userid' => $result->user_id, 
						'username' => $result->username,
						'password' => $result->password
					);
					echo json_encode(array('status' => $status, 'user' => $user));
					return;
				}else{
					$status = "error";
					$msg = "Username or password error.";
				}
			} catch (Exception $e) {
				$status = "error";
				$msg = "Database error.";
			}
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}

	public function logout()
	{
		$status = "";
		$msg = "";

		if(empty($_POST['username']) || empty($_POST['password']))
		{
			$status = "error";
			$msg = "Post data error.";
		}else{
			try {
				$username = $this->input->post('username');
				$password = $this->input->post('password');
				//$password = md5($this->input->post('password'));
				$this->db->query('UPDATE user set login_time = "" session = "" WHERE username = '.$this->db->escape($username).' AND password = '.$this->db->escape($password).'');
				$status = "success";
				$msg = "Logout ok.";
			} catch (Exception $e) {
				$status = "error";
				$msg = "Database error.";
			}
		}

		echo json_encode(array('status' => $status, 'msg' => $msg));
	}
}

?>