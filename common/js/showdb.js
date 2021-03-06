//<script type="text/javascript">
$(function(){  // 相当于在页面中的body标签加上onload事件 
	//奇偶行不同颜色
	$("table tr:nth-child(even)").addClass("even");  //1~OK, addClass
	//$("table tr:nth-child(odd)").css("background-color","#eeeeee");   //2~OK, css
	//$("#soldList tr:odd").addClass("odd");   //3~类似4，奇数行设色. 若奇偶都设置，则移动变色功能完全失效。
	//$("#soldList tr:even").addClass("even");  //4~偶数行设色。设置后，移动变色在此行会失效，相当于1~OK

	//鼠标移动到行变色,单独建立css类hover
	$("#soldList tr:gt(0)").hover(  //hover相当于mouseover和mouseout两个事件
		//tr:gt(0):表示获取大于 tr index 为0 的所有tr，即不包括表头
		function () { $(this).addClass("hover") },
		function () { $(this).removeClass("hover") }
	);
	
}) 

function saveToDB(obj, table, price, pid)
{
	//alert('saveToDB'+price+pid);
	xmlhttp=GetXmlHttpObject()
	if (xmlhttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	} 

	xmlhttp.onreadystatechange=function()
	{
		//if (xmlhttp.readyState==4 && xmlhttp.status==200)
		if (xmlhttp.readyState==4 || xmlHttp.readyState=="complete")
		{
			obj.html(xmlhttp.responseText);
		}
	}
	xmlhttp.open("GET","sql_update.php?table="+table+"&price="+price+"&pid="+pid, true);
	xmlhttp.send();
}

function deleteToDB(obj, table, pid)
{
	//alert('deleteToDB'+table+pid);
	xmlhttp=GetXmlHttpObject()
	if (xmlhttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	} 

	xmlhttp.onreadystatechange=function()
	{
		//if (xmlhttp.readyState==4 && xmlhttp.status==200)
		if (xmlhttp.readyState==4 || xmlHttp.readyState=="complete")
		{
			result = xmlhttp.responseText;
			obj.html(result);
			if (result == "ok")
			{
				obj.parent().remove();
			}
		}
	}
	xmlhttp.open("GET","sql_delete.php?table="+table+"&pid="+pid, true);
	xmlhttp.send();
}

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// code for IE7+, Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{   // code for IE6, IE5
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

//</script>
