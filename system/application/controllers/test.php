<?php

class Test extends Controller {
    
    function Test()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->helper('cookie');
        $this->load->library('session');

        echo 'constr';
    }

	function index()
	{
       
#var_dump($this->session);
var_dump($_COOKIE);

        $this->session->set_userdata('bla', 'lol');
#        set_cookie('test', 'ok');

        $this->load->view('form');
	}
}

