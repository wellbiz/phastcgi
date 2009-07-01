<?
class FastCGIRequest
{
    public function __construct($s)
    {
        $br = socket_read($s, 24);

/*
            unsigned char version; // 0
            unsigned char type; // 1
            unsigned char requestIdB1; // 2
            unsigned char requestIdB0; // 3
*/
        $content_length_ = ord($br[4]) * 16 + ord($br[5]);
        $padding_length_ = ord($br[6]);

/*
            unsigned char contentLengthB1; // 4
            unsigned char contentLengthB0; // 5
            unsigned char paddingLength; // 6
            unsigned char reserved; // 7
            unsigned char contentData[contentLength];
            unsigned char paddingData[paddingLength];
*/

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


