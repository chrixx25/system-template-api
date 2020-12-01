<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model {

	protected $user;

	function __construct()
	{
        parent::__construct();
		$this->tbl = 'tblUsers';
		$this->vw = 'vwUsers';
    }

	function login($user)
	{
        $sql = "SELECT * FROM $this->tbl WHERE Username = ? ";
		$qry = $this->db->query($sql,"$user");

        return $qry->num_rows() > 0 ? $qry : FALSE;
	}

	function getUsers()
	{
		$sql = "SELECT * FROM $this->tbl";
		$qry = $this->db->query($sql);
		return $qry->num_rows() > 0 ? $qry : FALSE;
	}

	function getUserInfo($user_id)
	{
		$sql = "SELECT * FROM $this->tbl WHERE UserID = ?";
		$qry = $this->db->query($sql, $user_id);

		return $qry->num_rows() > 0 ? $qry : FALSE;
	}

	function insertUser($username, $name)
	{
		$sql = "INSERT INTO $this->tbl (Username, Name) VALUES(?,?)";
		$qry = $this->db->query($sql,array("$username", "$name"));
		$start = strrpos( $this->db->error()['message'], "]") + 1;
		$error = substr( $this->db->error()['message'], $start );

        return $qry ? $qry : $error;
	}

	function updateUser($username, $name, $id)
    {
        $sql = "UPDATE $this->tbl SET Username = ?, Name = ? WHERE UserID = ?";
		$qry = $this->db->query($sql,array("$username", "$name", $id));
		$start = strrpos( $this->db->error()['message'], "]") + 1;
		$error = substr( $this->db->error()['message'], $start );

        return $qry ? $qry : FALSE;
    }
}
