<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class UserSetupController extends API_Controller
{

	function __construct()
	{
		parent::__construct();
		header("Access-Control-Allow-Origin: *");
		date_default_timezone_set('Asia/Manila');
		$this->load->model('User/UserModel','UserModel');

		$this->token_data = $this->_isAuthorized();
	}

	private function getUsers()
	{
		$rs = $this->UserModel->getUsers();
		$data = [];

		if($rs)
		{
			foreach ($rs->result() as $row)
			{
				$data[] = array(
					'userid' => (int)$row->UserID
					, 'username' => strtolower($row->Username)
					, 'name' => strtoupper($row->Name)
				);
			}
			return $this->api_return(['data' => $data], 200);
		}
		else
		{
			return $this->api_return(['message' => 'No record found'], 400);
		}
	}

	private function getUserInfo($id)
	{

		$rs = $this->UserModel->getUserInfo($id);
		$data = (object)[];

		if($rs)
		{
			$data = array(
				'userid' => (int)$rs->row()->UserID
				, 'username' => strtolower($rs->row()->Username)
				, 'name' => strtoupper($rs->row()->Name)
			);

			return $this->api_return(['data' => $data], 200);
		}
		else
		{
			return $this->api_return(['message' => 'No record found'], 400);
		}
	}

	private function insertUser()
	{
		if( empty($this->input->post('username')) ) return $this->api_return(['message'=>'Username is required'],400);
		if( empty($this->input->post('name')) ) return $this->api_return(['message'=>'Name is required'],400);

		$username = strtolower($this->input->post('username'));
		$name = strtoupper($this->input->post('name'));

		$rs = $this->UserModel->insertUser($username, $name);

		return $rs ? $this->api_return(['message' => 'Success Insert'], 200) : $this->api_return(['message' => 'Error Insert'], 400);
	}

	private function updateUser($id)
	{
		if( empty($this->input->post('username')) ) return $this->api_return(['message'=>'Username is required'],400);
		if( empty($this->input->post('name')) ) return $this->api_return(['message'=>'Name is required'],400);

		$username = strtolower($this->input->post('username'));
		$name = strtoupper($this->input->post('name'));

		$rs = $this->UserModel->updateUser($username, $name, (int)$id);

		return $rs ? $this->api_return(['message' => 'Success Update'], 200) : $this->api_return(['message' => 'Error Update'], 400);
	}

	function users($id = null)
	{
		$method = strtoupper($this->input->method());

		 // API Configuration
		$this->_apiConfig([
            'methods' => [$method]
            , 'requireAuthorization' => true
		]);

		switch ($method)
		{
			case 'GET':
					return is_null($id) ? $this->getUsers() : $this->getUserInfo($id);
				break;
			case 'POST':
					return is_null($id) ? $this->insertUser() : $this->updateUser($id) ;
				break;
			default:
				$data = 'INVALID METHOD.';
				break;
		}
	}
}
