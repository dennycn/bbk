<?php
// bytemind@gmail.com

@require_once('header.php');
require_once('database/common_db.class.php');
require_once('util.php');
if(isset($_SERVER['REMOTE_ADDR']))
{
    $ip=$_SERVER['REMOTE_ADDR'];
}
$uid = GetUid();

?>

<body>
<div class="RoundedCorner"> <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr><td height="65" valign="middle" bgcolor="#eeeeee"><span class="blueklinkFont">&nbsp;&nbsp;
<a href="index.php" target=_parent>&laquo;
返回比比看主页</a></span></td>
</tr>
<tr>
<td height="35"><?php
           $pid = "";
$choosenum = "";
if(isset($_POST['pid']))
{
    $pid = $_POST['pid'];
}
if(isset($_POST['1']))
{
    $choosenum = 1;
}
else if (isset($_POST['2']))
{
    $choosenum = 2;
}

$r = GetVote($pid, $choosenum, $uid);
$choose = $r["choose"];
$query = $r["query"];

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
    您的投票时间已过，请重试搜索再进行投票，谢谢！</span>&nbsp;</div><br/>";
}


?>
<?php
if(isset($choose))
{
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
    //print $votesql;
    //exit();
    $result=$dbm->SelectRows($votesql);
    //print $votesum;
    //exit();
    $flag=$dbm->begin();

    if($result[0]->votesum>0)
    {
        print "<div align='center'><span class='greenFont'><br/>您已对该关键字 \"<span class='darkFont'><b>".htmlspecialchars($query)."</b></span>\" 投过票了，本次投票不计入统计！</span>&nbsp;</div><br/>";
    }
    else
    {
        $inservote="insert into vote(query,uid,ip,choose,chooseTime,type) values ('".$dbm->escape_string($query)."',".$dbm->escape_string($uid).",'".$dbm->escape_string($ip)."','".$dbm->escape_string($choose)."',now()," . $type .")";
        //	print $inservote;
        $vid=$dbm->insertInternal($inservote);
        //exit();
        //print $vid;
    }


    $delsql="delete from pending where uid=".$dbm->escape_string($uid)." and query='".$dbm->escape_string($query)."' and pid=".$dbm->escape_string($pid);
    //print $delsql;
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
}

?></td>
</tr>
<tr><td>

<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <?php

                                    $sum = GetVoteSum($uid);
$googlesum = $sum["google"];
$baidusum = $sum["baidu"];
$total_vote=$googlesum+$baidusum;

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
    $firstlen *= (200/$maxlen);
    $seclen *= (200/$maxlen);
}
?>

<tr>
<td valign="top" style="padding:10px;" >
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<td width="30%" align="center" class="greenFont" style="font-size:16px"><strong>您的选择</strong></td>
<td height="40"><span class="darkFont">选择次数</span>&nbsp;
<strong><span style="font-size:16px; color:#FF3300"><?php print $total_vote;
?></span></strong>&nbsp;
<span class="darkFont"></span></td>
</tr>
<tr bgcolor="F6F6F6">
<td width="30%" height="30" align="center" class="darkFont">
<b><?php print $firstname;
?></b>搜索好</td>
<td height="30"><span class="darkFont"><img src="images/bar3_l.gif" width="3" height="15" align="absmiddle" alt="图柱"><img src="images/bar3.gif" width="<?php print $firstlen; ?>" height="31" align="absmiddle" alt="图柱"><img src="images/bar3_r.gif" width="3" height="15" align="absmiddle" alt="图柱">&nbsp;
<?php print $firstvalue;
?>&nbsp;
</span></td>
</tr>
<tr bgcolor="F6F6F6">
<td width="30%" height="30" align="center" class="darkFont">
<b><?php print $secname;
?></b>搜索好</td>
<td height="30">
<span class="darkFont"><img src="images/bar2_l.gif" width="3" height="15" align="absmiddle" alt="图柱"><img src="images/bar2.gif" width="<?php print $seclen; ?>" height="31" align="absmiddle" alt="图柱"><img src="images/bar2_r.gif" width="3" height="15" align="absmiddle" alt="柱图">&nbsp;
<?php print $secvalue;
?>&nbsp;
</span></td>
</tr>
</table>


</td></tr>
<tr>
<td valign="top" style="padding-left:10px; padding-top:10px; height:50px;" ><div style="text-align:center">
<input type="button"	 name="close" value="返回首页" class="sureButton" onclick="location.href='index.php'; "/>
</div></td></tr>
</table>
<br/><br/>
<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b> </div>
</body>
</html>
