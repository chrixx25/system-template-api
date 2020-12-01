<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class UserController extends API_Controller {

	function __construct()
	{
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        $this->load->model('User/UserModel','UserModel');
	}

	function login()
	{
        header("Access-Control-Allow-Origin: *");
        // API Configuration
        $this->_apiConfig([
            'methods' => ['POST'],
        ]);
        $con = ldap_connect("192.168.1.244");
        ldap_set_option( $con, LDAP_OPT_PROTOCOL_VERSION, 3 );
        ldap_set_option( $con, LDAP_OPT_REFERRALS, 0 );

        $user = $this->input->post('user',TRUE);
        $pass = $this->input->post('pass',TRUE);
        // $test = $test ?? $this->input->get('test'); // if $test is null
        if( empty($user) ) return $this->api_return(['message'=>'Username is required'],400);
		if( empty($pass) ) return $this->api_return(['message'=>'Password is required'],400);

		if( $con )
		{
			$bind = @ldap_bind($con,$user."@phoenix.net.ph",$pass);
			$is_admin = $user == 'administrator' && $pass == "phoenix927" ? true : false;

			if( $bind || $is_admin )
			{
				$rs = $this->UserModel->login($user);

				if($rs)
				{
					if(!$is_admin) //Get data on spark if not admin
					{
						$dn = "DC=phoenix,DC=net,DC=ph";
						$search = ldap_search($con, $dn, "samaccountname=$user*");
						$data = ldap_get_entries($con, $search);

						$dept = '';
						for ($ii=0; $ii < $data[0]['memberof']['count']; $ii++)
						{
							$dept = $data[0]['memberof'][0];
						}
						$memberof = substr($dept, 3);
						$group = strtok($memberof, ",");
					}

					$username = $is_admin ? $rs->row()->Name : $data[0]["samaccountname"][0];
					$fullname = $is_admin ? $rs->row()->Username : $data[0]["displayname"][0];
					$group = $is_admin ? 'Adminstrators' : $group;

					$tokendata = array(
						'userid'  => (int)$rs->row()->UserID
						, 'username'  => strtolower($username)
						, 'fullname'  => strtoupper($fullname)
						, 'group'     => strtoupper($group)
					);
					//$tokendata = (array)$rs->row();

					$this->load->library('authorization_token');

					// generate a token
					$token = $this->authorization_token->generateToken($tokendata);
					$this->api_return(['token' => $token ], 200);
				}
				else
				{
					$this->api_return(['message' => 'You have no Acces in this site'], 401);
				}
			}
			else
			{
				$this->api_return(['message' => 'Invalid Account not on spark'], 400);
			}
		}
		else
		{
			$this->api_return(['message'=>'Unable to connect server.'],500);
		}
    }
}
