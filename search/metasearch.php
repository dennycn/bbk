<?php

require_once('../util.php');

// 只获取网页结果：新闻/资讯
define("GOOGLEURL_PREFIX", "http://www.google.com.hk/#hl=zh-CN&source=hp&q=");
define("GOOGLEURL_POSTFIX", "");
define("BAIDUURL_PREFIX", "http://www.baidu.com/s?wd=");
define("BAIDUURL_POSTFIX", "&cl=2");   // cl: 2/3~news

/**
@beief:
@author: denny testdenny@163.com
@date: 2016/9/11
@tools:
    download_page: file/file_get_content/curl/fsockopen/fopen/snoopy
    parse_page: regex/simple_html_dom/DOMDocument
@note:
**/
class SearchEngine
{
    protected $query = '';
    protected $url = '';
    protected $res = array();
    function __construct()
    {
    }

    function search($query) {
        $this->query = iconv(CHARSET, "UTF-8//IGNORE", $query);
        $this->url = $this->build_url();
        $page = download($this->url);
        return $this->parse_page($page);
    }

    function build_url()
    {
    }

    // php解析页面暂只发现regex，jquery无法调用
    function parse_page($page)
    {
    }
}

class BaiduSearchEngine extends SearchEngine
{
    function build_url()
    {
        $url = BAIDUURL_PREFIX;
        $url .= urlencode($this->query);
        $url .= BAIDUURL_POSTFIX;
        return $url;
    }
    function parse_page($page)
    {
        echo "\n48,pagelen=", strlen($page);
        return $this->res;
    }
}

class GoogleSearchEngine extends SearchEngine
{
    function build_url() {
        $url = GOOGLEURL_PREFIX;
        $url .= urlencode($this->query);
        $url .= GOOGLEURL_POSTFIX;
        return $url;
    }
    function download_page($url)
    {
    }
    function parse_page($page)
    {
    }
}

class SogouSearchEngine extends SearchEngine
{
}


$baidu_se = new BaiduSearchEngine();
$res = $baidu_se->search('mp3');
print_r($res);

?>


