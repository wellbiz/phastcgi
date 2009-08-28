<?php

class Test extends Controller {

	function index()
	{
        $this->load->helper('form');
        $this->load->helper('cookie');

        set_cookie(array('lol' => 'ok'));
        $this->load->view('form');


	}
}

