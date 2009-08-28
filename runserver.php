<?
#require_once("phastcgi/error_handler.php");
require_once("phastcgi/fastcgi.php");

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
$maxpids = 4;
$pids = array();

$reply = new FastCGIReply();

function handle_connection($s)
{
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
                global $reply;
                $reply->send_reply($requests[$record->requestId], $connection);
                socket_close($connection);
                break;
            }
        }
        else
        {
            $requests[$record->requestId] = new FastCGIRequest($record);
        }
    }
}


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
