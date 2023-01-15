<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_a_user_by_id($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('user');
        if ($query->num_rows() != 1)
            return false;
        
        $row = $query->row();
        $res = array('user_id' => $row->user_id,
                        'first_name' => $row->first_name, 
                        'last_name' => $row->last_name,
                        'email' => $row->email, 
                        'phone_number' => $row->phone_number);
        return $res;
    }

    public function create_a_user($first_name, $last_name, $email, $phone_number, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $res = $this->db->insert('user', array('first_name' => $first_name, 
                                            'last_name' => $last_name, 
                                            'email' => $email, 
                                            'phone_number' => $phone_number, 
                                            'password' => $hashed_password));
        
        return $res;
    }

    public function update_a_user($user_id, $first_name, $last_name, $email, $phone_number, $password=null) {
        $updated_data = array();
        if (is_null($password)) {
            $updated_data = array('first_name' => $first_name, 
                                    'last_name' => $last_name, 
                                    'email' => $email, 
                                    'phone_number' => $phone_number);
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $updated_data = array('first_name' => $first_name, 
                                    'last_name' => $last_name, 
                                    'email' => $email, 
                                    'phone_number' => $phone_number, 
                                    'password' => $hashed_password);
        }

        $this->db->where('user_id', $user_id);
        $res = $this->db->update('user', $updated_data);

        return $res;
    }

    public function delete_a_user($user_id) {
        $this->db->where('user_id', $user_id);
        $res = $this->db->update('user', array('status' => 0));

        return $res;
    }

    public function user_exists($user_id) {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function is_user_active($user_id){
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}