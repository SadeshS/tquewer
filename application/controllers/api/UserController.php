<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class UserController extends RestController {

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
    }

    // getting user details by user id
    public function index_get() {
        $user_id = $this->uri->segment(3);
        $res = $this->UM->get_a_user_by_id($user_id);

        if($res) {
            $this->response($res, RestController::HTTP_OK);
        } else {
            $this->response(['message'=>'User Details Retrieved Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }

    // creating a user
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

    // updating a user
    public function index_put() {
        $user_id = $this->put('user_id');

        // checks whether the use exists or not
        if(!$this->UM->user_exists($user_id))
            $this->response(['user'=>null, 'message'=>'User Not Found!'], RestController::HTTP_NOT_FOUND);

        $first_name = $this->put('first_name');
        $last_name = $this->put('last_name');
        $email = $this->put('email');
        $phone_number = $this->put('phone_number');
        $password = $this->put('password');

        $res = $this->UM->update_a_user($user_id, $first_name, $last_name, $email, $phone_number, $password);

        if($res)
            $this->response(['user'=>array('user_id'=>$user_id,
                                            'first_name'=>$first_name,
                                            'last_name'=>$last_name,
                                            'email'=>$email,
                                            'phone_number'=>$phone_number), 'message'=>'User Updated Successful!'], RestController::HTTP_OK);
        else
            $this->response(['user'=>null, 'message'=>'User Updated Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        
    }

    // deleting the user
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

    // checking the password and email for authentication
    public function auth_post() {
        $email = $this->post('email');
        $password = $this->post('password');

        $res = $this->UM->login_user($email, $password);

        if($res['status'])
            $this->response($res['user'], RestController::HTTP_OK);
        else {
            if ($res['error_code'] == 401 || $res['error_code'] == 404)
                $this->response(['user'=>$res['user'], 'message'=>$res['message']], $res['error_code']);

            $this->response(['user'=>null, 'message'=>'User Login Unsuccessful!'], RestController::HTTP_INTERNAL_ERROR);
        }
    }
}