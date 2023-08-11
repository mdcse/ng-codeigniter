<?php
defined('BASEPATH') OR exit('DIRECT SCRIPT ACCESS IS NOT ALLOWED');

class Student_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function _insert($data) {
        $this->db->insert('student', $data);
    }

    function if_exist($data) {
        $where = "username='$data->username' OR email='$data->email'";
        $query = array('username', 'email');
        $this->db->select($query);
        $this->db->where($where);
        $obj = $this->db->get('student');
        return $obj->row_array();

    }

    function if_username_exist($username) {
        $this->db->where('username', $username);
        $obj = $this->db->get('student');
        return $obj->row_array();
    }

    function if_email_exist($email) {
        $this->db->where('email', $email);
        $obj = $this->db->get('student');
        return $obj->row_array();
    }

    public function _get() {
        $obj = $this->db->get('student');
        return $obj->result_array();
    }

    public function _search($key) {
        $query = array('name' => $key, 'email' => $key, 'username' => $key);
        $this->db->like($query);
        $obj = $this->db->get('student');
        return $obj->result_array();
    }

    public function _delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('student');
    }

    public function get_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('student');
        return $query->row_array();
    }
    
    function _update($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('student', $data);
    }

    function _authenticate($data) {
        $this->db->where('username', $data->username);
        $this->db->where('password', $data->password);
        $obj = $this->db->get('student');
        return $obj->row_array();
    }

    function _set_token($username, $token) {
        $this->db->where('username', $username);
        $this->db->update('student', $token);
    }

    function _validate_token($username, $token) {
        $this->db->where('username', $username);
        $this->db->where('token', $token);
        $obj = $this->db->get('student');
        return ($obj->num_row()) ? true : false;
    }

    function _unset_token($token) {
        $this->db->where('token', $token);
        $this->db->update('student', array('token' => ''));
    }

    function _is_login($token) {
        $this->db->where('token', $token);
        $obj = $this->db->get('student');
        if($obj->num_rows() > 0){
            return true;
        } else {
            return false;
        }
        
    }
}
