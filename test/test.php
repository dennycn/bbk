<?php

$pass='<h3 class="t"><a target="_blank" href="http://www.27270.net/" onmousedown="return >美女</em>图片_<em>美女</em>写真_性感<em>美女</em>_mm - 27270<em>美女</em>图片网</a>
</h3>';
//echo $pass;

    preg_match("/<a.*?href=\"(.*?)\"" . // url
               "(.*?)<\/a><\/h3>" . //title
               "<font size=-1>(.*?)<br>" . // snippet
               "<font [^>]*color=#008000>([^\s]*)/si", // dispurl
               $pass, $out);

//    preg_match("<a .*?href=\"(.*?)\" .*?>", $pass, $out);

    $url = $out[1];
    $title = $out[2];
    $snippet = $out[3];
    $dispurl = $out[4];
    print $out[1];
?>
