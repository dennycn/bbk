<?php
//header("Content-Type: text/html; charset=UTF-8");
require_once('header.php');
require_once("bbk_sql.php");

//
$uid = GetUid();

?>

<body>
<div class="RoundedCorner">
<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr align="center">
<td valign="middle" style="margin-left:10px;margin-top:10px;height:65px; filter: progid:DXImageTransform.Microsoft.Gradient(gradientType=0,startColorStr=#ECECEB,endColorStr=#FFFFFF);
">&nbsp;
<span class="bluetitleFont">&nbsp;
<br>
搜索比比看</span></td>
</tr>
<tr align="center"><td>
<font size=-1>输入任意搜索关键字,然后对比两边结果,选择你最喜欢的结果投票.</font>
</td></tr>

<tr>
<td height="35"><form name="form1" method="get" action="bbksearch.php" onsubmit="return CheckForm();">
<table id="search_box" width="98%" border="0" align="center">
<tr>
<td width="58%" height="35" align="right" valign="middle" style="padding-left:5px;">
<img src="images/search_icon.gif" width="24" height="25" align="absmiddle" />&nbsp;
<input type="text" name="query" class="searchbox"></td>
<td width="42%" align="left" valign="middle" style="padding-left:5px;">
<input type="submit" value="比比看" class="sureButton"/>
</td>
</tr>
</table>
</form></td>
</tr>

<?php
$sum = GetVoteSum(NULL);
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
<td width="30%" align="center" class="greenFont" style="font-size:16px"><strong>排行榜</strong></td>
<td height="40"><span class="darkFont">选择次数</span>&nbsp;
<strong><span style="font-size:16px; color:#FF3300"><?php print $total_vote;
?></span></strong>&nbsp;
<span class="darkFont"></span></td>
</tr>
<tr bgcolor="F6F6F6">
<td width="30%" height="30" align="center" class="darkFont">
<b><?php print $firstname;
?></b>搜索好</td>
<td height="30"><span class="darkFont"><img src="images/bar3_l.gif" width="3" height="15" align="absmiddle"><img src="images/bar3.gif" width="<?php print $firstlen; ?>" height="31" align="absmiddle"><img src="images/bar3_r.gif" width="3" height="15" align="absmiddle">&nbsp;
<?php print $firstvalue;
?>&nbsp;
</span></td>
</tr>
<tr bgcolor="F6F6F6">
<td width="30%" height="30" align="center" class="darkFont">
<b><?php print $secname;
?></b>搜索好</td>
<td height="30">
<span class="darkFont"><img src="images/bar2_l.gif" width="3" height="15" align="absmiddle"><img src="images/bar2.gif" width="<?php print $seclen; ?>" height="31" align="absmiddle"><img src="images/bar2_r.gif" width="3" height="15" align="absmiddle">&nbsp;
<?php print $secvalue;
?>&nbsp;
</span></td>
</tr>
</table></td>
</tr>

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

<?php if ($total_vote): ?>
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
<td height="30"><span class="darkFont"><img src="images/bar3_l.gif" width="3" height="15" align="absmiddle"><img src="images/bar3.gif" width="<?php print $firstlen; ?>" height="31" align="absmiddle"><img src="images/bar3_r.gif" width="3" height="15" align="absmiddle">&nbsp;
<?php print $firstvalue;
?>&nbsp;
</span></td>
</tr>
<tr bgcolor="F6F6F6">
<td width="30%" height="30" align="center" class="darkFont">
<b><?php print $secname;
?></b>搜索好</td>
<td height="30">
<span class="darkFont"><img src="images/bar2_l.gif" width="3" height="15" align="absmiddle"><img src="images/bar2.gif" width="<?php print $seclen; ?>" height="31" align="absmiddle"><img src="images/bar2_r.gif" width="3" height="15" align="absmiddle" alt="柱图">&nbsp;
<?php print $secvalue;
?>&nbsp;
</span></td>
</tr>
</table></td>
</tr>
<?php endif ?>

<?php
@require_once('foot.php');
?>
