<?php
/*
@file: vote.php
@author:  bytemind@gmail.com wuqifu@gmail.com
@date: 2016/9/15
@see:
*/

//header("Content-Type: text/html; charset=UTF-8");
@require_once('header.php');
require_once("page_template.php");

?>

<br>
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
