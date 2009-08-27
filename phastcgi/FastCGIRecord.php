<?

class FastCGIRecord
{
	private $sock;
    public $requestId;
    public $params;
    public $data = "";
	
    public function __construct()
    {
        $this->version = FCGI_VERSION_1;
        $this->type = FCGI_UNKNOWN_TYPE;
        $this->paddingLength = 0;
        $this->contentLength = 0;
    }
    public function read($connection)
    {
        $this->data = "";
        $data = socket_read($connection, 8);

        if($data === "")
            return(true);

        $headers = unpack(
            "Cversion/".
            "Ctype/".
            "nrequestId/".
            "ncontentLength/".
            "CpaddingLength/".
            "Creserved/"
            , $data);


        $this->type = $headers['type'];
        $this->requestId = $headers['requestId'];
        $this->contentLength = $headers['contentLength'];

        if($this->contentLength > 0)
            $data = socket_read($connection, $this->contentLength + $headers['paddingLength']);

        if($this->type == FCGI_PARAMS)
        {
            $offset = 0;
            while($offset + $headers['paddingLength'] < $this->contentLength)
            {
                $namelen = ord($data[$offset++]);
                if($namelen > 127)
                {
                    $namelen = (($namelen & 0x7f) << 24) +
                                (ord($data[$offset++]) << 16) + 
                                (ord($data[$offset++]) << 8) + 
                                ord($data[$offset++]);
                }

                $valuelen = ord($data[$offset++]);
                if($valuelen > 127)
                {
                    $valuelen = (($valuelen & 0x7f) << 24) +
                                (ord($data[$offset++]) << 16) + 
                                (ord($data[$offset++]) << 8) + 
                                ord($data[$offset++]);
                }

                $name = substr($data, $offset, $namelen);
                $offset += $namelen;
                $value = substr($data, $offset, $valuelen);
                $offset += $valuelen;
                $this->params[$name] = $value;
            }
        }
        if($this->type == FCGI_STDIN and $this->contentLength > 0)
        {
            $this->data = substr($data, 0, $this->contentLength);
        }
        return false;
    }

    public function send($connection)
    {

        $this->contentLength = strlen($this->data);

        $data = pack("CCnnxx",
                $this->version,
                $this->type,
                $this->requestId,
                $this->contentLength
            );

        $bytes = socket_write($connection, $data.$this->data, $this->contentLength + 8);

#        hexdump($data.$this->data);

#        print "$bytes sent\n";
    }
}

