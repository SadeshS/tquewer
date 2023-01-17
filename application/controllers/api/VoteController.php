<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class VoteController extends RestController {

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
        $this->load->model('votemodel', 'VM');
    }

    // creating a vote
    public function index_post() {
        $user_id = $this->post('user_id');
        $question_id = $this->post('question_id');
        $answer_id = $this->post('answer_id');
        $is_up_vote = $this->post('is_up_vote');

        $res = $this->VM->create_a_vote($user_id, $question_id, $answer_id, $is_up_vote);

        if($res) {
            $this->response(['message'=>'Vote Casted Successful!'], RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'User Casted Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }
}