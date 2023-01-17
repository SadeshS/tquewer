<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnswerModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // getting an answer details by given answer id
    public function get_an_answer_by_id($answer_id) {
        $this->db->where('answer_id', $answer_id);
        $query = $this->db->get('answer');
        if ($query->num_rows() != 1)
            return false;
        
        $row = $query->row();
        $res = array('answer_id' => $row->answer_id,
                        'user_id' => $row->user_id,
                        'question_id' => $row->question_id,
                        'description' => $row->description,
                        'created_date' => $row->created_date, 
                        'last_updated' => $row->last_updated,
                        'is_solution' => $row->is_solution);
        return $res;
    }

    // getting all the answers details by given user id
    public function get_answers_by_user_id($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        $query = $this->db->get('answer');

        return $query->result();
    }

    // getting all the answers details by given question id
    public function get_answers_by_question_id($question_id) {
        $query = $this->db->query("SELECT a.question_id as question_id, a.user_id as user_id, a.description as description, a.answer_id as answer_id, a.is_solution as is_solution, a.created_date as created_date, a.last_updated as last_updated, ((SELECT COUNT(v.vote_id) FROM vote v WHERE v.answer_id = a.answer_id AND v.is_upvote = '1') - (SELECT COUNT(v.vote_id) FROM vote v WHERE v.answer_id = a.answer_id AND v.is_upvote = '0')) as vote_count, (SELECT u.first_name FROM user u WHERE u.user_id = a.user_id) as first_name, (SELECT u.last_name FROM user u WHERE u.user_id = a.user_id) as last_name FROM answer a WHERE a.question_id = ".$question_id." ORDER BY a.created_date DESC");

        return $query->result();
    }

    // creating an answer
    public function create_an_answer($user_id, $description, $question_id) {
        $now_date_time = date("Y-m-d H:i:s");
        $res = $this->db->insert('answer', array('user_id' => $user_id, 
                                            'description' => $description, 
                                            'question_id' => $question_id, 
                                            'created_date' => $now_date_time, 
                                            'last_updated' => $now_date_time));
        
        return $res;
    }

    // updating an answer
    public function update_an_answer($answer_id, $description) {
        $now_date_time = date("Y-m-d H:i:s");
        $updated_data = array('description' => $description,
                                'last_updated' => $now_date_time);

        $this->db->where('answer_id', $answer_id);
        $res = $this->db->update('answer', $updated_data);

        return $res;
    }

    // deleting an answer
    public function delete_an_answer($answer_id) {
        $this->db->where('answer_id', $answer_id);
        $res = $this->db->update('answer', array('status' => 0));

        return $res;
    }

    // check wether the answer exists
    public function answer_exists($answer_id) {
        $this->db->where('answer_id', $answer_id);
        $query = $this->db->get('answer');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    // check wether the answer deleted or not
    public function is_answer_active($answer_id){
        $this->db->where('answer_id', $answer_id);
        $this->db->where('status', 1);
        $query = $this->db->get('answer');
        if ($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}