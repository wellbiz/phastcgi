<?php

class Test extends Controller {

	function index()
	{
        global $BM;
        $BM->mark('contr start');
        $this->load->helper('form');
        $BM->mark('form');
        $this->load->helper('cookie');

        $BM->mark('cookie');
        set_cookie('lol', 'ok');

        $this->load->view('form');

        $BM->mark('view');
	}
}

