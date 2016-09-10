<?php

function test1()
{
    // 新建一个Dom实例
    $html = new simple_html_dom();

    // 从url中加载
    $html->load_file('http://www.jb51.net');

    // 从字符串中加载
    $html->load('<html><body>从字符串中加载html文档演示</body></html>');

    //从文件中加载
    $html->load_file('path/file/test.html');
}

function test2()
{
    $url='http://t.qq.com';
    include_once('../simplehtmldom/simple_html_dom.php');
    $html=file_get_html($url);
    $title=$html->find('title');
    echo $title[0]->plaintext;
}

test1();
test2();

?>
