<?
class Application
{
    public function __construct()
    {
        include "system/init.php";
    }

    public function get_data($request)
    {

#        echo $request->headers["REMOTE_ADDR"]." ".$request->headers["PATH_INFO"]."\n";

        foreach($_SERVER as $key => $value)
            unset($_SERVER[$key]);

        foreach($request->headers as $key => $value)
            $_SERVER[$key]=$value;

        $_SERVER['argv'] = array('php', $_SERVER['REQUEST_URI']);

        ob_start();
        
        include "system/run.php";

        $buf =  ob_get_clean();
        $_POST = array();
        return $buf;
    }
}
