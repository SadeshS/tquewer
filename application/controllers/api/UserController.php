<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class UserController extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('usermodel', 'UM');
    }

    public function index_get() {
        $user_id = $this->uri->segment(3);
        $res = $this->UM->get_a_user_by_id($user_id);

        if($res) {
            $this->response($res, RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'User Details Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function index_post() {
        $first_name = $this->post('first_name');
        $last_name = $this->post('last_name');
        $email = $this->post('email');
        $phone_number = $this->post('phone_number');
        $password = $this->post('password');

        $res = $this->UM->create_a_user($first_name, $last_name, $email, $phone_number, $password);

        if($res) {
            $this->response(['message'=>'User Created Successful!'], RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'User Created Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    public function index_put() {
        $user_id = $this->put('user_id');

        if(!$this->UM->user_exists($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $first_name = $this->put('first_name');
        $last_name = $this->put('last_name');
        $email = $this->put('email');
        $phone_number = $this->put('phone_number');
        $password = $this->put('password');

        $res = $this->UM->update_a_user($user_id, $first_name, $last_name, $email, $phone_number, $password);

        if($res)
            $this->response(['message'=>'User Updated Successful!'], RestController::HTTP_OK);
        else
            $this->response(['message'=>'User Updated Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        
    }

    public function index_delete() {
        $user_id = $this->uri->segment(3);
        
        if(!$this->UM->user_exists($user_id))
            $this->response(['message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $res = $this->UM->delete_a_user($user_id);

        if($res)
            $this->response(['message'=>'User Deleted Successful!'], RestController::HTTP_OK);
        else
            $this->response(['message'=>'User Deleted Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
    }
}