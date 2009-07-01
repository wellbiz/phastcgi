<?
require_once('phastcgi/middleware/Common.php');

$middlewares = array(
    new Common()
);

require_once('demo/Application.php');

$applications = array(
    new Demo()
);
