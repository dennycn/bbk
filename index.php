<?php
//header("Content-Type: text/html; charset=UTF-8");
@require_once('header.php');
require_once("page_template.php");

/*
<form name="form1" method="get" action="bbksearch.php" onsubmit="return CheckForm();">
<table id="search_box" width="98%">
<tr><td class="td_search_left" >
<img src="images/search_icon.gif" class="img_search" />&nbsp;
<input id='query' type="text" name="query" class="searchbox"></td>
<td class="td_search_right"><input type="submit" value="比比看" class="sureButton"/></td>
</tr></table></form>
<tr><td><?php show_search_form() ?> </td></tr>
*/
?>

<table class="tbl">
<tr><td class='bluetitleFont'>搜索比比看<br></td></tr>
<tr><td class="td_small">输入任意搜索关键字,然后对比两边结果,选择你最喜欢的结果投票</td></tr>

<?php
// <tr><td>
echo '<tr><td>'.show_search_form().'</td></tr>';

// show choose stat
show_choose_stat(NULL);
show_choose_stat(GetUid());

print('</table>');

@require_once('foot.php');

?>
