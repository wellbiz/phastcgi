<?

define(FCGI_BEGIN_REQUEST, 1);
define(FCGI_ABORT_REQUEST, 2);
define(FCGI_END_REQUEST, 3);
define(FCGI_PARAMS, 4);
define(FCGI_STDIN, 5);
define(FCGI_STDOUT, 6);
define(FCGI_STDERR, 7);
define(FCGI_DATA, 8);
define(FCGI_GET_VALUES, 9);
define(FCGI_GET_VALUES_RESULT , 10);
define(FCGI_UNKNOWN_TYPE, 11);
define(FCGI_MAXTYPE, FCGI_UNKNOWN_TYPE);
define(FCGI_NULL_REQUEST_ID, 0);
define(FCGI_KEEP_CONN, 1);

define(FCGI_RESPONDER, 1);
define(FCGI_AUTHORIZER, 2);
define(FCGI_FILTER, 3);
define(FCGI_REQUEST_COMPLETE, 0);
define(FCGI_CANT_MPX_CONN, 1);
define(FCGI_OVERLOADED, 2);
define(FCGI_UNKNOWN_ROLE, 3);       

class FastCGIRecord
{
    public function __construct($s)
    {
        $data = socket_read($s, 8);
        $this->type = ord($data[1]);
        $this->requestId = ord($data[2]) * 256 + ord($data[3]);
        $this->contentLength = ord($data[4]) * 256 + ord($data[5]);
        $this->paddingLength = ord($data[6]);
    }
}

class FastCGIRequest
{
    public function __construct($s)
    {
        $rec = new FastCGIRecord($s);
        var_dump($rec);

        $content_length = ord($br[20]) * 256 + ord($br[21]);

        $data = socket_read($s, $content_length);
        $offset = 0;

        while($offset < $content_length)
        {
            $namelen = ord($data[$offset++]);
            if($namelen > 127)
            {
                $namelen = (($namelen & 0x7f) * 16777216) +
                            (ord($data[$offset++]) * 65536) + 
                            (ord($data[$offset++]) * 256) + 
                            ord($data[$offset++]);
            }

            $valuelen = ord($data[$offset++]);
            if($valuelen > 127)
            {
                $valuelen = (($valuelen & 0x7f) * 16777216) +
                            (ord($data[$offset++]) * 65536) + 
                            (ord($data[$offset++]) * 256) + 
                            ord($data[$offset++]);
            }

            $name = substr($data, $offset, $namelen);
            $offset += $namelen;
            $value = substr($data, $offset, $valuelen);
            $offset += $valuelen;

            $this->headers[$name] = $value;
        }
    }
}


