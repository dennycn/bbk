<?php
/*
@author:  bytemind@gmail.com 
@modifier: wuqifu@gmail.com 
@date: 2011-07-24
@see: http://code.google.com/intl/zh-cn/apis/customsearch/v1/using_rest.html
*/
require_once('util.php');
//define("GOOGLEURL_PREFIX", "http://www.google.com.hk/search?complete=1&hl=zh-CN&q=");
//define("GOOGLEURL_POSTFIX", "&btnG=Google+%E6%90%9C%E7%B4%A2&meta=");
define("GOOGLEURL_PREFIX", "http://www.google.com.hk/#hl=zh-CN&source=hp&q=");
define("GOOGLEURL_POSTFIX", "");


function search_google($query) {
//    return NULL;
  if (strcasecmp(CHARSET, "UTF-8") != 0 &&
      strcasecmp(CHARSET, "UTF8") != 0) {
    $need_iconv = TRUE;
  }
  else {
    $need_iconv = FALSE;
  }

  if ($need_iconv) {
    $query = iconv(CHARSET, "UTF-8//IGNORE", $query);
  }

  $url = GOOGLEURL_PREFIX;
  $url .= urlencode($query);
  $url .= GOOGLEURL_POSTFIX;
  $page = cached_download($url);
  if ($page == NULL) {
    print "google error";
    return NULL;
  }
  // convert encoding if needed
  if ($need_iconv) {
    $page = iconv('UTF-8', CHARSET . "//IGNORE", $page);
  }
    print $page;
  return parse_google($page);
}


function parse_google($page) {
  $results = array();
  // fetch news onebox
  preg_match("/<a href=\"http:\/\/news.google.cn\/news\?q=.*?\">.*?<\/a>" .
             ".*?<a class=fl href=\".*?\">.*?<\/a>" .
             "<\/font><tr><td valign=top width=.*?><img src=.*?>" .
             "<td valign=top><font size=-1>" .
             "(.*?)<\/font><\/td><\/tr><\/table><p><p>/si", $page, $out);
  $newshtml = $out[1];
  preg_match_all("/<a .*? href=\"\/url\?q=(.*?)&sa=X&oi=news.*?>" .
                 "(.*?)<\/a> - <font color=green>(.*?)<\/font> - (.*?)<br>/si",
                 $newshtml, $out, PREG_SET_ORDER);
  $news = array();
  foreach ($out as $outitem) {
    $title = $outitem[2];
    $title = str_replace("<font color=CC0033>", "<font color=#CC0033>", $title);
    $news[] = array("url" => $outitem[1], "title" => $title,
                    "source" => $outitem[3], "time" => $outitem[4]);
  }

  // fetch finance onebox
  preg_match("/<div><h2 class=r><a href=\"\/url\?q=(http:\/\/finance" .
             "\.sina\.com\.cn\/realstock[^&\"]*).*?\".*?><b>(.*?)<\/b><\/a>.*?" .
             "<font[^>]*>(.*?)<\/font><\/h2><br><\/div><div style=\".*?\">(.*?)<\/div>" .
             "<div style=\".*?\">(.*?)<\/div>/si", $page, $out);
  $finance = $out[0];
  // stock image
  $finance = str_replace("/pfetch/dchart", "http://www.google.cn/pfetch/dchart", $finance);
  $finance = str_replace("/url?", "http://www.google.cn/url?", $finance);
  $finance = str_replace("cellpadding=\"2\"", "cellpadding=0", $finance);
  $finance = str_replace("cellpadding=2", "cellpadding=0", $finance);
  $finance = str_replace("style=\"padding-right:15px\"><img", "style=\"padding-right:1px\"><img", $finance);
  $finance = str_replace("<td width=20>", "<td width=1px>", $finance);
  $finance = str_replace("padding:3px 0", "padding:0px 0", $finance);
  $finance = str_replace("margin-top:0.3em", "margin-top:0px", $finance);
  $finance = str_replace("</a><table style=\"display:inline;padding-bottom:0\"", 
  "</a><br><table style=\"display:inline;padding-bottom:0\"", $finance);

  $results["finance"] = $finance;

  // split every result
  preg_match_all("/<div class=g[^>]*>(.*?)<\/div>/si", $page, $out,
                 PREG_PATTERN_ORDER);
  $passes = $out[1];
  // get title, url and snippet
  $results["news"] = $news;
  foreach ($passes as $pass) {
    preg_match("/<a href=\"([^\"]*)\"[^>]*>(.*?)<\/a>.*?" . //title and url
               "<td class=\"?j[^\">]*\"?><font size=-1>(<a href=.*?>.*?<\/a><br>)?(.*?)(<br>)?" . // snippet
               "<span class=a>([^\s]*)/si",  // display url
               $pass, $out);
    $url = $out[1];
    $title = $out[2];
    $snippet = $out[4];
    $dispurl = $out[6];
    // handle the situation "this site may harm your computer"
    $pos = strpos($url, "/interstitial?url=");
    if ($pos !== false && $pos == 0) {
      $url = substr($url, 18);
      $url = urldecode($url);
    }

    // normallize snippet and title
    // strip <b>
    $title = str_replace("<b>...</b>", "...", $title);
    $snippet = str_replace("<b>...</b>", "...", $snippet);
    $dispurl = str_replace("<b>", "", $dispurl);
    $snippet = str_replace("<font color=CC0033>", "<font color=#CC0033>", $snippet);
    $snippet = str_replace("<font color=cc0033>", "<font color=#CC0033>", $snippet);
    $title = str_replace("<font color=CC0033>", "<font color=#CC0033>", $title);
    $title = str_replace("<font color=cc0033>", "<font color=#CC0033>", $title);

    $results[] = array("url" => $url, "title" => $title, "snippet" => $snippet,
                       "dispurl" => $dispurl);
  }
  return $results;
}

?>
