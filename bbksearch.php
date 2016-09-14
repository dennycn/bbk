<?php
// 实际的搜索实现文件
//header("Content-Type: text/html; charset=UTF-8");
//require_once("search/baidu.php");
//require_once("sogou.php");
//require_once("search/google.php");
require_once("bbk_sql.php");
require_once('header.php');

$query="";
if(isset($_GET["query"]))
{
    $query=$_GET['query'];
}
if($query=="")
{   //Redirect browser
    header("Location: index.php");
}
$uid = GetUid();

$order=randOrder(1);
//print $order;
if ($order=="1")
{
$order_one="google";
$order_two="baidu";
}
else
{
$order_one="baidu";
$order_two="google";
}
$showOrder=$order_one."".$order_two;
if($query!="" && $uid!="")
{
$pid = pending($uid, $showOrder, $query);
}
?>

<body>
<div class="RoundedCorner">
<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom:8px">
<tr>
<td height="65" valign="middle" id="search_td" >&nbsp;
&nbsp;
<span class="blueklinkFont"><a href="index.php" target=_parent>&laquo;
返回比比看主页</a></span></td>
</tr>
<tr>
<td height="35"><form name="form1" method="get" action="bbksearch.php" onsubmit="return CheckForm();">
<table id="search_box" width="98%" border="0" align="center">
<tr>
<td width="50%" height="35" align="right" valign="middle" style="padding-left:5px;"><img src="images/search_icon.gif" width="24" height="25" align="absmiddle" />&nbsp;
<input type="text" id="query" name="query" class="searchbox" value="<?php print htmlspecialchars($query); ?>
"></td>
<td width="50%" align="left" valign="middle" style="padding-left:5px;">
<input type="submit" name="Submit" value="比比看" class="sureButton" /> <font size=-1 color=#cc0000><b>←如果两边差不多, 再试试搜别的看?</b></font>
</td>
</tr>
</table>
</form>	  </td>
</tr>
</table>

<?php

$result_one = get_search_result($query, $order_one);
$result_two = get_search_result($query, $order_two);
if ($result_one==NULL || $result_two==NULL) {
    // avoid displaying only one side result.
    $result_one=NULL;
    $result_two=NULL;
    print "<p align=\"center\"><b><h3>Server error</h3></b></p>" .
    "<br><br><b class=\"rbottom\"><b class=\"r4\"></b>" .
    "<b class=\"r3\"></b><b class=\"r2\"></b><b class=\"r1\"></b></b>" .
    "</div></body></html>";
    return;
}
?>

<table width="97%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFC35B" style="table-layout:fixed">
<form name="form2" method="post" action="vote.php" target=_parent><tr>
<td width="50%" valign="top" style="padding-top:5px;">
<table width="97%" border="0" align="center">
<tr>
<td height="30" align="center" bgcolor="#EEEEEE"><span style="padding-left:10px; padding-top:5px;">
<input name="pid" type="hidden" id="pid" value="<?php print $pid;?>" />
<input type="submit" name="1" value="这个结果最好" class="inputButton" />
</span></td>
</tr>
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td style="word-break:break-all;width:600px; line-height:25px;  overflow:auto;"><?php
display($result_one, $query);
?>
</td>
</tr>
</table></td>
</tr>
</table></td>
<td valign="top" style="padding-top:5px;"><table width="97%" border="0" align="center">
<tr>
<td height="30" align="center" bgcolor="#EEEEEE"><input type="submit" name="2" value="这个结果最好" class="inputButton" /></td>
</tr>
<tr>
<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td style="word-break:break-all;width:600px; line-height:25px;  overflow:auto;"><?php
  display($result_two, $query);
?>
</td>
</tr>
</table></td>
</tr>
</table></td>
</tr></form></table>
<br>
<br>
<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
</div>
</body>
</html>
<?php

function simulate_data()
{
    $results = array();
    for ($i = 0; $i < 4; $i++){
        $url = 'www.9ku.com';
        $title = '9k';
        $snippet = 'fffff 9k ddddd';
        $dispurl = $url;
        $results[] = array("url" => $url, "title" => $title, "snippet" => $snippet,
                           "dispurl" => $dispurl);
    }
    print_r($results);
    return $results;
}

// 最终的网页检索选择函数
function search($query, $engine) {
    //if ($engine == 'google')
        $engine = 'sogou';
    //return simulate_data();

    // called python
    $program = 'python ./search/metasearch.py '.$engine.' '.$query;
    print($program."\t");
    $str = exec($program);
    $json_str = json_decode($str, true);
    print($str);
    //var_dump($json_str);
    printf("len=%d<br>", count($json_str));
    //return $str;
    return $json_str;

    if ($engine == "google") {
        return search_google($query);
    }
    else if ($engine == "baidu") {
        return search_baidu($query);
    }
    else {
        return array();
    }
}

function get_search_result($query, $engine) {
    if($query=="")
    {
        return NULL;
    }
    $result = search($query,$engine);
    return $result;
}

function display($result, $query)
{
    // 不同频道有不同的显示模板，现分为news/finance/
    $result_num = count ($result);
    if ($result_num == 0)
        return;

    if ($result["news"]!=NULL) {
        $result_num--;
    }
    if($result_num>=10)
    {
        $result_num=10;  // display at most 10 results
    }

    if ($result["finance"])
    {
        print $result["finance"];
    }

    $news_num=count($result["news"]);
    if($news_num>=4)
    {   // display at most 4 results
        $news_num=4;
    }
    if($news_num>0)
        print "<strong><span class='grayFont'>".htmlspecialchars($query)."的相关新闻  - 今日焦点新闻</span></strong><br />";

    for($k=0; $k<$news_num; $k++)
    {
        print "<div class='newslist'><span class='bluelink'>&nbsp;&nbsp;<a href=".$result["news"][$k]["url"].">".$result["news"][$k]["title"]."</a>&nbsp;&nbsp;</span><span class='grayFont'>". $result["news"][$k]["source"]."&nbsp;	".$result["news"][$k]["time"] ."</span><br /></div>";
    }

    print "<br/>";
    for($i=0; $i<$result_num; $i++)
    {
        $title=$result[$i]["title"];
        print "<div class='resultlist'><a href='".$result[$i]["url"]."' target='_blank'>".$title."</a>";
        if ($result[$i]["snippet"] != "")
        print "<br/><span class='darkFont'>".$result[$i]["snippet"] . "</span>";
        print "<br/><span class='greenFont'>".$result[$i]["dispurl"] . "</span></div><br/>";
    }

}
?>
