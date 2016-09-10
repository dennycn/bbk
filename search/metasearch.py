<?php

require_once('../util.php');

// 只获取网页结果：新闻/资讯
define("GOOGLEURL_PREFIX", "http://www.google.com.hk/#hl=zh-CN&source=hp&q=");
define("GOOGLEURL_POSTFIX", "");
define("BAIDUURL_PREFIX", "http://www.baidu.com/s?wd=");
define("BAIDUURL_POSTFIX", "&cl=2");   // cl: 2/3~news


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


