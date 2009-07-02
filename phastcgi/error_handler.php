<?php
/**
* Exeption handler for errors
*/
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new FastCGIException($errstr, 0, $errno, $errfile, $errline);
}

class FastCGIException extends ErrorException {

}

set_error_handler("exception_error_handler");

?>