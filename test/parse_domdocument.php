<?php

// DOMDocument：要保证原始HTML文件没有语法错误，否则解析不正确。
$url = 'http://t.qq.com';
$html = new DOMDocument();
$html->loadHTMLFile($url);
$title = $html->getElementsByTagName('title');
echo $title->item(0)->nodeValue;

?>
