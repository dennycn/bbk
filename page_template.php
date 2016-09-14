<?php
require_once("bbk_sql.php");


function simulate_data()
{
    $results = array();
    for ($i = 0; $i < 4; $i++) {
        $url = 'www.9ku.com';
        $title = '9k';
        $snippet = 'fffff 9k ddddd';
        $dispurl = $url;
        $results[] = array("url" => $url, "title" => $title, "snippet" => $snippet,
                           "dispurl" => $dispurl);
    }
    //print_r($results);
    return $results;
}

// 最终的网页检索选择函数
function search($query, $engine) {
    //if ($engine == 'google')
    $engine = 'sogou';
    return simulate_data();

    // called python
    $program = 'python ./search/metasearch.py '.$engine.' '.$query;
    print($program."\t");
    $str = exec($program);
    $json_str = json_decode($str, true);
    //print($str);
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
    $result = search($query, $engine);
    return $result;
}

// show search box
function show_search_form()
{
    echo <<<EOD
    <form name="form1" method="get" action="bbksearch.php" onsubmit="return CheckForm();">
                                           <table id="search_box" width="98%">
                                                   <tr><td class="td_search_left" >
                                                               <img src="images/search_icon.gif" class="img_search" />&nbsp;
    <input id='query' type="text" name="query" class="searchbox"></td>
                                           <td class="td_search_right"><input type="submit" value="比比看" class="sureButton"/></td>
                                                           </tr></table></form>
                                                           EOD;
}

// choose stat: 排行榜/我的选择
function show_choose_stat($uid=NULL)
{
    $title = '排行榜';
    $sum = GetVoteSum($uid);
    $googlesum = $sum["google"];
    $baidusum = $sum["baidu"];
    $total_vote= $googlesum + $baidusum;
    //print "uid=".$uid.", total_vote=".$total_vote;

    if ($googlesum>=$baidusum) {
        $firstname = "谷歌";
        $firstvalue = $googlesum;
        $secname = "百度";
        $secvalue = $baidusum;
    }
    else {
        $firstname = "百度";
        $firstvalue = $baidusum;
        $secname = "谷歌";
        $secvalue = $googlesum;
    }
    $firstlen = $firstvalue;
    $seclen = $secvalue;
    $maxlen = max($firstvalue, $secvalue);
    if ($maxlen>200) {
        $firstlen *= (200/$maxlen);   //百分比
        $seclen *= (200/$maxlen);   //百分比
    }

    if ($uid) {
        $title = '你的选择';
        if ($total_vote == 0) {
            //return;
        }
    }

    //print '<table width="500" align="center" >';
    print '<tr><td ><table class="td_statpage" align="center">';
    $html = '<tr><td width="30%" ><strong class="greenFont">'.$title.'</strong></td>';
    $html .= '<td height="40"><span class="darkFont">选择次数</span>&nbsp;<strong class="redFont">';
    $html .= $total_vote;
    $html .= '</strong></td></tr>';
    // first tr
    $html .= '<tr class="tr_data"><td width="30%"><b>'.$firstname.'</b>搜索好</td>';
    $html .= '<td height="30"><span class="darkFont"><img src="images/bar3.gif" class="img_choose" ><img src="images/bar3.gif" width="';
    $html .= $firstlen;
    $html .= '" height="31" align="absmiddle"><img src="images/bar3_r.gif" class="img_choose" >&nbsp;';
    $html .= $firstvalue;
    $html .= '</span></td></tr>';
    // second tr
    $html .= '<tr class="tr_data"><td width="30%"><b>'.$secname.'</b>搜索好</td>';
    $html .= '<td height="30"><span class="darkFont"><img src="images/bar2_l.gif" class="img_choose" ><img src="images/bar2.gif" width="';
    $html .= $seclen;
    $html .= '" height="31" align="absmiddle"><img src="images/bar2_r.gif" class="img_choose" >&nbsp;';
    $html .= $secvalue;
    $html .= '</span></td></tr>';
    echo $html;
    print '</table></td></tr>';
}

function show_results($result, $query, $show_no=1)
{
    $html = '<td class="td_sidepage">';
    $html .= '<table width="97%"><tbody><tr><td class="td_data">';
    $html .= '<input type="submit" name="'.$show_no.'" value="这个结果最好" class="inputButton" />';
    $html .= '</td></tr>';
    $html .= '<tr><td><table width="100%"><tbody><tr><td>';  //<tr><td class="td_sidepage> </tody></table>
    print $html;
    show_choose_results($result, $query, $show_no);
    $html2 = '</td></tr></tbody></table>';
    $html2 .= '</td></tr></table><td>';
    print $html2;
}

// show sidepage one result
function show_choose_results($result, $query)
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
        $result_num=10;  // showResults at most 10 results
    }

    if ($result["finance"])
    {
        print $result["finance"];
    }

    $news_num=count($result["news"]);
    if($news_num>=4)
    {   // showResults at most 4 results
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
