<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
require_once APPPATH . 'libraries/API_Controller.php';

class ADUsersController extends API_Controller
{
	function __construct()
	{
		parent::__construct();
		header("Access-Control-Allow-Origin: *");
		date_default_timezone_set('Asia/Manila');
		$this->load->model('User/UserModel','UserModel');

		$this->token_data = $this->_isAuthorized();
	}

	private function GetAllADUsers( $is_username = true )
    {
        $con = ldap_connect("192.168.1.244");
        ldap_set_option( $con, LDAP_OPT_PROTOCOL_VERSION, 3 );
        ldap_set_option( $con, LDAP_OPT_REFERRALS, 0 );
        $user = "psb";
        $pass = "psb";
        $listGroupID = [];
        $existingEmployee = [];
		$existingSpark = [];
		$data = [];
        $first_dept = '';

        if($con)
        {

			$bind = ldap_bind($con,$user."@phoenix.net.ph",$pass);

            if($bind)
            {
                $dn = "DC=phoenix,DC=net,DC=ph";
                $search_groups =ldap_search($con, $dn, "objectcategory=CN=Group,CN=Schema,CN=Configuration,DC=phoenix,DC=net,DC=ph");
				$groups = ldap_get_entries($con, $search_groups);

                for($i = 0; $i < $groups["count"]; $i++)
                {
                    $exceptions = array(
                        'Users',
                        'Administrators',
                        'Certificate Service DCOM Access',
                        'Denied RODC Password Replication Group',
                        'Domain Admins',
                        'Enterprise Admins',
                        'Group Policy Creator Owners',
                        'Guests',
                        'IIS_IUSRS',
                        'Inactive',
                        'Pre-Windows 2000 Compatible Access',
                        'Schema Admins',
                        'Cert Publishers',
                        'Windows Authorization Access Group',
                        'Print Operators',
                        'backup operators',
                        'replicator',
                        'remote desktop users',
                        'network configuration operators',
                        'performance monitor users',
                        'performance log users',
                        'performance monitor users',
                        'distributed com users',
                        'gryptographic operators',
                        'event log readers',
                        'domain computers',
                        'domain controllers',
                        'domain users',
                        'domain guests',
                        'RAS and ias servers',
                        'server operators',
                        'cryptographic operators',
                        'account operators',
                        'incoming forest trust builders',
                        'terminal server license servers',
                        'allowed rodc password replication group',
                        'read-only domain controllers',
                        'enterprise read-only domain controllers',
                        'dnsadmins',
                        'dnsupdateproxy',
                        'server operators'
					);

                    $group_name = $groups[$i]['cn'][0];
					$group_member = $groups[$i]['member'];

                    if( in_array(strtolower($group_name),array_map('strtolower', array('mis team'))) )
                    {
                        $first_group_member = $groups[ $listGroupID[0] ]['member'];
                        $first_emp = strpos( $first_group_member[0] , ',');
						$first_emp = strtoupper(substr($first_group_member[0], 3,$first_emp - 3));

                        for($ii = 0; $ii < $group_member["count"]; $ii++)
                        {
                            $firstComma = strpos( $group_member[$ii] , ',');
                            $fullName = strtoupper(substr($group_member[$ii], 3,$firstComma - 3));
                            $dn = "DC=phoenix,DC=net,DC=ph";
                            $search_groups =ldap_search($con, $dn, "cn=$fullName");
							$user = ldap_get_entries($con, $search_groups);

								$value = $is_username ? strtolower($user[0]['samaccountname'][0]) : strtoupper($fullName);

								$data[] = array(
									'id' => strtolower($user[0]['samaccountname'][0])
									, 'text' => $value
								);
						}
                    }
				}

				$first_dept = $group_name[0];

				return $this->api_return([
					'results' => $data
				], 200);
			}
        }
		else
		{
			return $this->api_return([
                'results' => "Unable to connect to the server.",
            ], 401);
		}
	}

	private function GetADByValue( $username = "", $employee_name = "" )
    {
        $con = ldap_connect("192.168.1.244");
        ldap_set_option( $con, LDAP_OPT_PROTOCOL_VERSION, 3 );
        ldap_set_option( $con, LDAP_OPT_REFERRALS, 0 );
        $user = 'psb';
		$pass = 'psb';
		$data = [];
		$by = $username == "" ? "cn" : "samaccountname";
		$value = $username == "" ? $employee_name : $username;

        if($con)
        {
			$bind = ldap_bind($con,$user."@phoenix.net.ph",$pass);

            if($bind)
            {
                $dn = "DC=phoenix,DC=net,DC=ph";
                $search =ldap_search($con, $dn, "$by=$value");
				$ad_data = ldap_get_entries($con, $search);

                for ($i=0; $i < $ad_data['count']; $i++)
                {
                    $memberof = substr( $ad_data[$i]['memberof'][0], 3);
                    $group = strtok( $memberof, ",");
					$fullname = strtoupper($ad_data[$i]['cn'][0]);
					$username = strtolower($ad_data[$i]['samaccountname'][0]);
                }

                $data[] = array(
                  	'name' => $fullname
					, 'username' => $username
                	//, 'group' => $group
				);

				return $this->api_return([
					'data' => $data
				], 200);
            }
        }
        else
        {
            return $this->api_return([
				'data' => "Unable to connect to the server"
			], 401);
        }
    }

	function ADname()
	{
		$this->_apiConfig([
            'methods' => ['GET'],
            'requireAuthorization' => true
		]);

		$username = empty($this->input->get('username')) ? "" : $this->input->get('username');
		$employee_name = empty($this->input->get('name')) ? "" : $this->input->get('name');

		return $username == "" && $employee_name == "" ? $this->GetAllADUsers( false ) : $this->GetADByValue( $username, $employee_name );
	}

	function ADusername()
	{
		$this->_apiConfig([
            'methods' => ['GET'],
            'requireAuthorization' => true
		]);

		$username = empty($this->input->get('username')) ? "" : $this->input->get('username');
		$employee_name = empty($this->input->get('name')) ? "" : $this->input->get('name');

		return $username == "" && $employee_name == "" ? $this->GetAllADUsers( true ) : $this->GetADByValue( $username, $employee_name );
	}
}
