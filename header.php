<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="common/css/style1.css" rel="stylesheet" type="text/css">
<title>搜索比比看</title>
<script language="JavaScript" type="text/javascript">
function CheckForm() {
    var query = document.getElementById("query").value;
    if(query == "" || query == null ) {
        alert("请输入搜索关键字！");
        return false;
    }
}
</script>
</head>
<body>
<div class="RoundedCorner">
