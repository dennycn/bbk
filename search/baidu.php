<?php
/*
@author:  bytemind@gmail.com
@modifier: wuqifu@gmail.com
@date: 2011-07-24
@see:
@query: http://www.baidu.com/s?wd=
*/

require_once('util.php');

define("BAIDUURL_PREFIX", "http://www.baidu.com/s?wd=");
define("BAIDUURL_POSTFIX", "&cl=3");

function search_baidu($query) {
    return NULL;
    if (strcasecmp(CHARSET, "GB2312") != 0 &&
            strcasecmp(CHARSET, "GBK") != 0) {
        $need_iconv = TRUE;
    }
    else {
        $need_iconv = FALSE;
    }

    if ($need_iconv) {
        $query = iconv(CHARSET, "GBK//IGNORE", $query);
    }

    $url = BAIDUURL_PREFIX;
    $url .= urlencode($query);
    $url .= BAIDUURL_POSTFIX;
    ///print $url."\n";
    $page = cached_download($url);
    if ($page == NULL) {
        return NULL;
    }
    // convert encoding if needed
    if ($need_iconv) {
        $page = iconv('GBK', CHARSET . "//IGNORE", $page);
    }
    //print $page;
    return parse_baidu($page);
}


//TODO: error here
function parse_baidu($page) {
    $results = array();
    /* // fetch finance--news
      // fetch finance onebox
      //preg_match("/<ol><p style=\".*?\">".".*?<\/p><\/ol>/si", $page, $out);
      $finance = $out[0];
      ///print $page;
      // stock image
      $finance = str_replace("<ol>", "<ol style=\"padding-left:16px;\"", $finance);
      $finance = str_replace("/baidu?", "http://www.baidu.com/baidu?", $finance);
      $results["finance"] = $finance;

      // fetch news one box
      preg_match("/<a href=\"http:\/\/news.baidu.com\/ns\?" .
                 ".*?>.*?<\/a>.*?<a href=\"http:\/\/news.baidu.com\/" .
                 "view.html\?from=web.*?>.*?<\/a>.*?<\/font><\/td><\/tr><tr><td><font>".
                 "(.*?<\/font><br>)<\/font><\/td><\/tr><\/table>/si",
                 $page, $out);
      $newshtml = $out[1];

      preg_match_all("/<font size=-1>&nbsp;&nbsp;<a href=\"http:\/\/news.baidu.com\/ns\[0\]_(.*?)&web=5&query=.*?>" .
                     "(.*?)<\/a>[ ]*<font color=#666666>(.*?) (.*?)<\/font><\/font><br>/si",
                     $newshtml, $out, PREG_SET_ORDER);
      $news = array();
      foreach ($out as $outitem) {
        $title = $outitem[2];
        $title = str_replace("<font color=#C60A00>", "<font color=#CC0033>", $title);
        $news[] = array("url" => $outitem[1], "title" => $title,
                        "source" => $outitem[3], "time" => $outitem[4]);
      }
    */

    // split every result
    preg_match_all("/<tr><td class=f>.*?<\/font><\/td><\/tr><\/table><br>/si",
                   $page, $out, PREG_PATTERN_ORDER);
    $passes = $out[0];

    // get title, url and snippet
    $results["news"] = $news;
    foreach ($passes as $pass) {
        ///print $pass;
        //TODO: error here, add by denny
        /*
            preg_match("/<a.*?href=\"(.*?)\".*?><font size=\"3\">" . // url
                       "(.*?)<\/font><\/a><br>" . //title
                       "<font size=-1>(.*?)<br>" . // snippet
                       "<font [^>]*color=#008000>([^\s]*)/si", // dispurl
                       $pass, $out);
        */
        preg_match("/<a.*?href=\"(.*?)\"" . // url
                   "(.*?)<\/a><\/h3>" . //title
                   "<font size=-1>(.*?)<br>" . // snippet
                   "([^\s]*)/si", // dispurl
                   $pass, $out);

        $url = $out[1];
        $title = $out[2];
        $snippet = $out[3];
        //$dispurl = $out[4];
        print $url;
        // handle the situation "tui guang"
        $pos = strpos($url, "http://www.baidu.com/baidu.php?url=");
        if ($pos !== false && $pos == 0) {
            $url = "http://" . $dispurl;
        }

        // normallize snippet and title color
        $snippet = str_replace("<font color=#C60A00>", "<font color=#CC0033>", $snippet);
        $title = str_replace("<font color=#C60A00>", "<font color=#CC0033>", $title);
        print $title;
        $results[] = array("url" => $url, "title" => $title, "snippet" => $snippet,
                           "dispurl" => $dispurl);
    }

    return $results;
}

?>
