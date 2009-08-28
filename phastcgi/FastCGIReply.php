<?
class FastCGIReply
{
    public function __construct()
    {
        include "system/init.php";
        $this->record = new FastCGIRecord();
    }

    public function send_reply($request, $connection)
    {
        $this->record->requestId = $request->requestId;
        $this->record->type = FCGI_STDOUT;

        $this->record->data = "Content-type: text/html\r\n\r\n";
        $this->record->send($connection);

        $data = $this->get_data($request);

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
