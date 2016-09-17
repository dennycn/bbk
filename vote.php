<?php
/*
@file: vote.php
@author:  bytemind@gmail.com wuqifu@gmail.com
@date: 2007-6-13 2016/9/15
@see:
*/

require_once('common/common_db.class.php');
require_once('header.php');
require_once('bbk_sql.php');
require_once("page_template.php");

$ip = '';
$pid = "";
$choosenum = "";
if(isset($_SERVER['REMOTE_ADDR']))
{
    $ip=$_SERVER['REMOTE_ADDR'];
}
if(isset($_GET['pid']))
{
    $pid = $_GET['pid'];
}
if(isset($_GET['1']))
{
    $choosenum = 1;
}
else if (isset($_GET['2']))
{
    $choosenum = 2;
}
$uid = GetUid();

function print_choose($pid, $choosenum, $ip, $uid)
{
    $r = GetVote($pid, $choosenum, $uid);
    $choose = $r["choose"];
    $query = $r["query"];
    print "<tr><td>";
    printf("para: pid=%s, choosenum=%s|%s, ip=%s, uid=%s, query=%s", $pid, $choosenum, $choose, $ip, $uid, $query);
    if($choose=="google")
    {
        print "<div align='center'><span class='greenFont'><br/>
        您选择的是谷歌&nbsp;</span><span class='darkFont'><strong>google.cn</strong></span>&nbsp;</div><br/>";
    }
    else if ($choose=="baidu")
    {
        print "<div align='center'><span class='greenFont'><br/>
        您选择的是百度&nbsp;</span><span class='darkFont'><strong>baidu.com</strong></span>&nbsp;</div><br/>";
    }
    else
    {
        print "<div align='center'><span class='greenFont'><br/>
        您选择的是搜狗&nbsp;</span><span class='darkFont'><strong>sogou.com</strong></span>&nbsp;</div><br/>";
        //您的投票时间已过，请重试搜索再进行投票，谢谢！</span>&nbsp;</div><br/>";
    }
    print "</td></tr>";

    if(isset($choose))
    {
        print "<tr><td>";
        $dbm = new CommonDB();
        $type = 0;
        $checkip_sql = "select count(*) as votesum from vote where ip='". $ip ."' and chooseTime>date_sub(now(),INTERVAL 1 HOUR)";
        $result=$dbm->SelectRows($checkip_sql);
        if($result[0]->votesum>MAX_VOTES_PER_HOUR)
        {
            print "<div align='center'><span class='greenFont'><br/>您的IP投票过快，本次投票不计入总数统计。</div><br/>";
            $type = 1;
        }
        $votesql="select count(*) as votesum from vote where uid=".$dbm->escape_string($uid)." and query='".$dbm->escape_string($query)."'";
        $result=$dbm->SelectRows($votesql);
        $flag=$dbm->begin();

        if($result[0]->votesum>0)
        {
            print "<div align='center'><span class='greenFont'><br/>您已对该关键字 \"<span class='darkFont'><b>".htmlspecialchars($query)."</b></span>\" 投过票了，本次投票不计入统计！</span>&nbsp;</div><br/>";
        }
        else
        {
            $inservote="insert into vote(query,uid,ip,choose,chooseTime,type) values ('".$dbm->escape_string($query)."',".$dbm->escape_string($uid).",'".$dbm->escape_string($ip)."','".$dbm->escape_string($choose)."',now()," . $type .")";
            $vid=$dbm->insertInternal($inservote);
        }


        $delsql="delete from pending where uid=".$dbm->escape_string($uid)." and query='".$dbm->escape_string($query)."' and pid=".$dbm->escape_string($pid);
        $dbm->deleteInternal($delsql);
        //	print $flag;
        if($flag==1)
        {
            $dbm->commit();
        }
        else
        {
            $dbm->rollback();
        }
        print "</td></tr>";
    }
}

// print return url
echo '<table class="tbl"><tr><td height="65" valign="middle" bgcolor="#eeeeee"><span class="blueklinkFont">&nbsp;&nbsp;<a href="index.php" target=_parent>&laquo;返回比比看主页</a></span></td></tr>';
// print choose: <tr><td>
echo '<tr><td>'.print_choose($pid, $choosenum, $ip, $uid).'</td></tr>';

// show choose stat
show_choose_stat(GetUid());
echo('</table>');

@require_once('foot.php');

?>
