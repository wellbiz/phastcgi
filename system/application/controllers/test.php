<?php

class Test extends Controller {
    
    function Test()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->helper('cookie');
        $this->load->library('session');
    }

	function index()
	{
        global $BM;

#        set_cookie('test', 'ok');
        var_dump($this->session->userdata);

#        $this->session->set_userdata('bla1', 'lol1');
        $this->load->view('form');
	}
}

