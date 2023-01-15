<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require (APPPATH . 'libraries/RestController.php');
require (APPPATH . 'libraries/Format.php');

class Me extends RestController {

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $this->response( ['Hello World!'], 200 );
    }
}