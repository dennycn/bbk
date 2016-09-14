<?php
// 实际的搜索实现文件
//header("Content-Type: text/html; charset = UTF-8");
//require_once("search/sogou.php");
require_once('header.php');
require_once("page_template.php");
require_once("common/common.php");
require_once("bbk_sql.php");

$query = "";
if(isset($_GET["query"]))
{
    $query = $_GET['query'];
}
if($query == "")
{   //Redirect browser
    header("Location: index.php");
}

$order = randOrder(1);
//print $order;
if ($order == "1")
{
    $order_one = "google";
    $order_two = "baidu";
}
else
{
    $order_one = "baidu";
    $order_two = "google";
}
$showOrder = $order_one."".$order_two;
$uid = GetUid();
$pid = '';
if($query != "" && $uid != "")
{
    $pid = pending($uid, $showOrder, $query);
}
?>


<table class = "tbl">
<tr valign = "left" text-align = "left"><td >&nbsp;&nbsp;<span class = "blueklinkFont"><a href = "index.php" target = _parent>&laquo;返回比比看主页</a></span></td></tr>
<tr><td><?php show_search_form() ?> </td></tr>
</table>

<?php
//show_search_form();

// show search result
$result_one = get_search_result($query, $order_one);
$result_two = get_search_result($query, $order_two);
if ($result_one == NULL || $result_two == NULL) {
    // avoid displaying only one side result.
    $result_one = NULL;
    $result_two = NULL;
    print "<p align = \"center\"><b><h3>Server error</h3></b></p>" .
    "<br><br><b class = \"rbottom\"><b class = \"r4\"></b>" .
    "<b class = \"r3\"></b><b class = \"r2\"></b><b class = \"r1\"></b></b>" .
    "</div></body></html>";
    return;
}

print('<table class = "tbl">');
print('<form name = "form2" method = "get" action = "vote.php" target = _parent>');
print '<input name = "pid" type = "hidden" id = "pid" value = "'.$pid.'" /><tbody>';
show_results($result_one, $query, 1);
show_results($result_two, $query, 2);
print('</tbody></form></table>');

@require_once('foot.php');

?>
