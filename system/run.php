<?

global $BM;
global $CFG;
global $URI;
global $RTR;
global $EXT;
global $OUT;
global $IN;
global $LANG;

/*
 * ------------------------------------------------------
 *	Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */

if ($EXT->_call_hook('cache_override') === FALSE)
{
	if ($OUT->_display_cache($CFG, $URI) == TRUE)
	{
		return;
	}
}
$RTR->_set_routing();

// Load the local application controller
// Note: The Router class automatically validates the controller path.  If this include fails it 
// means that the default controller in the Routes.php file is not resolving to something valid.
$fname = APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT;
if ( ! file_exists($fname))
{
	show_error("Unable to load your default controller.  Please make sure the controller specified in your Routes.php file is valid.($fname)");
    return;
}

require_once(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT);

// Set a mark point for benchmarking
$BM->mark('loading_time_base_classes_end');


/*
 * ------------------------------------------------------
 *  Security check
 * ------------------------------------------------------
 *
 *  None of the functions in the app controller or the
 *  loader class can be called via the URI, nor can
 *  controller functions that begin with an underscore
 */
$class  = $RTR->fetch_class();
$method = $RTR->fetch_method();

if ( ! class_exists($class)
	OR $method == 'controller'
	OR strncmp($method, '_', 1) == 0
	OR in_array(strtolower($method), array_map('strtolower', get_class_methods('Controller')))
	)
{
	show_404("{$class}/{$method}");
}

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('pre_controller');

/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested method
 * ------------------------------------------------------
 */

// Mark a start point so we can benchmark the controller
$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

$CI = new $class();

// Is this a scaffolding request?
if ($RTR->scaffolding_request === TRUE)
{
	if ($EXT->_call_hook('scaffolding_override') === FALSE)
	{
		$CI->_ci_scaffolding();
	}
}
else
{
	/*
	 * ------------------------------------------------------
	 *  Is there a "post_controller_constructor" hook?
	 * ------------------------------------------------------
	 */
	$EXT->_call_hook('post_controller_constructor');
	
	// Is there a "remap" function?
	if (method_exists($CI, '_remap'))
	{
		$CI->_remap($method);
	}
	else
	{
		// is_callable() returns TRUE on some versions of PHP 5 for private and protected
		// methods, so we'll use this workaround for consistent behavior
		if ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($CI))))
		{
			show_404("{$class}/{$method}");
		}

		// Call the requested method.
		// Any URI segments present (besides the class/function) will be passed to the method for convenience
		call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
	}
}

// Mark a benchmark end point
$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');

/*
 * ------------------------------------------------------
 *  Is there a "post_controller" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('post_controller');

/*
 * ------------------------------------------------------
 *  Send the final rendered output to the browser
 * ------------------------------------------------------
 */

if ($EXT->_call_hook('display_override') === FALSE)
{
	$OUT->_display();
}

/*
 * ------------------------------------------------------
 *  Is there a "post_system" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('post_system');

/*
 * ------------------------------------------------------
 *  Close the DB connection if one exists
 * ------------------------------------------------------
 */
if (class_exists('CI_DB') AND isset($CI->db))
{
	$CI->db->close();
}


/* End of file CodeIgniter.php */
/* Location: ./system/codeigniter/CodeIgniter.php */
