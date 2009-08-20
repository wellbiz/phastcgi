<?
# record types
define('FCGI_BEGIN_REQUEST', 1);
define('FCGI_ABORT_REQUEST', 2);
define('FCGI_END_REQUEST', 3);
define('FCGI_PARAMS', 4);
define('FCGI_STDIN', 5);
define('FCGI_STDOUT', 6);
define('FCGI_STDERR', 7);
define('FCGI_DATA', 8);
define('FCGI_GET_VALUES', 9);
define('FCGI_GET_VALUES_RESULT' , 10);
define('FCGI_UNKNOWN_TYPE', 11);
define('FCGI_MAXTYPE', FCGI_UNKNOWN_TYPE);
define('FCGI_NULL_REQUEST_ID', 0);

define('FCGI_VERSION_1', 1);
define('FCGI_KEEP_CONN', 1);

define('FCGI_RESPONDER', 1);
define('FCGI_AUTHORIZER', 2);
define('FCGI_FILTER', 3);

define('FCGI_REQUEST_COMPLETE', 0);
define('FCGI_CANT_MPX_CONN', 1);
define('FCGI_OVERLOADED', 2);
define('FCGI_UNKNOWN_ROLE', 3);       

define('STRUCT_FCGI_END_REQUEST', "NCxxx");

require_once("FastCGIRecord.php");
require_once("FastCGIRequest.php");
require_once("FastCGIReply.php");


function hexdump ($data, $htmloutput = false, $uppercase = false, $return = false)
{
    // Init
    $hexi   = '';
    $ascii  = '';
    $dump   = ($htmloutput === true) ? '<pre>' : '';
    $offset = 0;
    $len    = strlen($data);

    // Upper or lower case hexadecimal
    $x = ($uppercase === false) ? 'x' : 'X';

    // Iterate string
    for ($i = $j = 0; $i < $len; $i++)
    {
        // Convert to hexidecimal
        $hexi .= sprintf("%02$x ", ord($data[$i]));

        // Replace non-viewable bytes with '.'
        if (ord($data[$i]) >= 32) {
            $ascii .= ($htmloutput === true) ?
                            htmlentities($data[$i]) :
                            $data[$i];
        } else {
            $ascii .= '.';
        }

        // Add extra column spacing
        if ($j === 7) {
            $hexi  .= ' ';
            $ascii .= ' ';
        }

        // Add row
        if (++$j === 16 || $i === $len - 1) {
            // Join the hexi / ascii output
            $dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);

            // Reset vars
            $hexi   = $ascii = '';
            $offset += 16;
            $j      = 0;

            // Add newline
            if ($i !== $len - 1) {
                $dump .= "\n";
            }
        }
    }

    // Finish dump
    $dump .= $htmloutput === true ?
                '</pre>' :
                '';
    $dump .= "\n";

    // Output method
    if ($return === false) {
        echo $dump;
    } else {
        return $dump;
    }
}
