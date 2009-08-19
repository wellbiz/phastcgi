<?

class FastCGIReply
{
    public $headers = array("X-Powered-by:" => "PhastCGI lol");
    public $finished = False;
    public $requestId = NULL;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function get_headers()
    {
        $record = new FastCGIRecord();
        $record->type = FCGI_STDOUT;
        foreach($this->headers as $key => $value)
        {
            $record->data .= $key.": ".$value."\r\n";
            print $key.": ".$value."\r\n";
        }
        $record->data .= "\r\n";
        $this->headers_sent = true;
        return $record;
    }

    public function send($connection)
    {
        
    }

    public static function end_request($id)
    {
        $record1 = new FastCGIRecord();
        $record1->type = FCGI_STDOUT;

        $record2 = new FastCGIRecord();
        $record2->type = FCGI_END_REQUEST;
        $record2->data = "\x00\x00\x00\x00\x00\x00\x00\x00";

        return $record1->get_raw_data().$record2->get_raw_data();
    }
}

