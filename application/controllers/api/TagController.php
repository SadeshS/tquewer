<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class TagController extends RestController {

    public function __construct() {        
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('UserModel', 'UM');
        $this->load->model('TagModel', 'TM');
    }

    // getting the all tags
    public function index_get() {
        $res = $this->TM->get_all_tags();

        if($res) {
            $this->response($res, RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'Tags Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    // creating a tag
    public function index_post() {
        $user_id = $this->post('user_id');
        $title = $this->post('title');

        // checks the user active or not
        if(!$this->UM->is_user_active($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->TM->create_a_tag($user_id, $title);

        if($res) {
            $tag = $this->TM->get_tag_by_title($title);
            $this->response(['tag'=>$tag], RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'Tag Created Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    // getting all the tags related to a particular question
    public function question_tags_get() {
        $question_id = $this->uri->segment(4);
        
        $res = $this->TM->get_tags_by_question_id($question_id);

        if($res)
            $this->response(['tags'=>$res], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Tag Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }
}