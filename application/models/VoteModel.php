<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VoteModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_a_vote($user_id, $question_id, $answer_id, $is_up_vote) {
        $now_date_time = date("Y-m-d H:i:s");
        $res = $this->db->insert('vote', array('user_id' => $user_id,
                                                    'question_id' => $question_id,
                                                    'answer_id' => $answer_id,
                                                    'is_upvote' => $is_up_vote,
                                                    'created_date' => $now_date_time));
        
        return $res;
    }
}