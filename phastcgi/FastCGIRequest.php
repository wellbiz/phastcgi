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
            if(!$record->data)
                return TRUE;

            if(array_key_exists('HTTP_CONTENT_TYPE', $this->headers))
            {
                switch($this->headers['HTTP_CONTENT_TYPE'])
                {
                    case 'application/x-www-form-urlencoded':
                        $_POST = $this->parse_form_data($record->data);
                }
            }
            $this->data = $record->data;
        }
        return FALSE;
    }
    private function parse_form_data($data)
    {
        $form_data = array();
        foreach(explode('&', $data) as $line)
        {
            list($name, $value) = explode('=', $line);
            $form_data[$name] = $value;
        }
        return $form_data;
    }

}
