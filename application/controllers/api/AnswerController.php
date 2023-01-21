<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class AnswerController extends RestController {

    public function __construct() {        
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('UserModel', 'UM');
        $this->load->model('AnswerModel', 'AM');
        $this->load->model('QuestionModel', 'QM');
    }

    // getting an answer by given answer id
    public function index_get() {
        $answer_id = $this->uri->segment(3);
        $res = $this->AM->get_an_answer_by_id($answer_id);

        if($res) {
            $this->response($res, RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'Answer Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    // creating an answer
    public function index_post() {
        $user_id = $this->post('user_id');
        $question_id = $this->post('question_id');
        $description = $this->post('description');

        // dont let inactive users to create
        if(!$this->UM->is_user_active($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->AM->create_an_answer($user_id, $description, $question_id);

        if($res) {
            $this->QM->update_last_updated_time($question_id);
            $this->response(['message'=>'Answer Created Successful!'], RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'Answer Created Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    // updating an answer
    public function index_put() {
        $answer_id = $this->put('answer_id');

        // check whether the answer exists
        if(!$this->AM->answer_exists($answer_id))
            $this->response(['message'=>'Answer Not Found!'], RestController::HTTP_NOT_FOUND);

        $description = $this->put('description');
        $question_id = $this->put('question_id');

        $res = $this->AM->update_an_answer($answer_id, $description);

        if($res) {
            $this->QM->update_last_updated_time($question_id);
            $this->response(['message'=>'Answer Updated Successful!'], RestController::HTTP_OK);
        }
        else
            $this->response(['message'=>'Answer Updated Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        
    }

    // deleting an answer by given id
    public function index_delete() {
        $answer_id = $this->uri->segment(3);
        
        // check whether the answer exists
        if(!$this->AM->answer_exists($answer_id))
            $this->response(['message'=>'Answer Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->AM->delete_an_answer($answer_id);

        if($res)
            $this->response(['message'=>'Answer Deleted Successful!'], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Answer Deleted Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }

    // getting all the answers which a user has posted
    public function user_answers_get() {
        $user_id = $this->uri->segment(4);

        // checks the user active or not
        if(!$this->UM->is_user_active($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);
        
        $res = $this->AM->get_answers_by_user_id($user_id);

        if($res)
            $this->response(['answers'=>$res], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Answers Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }

    // getting all the answers which users posted in a particular question
    public function question_answers_get() {
        $question_id = $this->uri->segment(4);
        
        $res = $this->AM->get_answers_by_question_id($question_id);

        if($res)
            $this->response(['answers'=>$res], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Answers Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }
}