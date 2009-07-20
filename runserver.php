<?
require_once("phastcgi/error_handler.php");
require_once("phastcgi/fastcgi.php");
require_once("settings.php");

$sn = '/tmp/tst.sock';
if (file_exists($sn)){
    unlink($sn);
}
$s = socket_create(AF_UNIX, SOCK_STREAM, 0);

socket_bind($s, $sn);

socket_listen($s, 3);
chmod($sn, 0777);

while($conn = socket_accept($s))
{
    $request = new FastCGIRequest($conn);

    var_dump($request);

    $reply = new FastCGIReply($conn);
    $reply->add_header("Content-type", "text/plain");
    $reply->send_data("Hello World\n");
    $reply->send_data("Yohoho");
    $reply->end_request();
}
