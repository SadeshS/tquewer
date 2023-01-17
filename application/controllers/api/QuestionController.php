<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class QuestionController extends RestController {

    public function __construct() {
        // to avoid the cors issues
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('usermodel', 'UM');
        $this->load->model('questionmodel', 'QM');
        $this->load->model('tagmodel', 'TM');
    }

    // getting a question by given question id
    public function index_get() {
        $question_id = $this->uri->segment(3);
        $res = $this->QM->get_a_question_by_id($question_id);

        if($res) {
            $this->response($res, RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'Question Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    // creating a question
    public function index_post() {
        $user_id = $this->post('user_id');
        $description = $this->post('description');
        $title = $this->post('title');
        $tags = $this->post('tags');

        // dont let inactive users to create
        if(!$this->UM->is_user_active($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->QM->create_a_question($user_id, $description, $title);

        if(!$res)
            $this->response(['message'=>'Question Created Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);

        // adding tags to the questions
        foreach ($tags as $tag_id) {
            $this->TM->add_tag_to_a_question($res, $tag_id);
        }

        $this->response(['message'=>'Question Created Successful!'], RestController::HTTP_OK);
    }

    // updating a question
    public function index_put() {
        $question_id = $this->put('question_id');

        // check whether the question exists
        if(!$this->QM->qusetion_exists($question_id))
            $this->response(['message'=>'Question Not Found!'], RestController::HTTP_NOT_FOUND);

        $description = $this->put('description');
        $title = $this->put('title');
        $tags = $this->put('tags');

        $res = $this->QM->update_a_question($question_id, $description, $title);

        if(!$res)
            $this->response(['message'=>'Question Updated Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);

        // removing all the previous tags related to question
        $this->TM->remove_all_tags_related_to_a_question($question_id);
        
        // adding new tags to the question
        foreach ($tags as $tag_id) {
            $this->TM->add_tag_to_a_question($question_id, $tag_id);
        }

        $this->response(['message'=>'Question Updated Successful!'], RestController::HTTP_OK);
    }

    // deleting a question
    public function index_delete() {
        $question_id = $this->uri->segment(3);
        
        // check whether the question exists
        if(!$this->QM->qusetion_exists($question_id))
            $this->response(['message'=>'Question Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->QM->delete_a_question($question_id);

        if($res)
            $this->response(['message'=>'Question Deleted Successful!'], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Question Deleted Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }

    // getting all the questions which a user has posted
    public function user_questions_get() {
        $user_id = $this->uri->segment(4);

        if(!$this->UM->is_user_active($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);
        
        $res = $this->QM->get_questions_by_user_id($user_id);

        if($res)
            $this->response(['questions'=>$res], RestController::HTTP_OK);
        else
            $this->response(['message'=>'Question Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }

    // getting the filtered and sorted questions 
    public function all_get() {
        $sort = $this->get('sort');
        $tag_filter= $this->get('tag_filter'); 
        $status_filter= $this->get('status_filter');
        $query= $this->get('query');

        $res = $this->QM->get_all_questions($sort, $tag_filter, $status_filter, $query);

        if($res)
            $this->response(['questions'=>$res], RestController::HTTP_OK);
        else
            $this->response([$res], RestController::HTTP_INTERNAL_ERROR);

    }
}