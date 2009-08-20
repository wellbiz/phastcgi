<?
class Application
{
    public function __construct()
    {
        $this->db = mysql_connect("localhost", "phastcgi");
        mysql_select_db("phastcgi");
    }

    public function get_data($request)
    {
#        $res = mysql_query("INSERT INTO test SET num={$request->requestId}, text='ololo ololo'");
        $res = mysql_query("SELECT * FROM test");
        ob_start();
        ?><table><?
        while($row = mysql_fetch_row($res))
        {
            ?>
                <tr>
                <td><?=$row[0] ?>
                <td><?=$row[1] ?>
                <td><?=$row[2] ?>
                </tr>
            <?
        }
        $ret = ob_get_clean();
        return $ret;
    }
}
