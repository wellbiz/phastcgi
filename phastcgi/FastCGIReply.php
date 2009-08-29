<?
class FastCGIReply
{
    public static $cookies = array();

    public function __construct()
    {
        include "system/init.php";
        $this->record = new FastCGIRecord();
    }

    public function send_reply($request, $connection)
    {
        $this->record->requestId = $request->requestId;
        $this->record->type = FCGI_STDOUT;

        $data = $this->get_data($request);

        $this->record->data = "";
        foreach(self::$cookies as $cookie)
        {
            $value = urlencode($cookie[1]);
            $this->record->data = "Set-Cookie: {$cookie[0]}={$value}";
            if($cookie[2] > 0)
            {
                $expires = strftime("%a, %d-%b-%Y %T GMT", $cookie[2]);
                $this->record->data .= "; expires={$expires}";
            }
            if($cookie[3] != "")
            {
                $this->record->data .= "; path={$cookie[3]}";
            }
            if($cookie[4] != "")
            {
                $this->record->data .= "; domain={$cookie[4]}";
            }
            $this->record->data .= "\r\n";
        }
        self::$cookies = array();

        $this->record->data .="Content-type: text/html\r\n\r\n";

#        echo $this->record->data;

        $this->record->send($connection);

        for($i=0; $i<strlen($data);$i += 16300)
        {
            $this->record->data = substr($data, $i, 16300);
            $this->record->send($connection);
        }

        $this->record->data = "";
        $this->record->send($connection);

        $this->record->type = FCGI_END_REQUEST;
        $this->record->data = pack(STRUCT_FCGI_END_REQUEST, 0, FCGI_REQUEST_COMPLETE);
        $this->record->send($connection);

#        print "reply sent\n";

        $_POST = array();
        $_COOKIE = array();
    }

    public function get_data($request)
    {
#        echo $request->headers["REMOTE_ADDR"]." ".$request->headers["PATH_INFO"]."\n";
#        var_dump($request->headers["HTTP_COOKIE"]);

        foreach($_SERVER as $key => $value)
            unset($_SERVER[$key]);

        foreach($request->headers as $key => $value)
            $_SERVER[$key]=$value;

        $_SERVER['argv'] = array('php', $_SERVER['REQUEST_URI']);

        ob_start();
        include "system/run.php";
        $buf =  ob_get_clean();
        return $buf;
    }
}
