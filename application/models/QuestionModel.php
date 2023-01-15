<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class QuestionModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_a_question_by_id($question_id) {
        $this->db->where('question_id', $question_id);
        $query = $this->db->get('question');
        if ($query->num_rows() != 1)
            return false;
        
        $row = $query->row();
        $res = array('question_id' => $row->question_id,
                        'user_id' => $row->user_id,
                        'description' => $row->description, 
                        'title' => $row->title,
                        'created_date' => $row->created_date, 
                        'last_updated' => $row->last_updated);
        return $res;
    }

    public function create_a_question($user_id, $description, $title) {
        $now_date_time = date("Y-m-d H:i:s");
        $res = $this->db->insert('question', array('user_id' => $user_id, 
                                            'description' => $description, 
                                            'title' => $title, 
                                            'created_date' => $now_date_time, 
                                            'last_updated' => $now_date_time));
        
        return $res;
    }

    public function update_a_question($question_id, $description, $title) {
        $now_date_time = date("Y-m-d H:i:s");
        $updated_data = array('description' => $description, 
                                'title' => $title,
                                'last_updated' => $now_date_time);

        $this->db->where('question_id', $question_id);
        $res = $this->db->update('question', $updated_data);

        return $res;
    }

    public function delete_a_question($question_id) {
        $this->db->where('question_id', $question_id);
        $res = $this->db->update('question', array('status' => 0));

        return $res;
    }

    public function qusetion_exists($question_id) {
        $this->db->where('question_id', $question_id);
        $query = $this->db->get('question');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function is_question_active($question_id){
        $this->db->where('question_id', $question_id);
        $this->db->where('status', 1);
        $query = $this->db->get('question');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}