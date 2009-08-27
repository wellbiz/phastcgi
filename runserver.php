<?
#require_once("phastcgi/error_handler.php");
require_once("phastcgi/fastcgi.php");
require_once("settings.php");

$sn = '/tmp/tst.sock';
if (file_exists($sn)){
    unlink($sn);
}
$s = socket_create(AF_UNIX, SOCK_STREAM, 0);

socket_bind($s, $sn);

socket_listen($s, 16);
chmod($sn, 0777);

$requests = array();
$responders = array();

$requests = array();
$maxpids = 1;
$pids = array();

require_once("demo/Application.php");


function handle_connection($s)
{

    $application = new Application();
    $record = new FastCGIRecord();

    var_dump($s);

    $requests = array();
    while($connection = socket_accept($s))
    while(1)
    {
        $error = $record->read($connection);

        if($error)
        {
            socket_close($connection);
            break;
        }

        if (array_key_exists($record->requestId, $requests))
        {
            $is_last = $requests[$record->requestId]->process_record($record);
            if($is_last)
            {
#                print "last\n";
                $reply = new FastCGIReply($requests[$record->requestId]);
                $reply->send_reply($connection, $application);
                socket_close($connection);
                break;
            }
        }
        else
        {
            $requests[$record->requestId] = new FastCGIRequest($record);
        }
//        echo "len ".sizeof($requests)."\n";

    }
}


#while($connection = socket_accept($s))
while(1)
{
    $pid = pcntl_fork();
    if($pid)
    {
        $pids[$pid] = true;
    }
    else
    {
        handle_connection($s);
        exit(0);
    }

    do {
        $pid = pcntl_waitpid(-1, $status, count($pids) < $maxpids ? WNOHANG : 0);
        if(array_key_exists($pid, $pids))
            unset($pids[$pid]);
    } while($pid > 0);

}
    

/*
        $reads = array($connection);
        $writes = empty($responders) ? array() : array($connection);
        $excepts = array($connection);

        socket_select($reads, $writes, $excepts, 0);
        echo "reads: ";
        var_dump($reads);

        if(!empty($excepts))
        {
            print "Excepts\n";
            continue;
        }

        if(!empty($writes))
        {
            print "Writes\n";
            var_dump($writes);
            foreach($responders as $responder)
            {
                $reply = new FastCGIReply($responder->requestId);
                $response = $reply->get_response("lol ok");

                var_dump($response);
                if($response === NULL)
                {
                    socket_write($connection, FastCGIReply::end_request($responder->requestId), 16);
                }
                else
                {
                    $bytes = socket_write($connection, $response->get_raw_data());
                    print "$bytes written\n";
                }
            }
            continue;
        }

        
        if(!empty($reads))
        {

            $record = new FastCGIRecord();
            $record->read($connection);
            print "type: " . $record->type . "\n";

            if($record->type === FCGI_BEGIN_REQUEST)
            {
                $requests[$record->requestId] = new FastCGIRequest($record);
            }
            elseif($record->type === FCGI_STDIN and $record->contentLength === 0)
            {
                print "end of request\n";
                array_push($responders, $requests[$record->requestId]);
                unset($requests[$record->requestId]);
            }
            else
            {
                $requests[$record->requestId]->process_record($record);
            }
        }


    }

}
*/
