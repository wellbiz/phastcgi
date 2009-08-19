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
        $data = socket_read($connection, 8);
        if($data === "")
            return(TRUE);
        $this->type = ord($data[1]);
        $this->requestId = (ord($data[2]) << 8) + ord($data[3]);
        $this->contentLength = (ord($data[4]) << 8) + ord($data[5]);
        $this->paddingLength = ord($data[6]);

        if($this->contentLength > 0)
            $data = socket_read($connection, $this->contentLength);

        if($this->type == FCGI_PARAMS)
        {
            $offset = 0;
            while($offset < $this->contentLength)
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
        if($this->type == FCGI_STDIN)
        {
            $this->data = socket_read($connection, $this->contentLength);
        }
        return FALSE;
    }

    public function send($connection)
    {

        $this->contentLength = strlen($this->data);

        $data = pack("CCnnnC",
                $this->version,
                $this->type,
                $this->requestId,
                $this->contentLength,
                $this->paddingLength,
                0
            );
        socket_write($connection, $data . $this->data);
    }
}

