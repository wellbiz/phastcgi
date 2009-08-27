<?
class Application
{
    public function __construct()
    {
        include "system/init.php";
    }

    public function get_data($request)
    {

        foreach($_SERVER as $key => $value)
            unset($_SERVER[$key]);

        foreach($request->headers as $key => $value)
            $_SERVER[$key]=$value;

        $site_root = "/phast/";
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($site_root));
        $_SERVER['argv'] = array('php', $_SERVER['REQUEST_URI']);


        ob_start();
        
        include "system/run.php";

        $buf =  ob_get_clean();
        return $buf;
    }
}
