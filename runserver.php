<?
require_once("phastcgi/fastcgi.php");
require_once("settings.php");

$sn = '/tmp/tst.sock';
unlink($sn);
$s = socket_create(AF_UNIX, SOCK_STREAM, 0);

socket_bind($s, $sn);

socket_listen($s, 3);
chmod($sn, 0777);

while($conn = socket_accept($s))
{
    $request = new FastCGIRequest($conn);

    var_dump($request);

    $rec = new FastCGIRecord();
    $rec->type = FCGI_STDOUT;
    $rec->add_data("Content-type: text/plain\r\n\r\n");
    $rec->write($conn);

    $rec = new FastCGIRecord();
    $rec->type = FCGI_STDOUT;
    $rec->write($conn);

    $rec = new FastCGIRecord();
    $rec->type = FCGI_END_REQUEST;
    $rec->write($conn);
}
