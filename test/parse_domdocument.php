<?php

// DOMDocument��Ҫ��֤ԭʼHTML�ļ�û���﷨���󣬷����������ȷ��
$url = 'http://t.qq.com';
$html = new DOMDocument();
$html->loadHTMLFile($url);
$title = $html->getElementsByTagName('title');
echo $title->item(0)->nodeValue;

?>
