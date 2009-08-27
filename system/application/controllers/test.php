<?php

class Test extends Controller {

	function index()
	{
        $this->load->helper('form');
        $this->load->view('form');
	}
}

