<?

class FastCGIReply
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function send($connection)
    {
        $record = new FastCGIRecord();
        $record->requestId = $this->request->requestId;
        $record->type = FCGI_STDOUT;
        $record->data = "Content-type: text/html\r\n\r\nlol ok";
        $record->send($connection);

        $record->data = "";
        $record->send($connection);

        $record->type = FCGI_END_REQUEST;
        $record->data = pack(STRUCT_FCGI_END_REQUEST, 0, FCGI_REQUEST_COMPLETE);
        $record->send($connection);

#        print "reply sent\n";
        
    }

}

