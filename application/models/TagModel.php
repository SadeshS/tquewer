<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TagModel extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_tags() {
        $query = $this->db->get('tag');
        return $query->result();
    }

    public function get_tags_by_question_id($question_id) {
        $this->db->select('*');
        $this->db->from('tag');
        $this->db->join('question_tag', 'question_tag.tag_id = tag.tag_id');
        $this->db->where('question_id', $question_id);
        $query = $this->db->get();

        return $query->result();
    }

    public function create_a_tag($user_id, $title) {
        $res = $this->db->insert('tag', array('user_id' => $user_id,
                                            'title' => $title));
        
        return $res;
    }

    public function get_tag_by_title($title) {
        $this->db->where('title', $title);
        $query = $this->db->get('tag');
        return $query->result()[0];
    }

    public function add_tag_to_a_question($question_id, $tag_id) {
        $res = $this->db->insert('question_tag', array('question_id' => $question_id,
                                            'tag_id' => $tag_id));
        
        return $res;
    }

    public function remove_all_tags_related_to_a_question($question_id) {
        $this->db->where('question_id', $question_id);
        $this->db->delete('question_tag');
    }
}