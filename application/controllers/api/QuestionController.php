<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class QuestionController extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('usermodel', 'UM');
        $this->load->model('questionmodel', 'QM');
    }

    public function index_get() {
        $question_id = $this->uri->segment(3);
        $res = $this->QM->get_a_question_by_id($question_id);

        if($res) {
            $this->response($res, RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'User Details Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function index_post() {
        $user_id = $this->post('user_id');
        $description = $this->post('description');
        $title = $this->post('title');

        if(!$this->UM->is_user_active($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->QM->create_a_question($user_id, $description, $title);

        if($res) {
            $this->response(['message'=>'Question Created Successful!'], RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'Question Created Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function index_put() {
        $question_id = $this->put('question_id');

        if(!$this->QM->qusetion_exists($question_id))
            $this->response(['message'=>'Question Not Found!'], RestController::HTTP_NOT_FOUND);

        $description = $this->put('description');
        $title = $this->put('title');

        $res = $this->QM->update_a_question($question_id, $description, $title);

        if($res)
            $this->response(['message'=>'Question Updated Successful!'], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Question Updated Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        
    }

    public function index_delete() {
        $question_id = $this->uri->segment(3);
        
        if(!$this->QM->qusetion_exists($question_id))
            $this->response(['message'=>'Question Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->QM->delete_a_question($question_id);

        if($res)
            $this->response(['message'=>'Question Deleted Successful!'], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Question Deleted Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }
}