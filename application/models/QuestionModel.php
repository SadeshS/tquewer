<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class QuestionModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // getting all question with filtering and sorting
    public function get_all_questions($sort='created_date', $tag_filter=null, $status_filter="1", $query=null) {
        $like_clause = 'WHERE status = '.$status_filter.' ';
        $query = '';

        if($query != null) {
            $like_clause = $like_clause." AND LOWER(title) LIKE LOWER('%".$query."%') ";
        }

        if($tag_filter == null) {
            $query = "SELECT q.question_id as question_id, q.title as title, q.description as description, q.user_id as user_id, q.is_solved as is_solved, q.created_date as created_date, q.last_updated as last_updated, ((SELECT COUNT(v.vote_id) FROM vote v WHERE v.question_id = q.question_id AND v.is_upvote = '1') - (SELECT COUNT(v.vote_id) FROM vote v WHERE v.question_id = q.question_id AND v.is_upvote = '0')) as vote_count, (SELECT COUNT(a.answer_id) FROM answer a WHERE a.question_id = q.question_id) as answer_count, (SELECT u.first_name FROM user u WHERE u.user_id = q.user_id) as first_name, (SELECT u.last_name FROM user u WHERE u.user_id = q.user_id) as last_name FROM question q ".$like_clause;
        } else {
            $query = "SELECT q.question_id as question_id, q.title as title, q.description as description, q.user_id as user_id, q.is_solved as is_solved, q.created_date as created_date, q.last_updated as last_updated, ((SELECT COUNT(v.vote_id) FROM vote v WHERE v.question_id = q.question_id AND v.is_upvote = '1') - (SELECT COUNT(v.vote_id) FROM vote v WHERE v.question_id = q.question_id AND v.is_upvote = '0')) as vote_count, (SELECT COUNT(a.answer_id) FROM answer a WHERE a.question_id = q.question_id) as answer_count, (SELECT u.first_name FROM user u WHERE u.user_id = q.user_id) as first_name, (SELECT u.last_name FROM user u WHERE u.user_id = q.user_id) as last_name  FROM question q ".$like_clause."AND q.question_id IN (SELECT qt.question_id FROM question_tag qt JOIN tag t ON t.tag_id = qt.tag_id WHERE t.title ='".$tag_filter."') ";
        }

        if($sort !='vote_count') {
            $query = $query."ORDER BY q.".$sort." DESC"; 
        } else {
            $query = $query."ORDER BY ".$sort." DESC"; 
        }

        $query_result = $this->db->query($query);

        return $query_result->result();
    }

    // getting question details by given question id
    public function get_a_question_by_id($question_id) {
        $query = $this->db->query("SELECT q.question_id as question_id, q.user_id as user_id, q.description as description, q.title as title, q.is_solved as is_solved, q.created_date as created_date, q.last_updated as last_updated, ((SELECT COUNT(v.vote_id) FROM vote v WHERE v.question_id = q.question_id AND v.is_upvote = '1') - (SELECT COUNT(v.vote_id) FROM vote v WHERE v.question_id = q.question_id AND v.is_upvote = '0')) as vote_count, (SELECT u.first_name FROM user u WHERE u.user_id = q.user_id) as first_name, (SELECT u.last_name FROM user u WHERE u.user_id = q.user_id) as last_name FROM question q WHERE q.question_id = ".$question_id);

        return $query->result()[0];
    }

    // getting all questions details by user id
    public function get_questions_by_user_id($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        $query = $this->db->get('question');

        return $query->result();
    }

    // creating a question
    public function create_a_question($user_id, $description, $title) {
        $now_date_time = date("Y-m-d H:i:s");
        $res = $this->db->insert('question', array('user_id' => $user_id, 
                                            'description' => $description, 
                                            'title' => $title, 
                                            'created_date' => $now_date_time, 
                                            'last_updated' => $now_date_time));

        if($res) {
            return $this->db->insert_id();
        }
        
        return $res;
    }

    // updating a question
    public function update_a_question($question_id, $description, $title) {
        $now_date_time = date("Y-m-d H:i:s");
        $updated_data = array('description' => $description, 
                                'title' => $title,
                                'last_updated' => $now_date_time);

        $this->db->where('question_id', $question_id);
        $res = $this->db->update('question', $updated_data);

        return $res;
    }

    // deleting a question
    public function delete_a_question($question_id) {
        $this->db->where('question_id', $question_id);
        $res = $this->db->update('question', array('status' => 0));

        return $res;
    }

    // check whether the question exists
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

    // check whether the question deleted or not
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