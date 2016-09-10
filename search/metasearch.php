<?php

require_once('util.php');

define("GOOGLEURL_PREFIX", "http://www.google.com.hk/#hl=zh-CN&source=hp&q=");
define("GOOGLEURL_POSTFIX", "");
define("BAIDUURL_PREFIX", "http://www.baidu.com/s?wd=");
define("BAIDUURL_POSTFIX", "&cl=3");


class SearchEngine
{
    public $query = '';
    public $url = '';
    function __construct($query)
    {
        $this->$query = iconv(CHARSET, "UTF-8//IGNORE", $query);
        $this->$url = build_url();
    }

    function build_url()
    {
    }
    function download_page($url)
    {
        return cached_download($url);
    }

    function parse_page($page)
    {
    }
}

class BaiduSearchEngine : SearchEngine
{
    function build_url(){
        $url = '';
        $url = BAIDUURL_PREFIX;
        $url .= urlencode($this->$query);
        $url .= BAIDUURL_POSTFIX;
        return $url;
    }
    function download_page($url)
    {
    }
    function parse_page($page)
    {
    }
}

class GoogleSearchEngine : SearchEngine
{
    function build_url(){
        $url = '';
        $url = GOOGLEURL_PREFIX;
        $url .= urlencode($this->$query);
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

class SogouSearchEngine : SearchEngine
{
}

?>
