<?

class FastCGIRequest
{
    public $requestId;
    public $headers = array();

    public function __construct($record)
    {
        $this->requestId = $record->requestId;
    }

    public function process_record($record)
    {
        if ($record->type === FCGI_PARAMS and is_array($record->params))
        {
            $this->headers = array_merge($this->headers, $record->params);
        }
        elseif($record->type == FCGI_STDIN)
        {
            if($record->data === FALSE)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function get_response()
    {
        return "ok\n";
    }
}

