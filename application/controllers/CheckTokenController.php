<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class CheckTokenController extends API_Controller
{
	function __construct()
	{
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
	}

	function checkToken()
	{
        // API Configuration
        header("Access-Control-Allow-Origin: *");
        $this->_apiConfig([
            'methods' => ['GET','OPTIONS'],
            'requireAuthorization' => true
        ]);

        return $this->api_return([
            'status' => true
        ],200);
    }
}
