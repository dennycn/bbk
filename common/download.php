<?php

// curl
function download($url) {
    echo $url;
    $header[] = "Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*";
    $header[] = "Accept-Language: zh-cn";
    $header[] = "UA-CPU: x86";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT,
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    # if your php.curl does not support gzip, remove the following line.
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    $content = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status != 200) {
        return NULL;
    }
    return $content;
}


//获取所有内容url保存到文件
function  get_index ( $save_file ,  $prefix = "index_" ) {
    $count  =  68 ;
    $i  =  1 ;
    if ( file_exists ( $save_file )) @ unlink ( $save_file );
    $fp  =  fopen ( $save_file ,  "a+" ) or die( "Open " .  $save_file  . " failed" );
    while( $i < $count ) {
        $url  =  $prefix  .  $i  . ".htm" ;
        echo  "Get " .  $url  . "..." ;
        $url_str  =  get_content_url ( get_url ( $url ));
        echo  " OK/n" ;
        fwrite ( $fp ,  $url_str );
        ++ $i ;
    }
    fclose ( $fp );
}
//获取目标多媒体对象
function  get_object ( $url_file ,  $save_file ,  $split = "|--:**:--|" ) {
    if (! file_exists ( $url_file )) die( $url_file  . " not exist" );
    $file_arr  =  file ( $url_file );
    if (! is_array ( $file_arr ) || empty( $file_arr )) die( $url_file  . " not content" );
    $url_arr  =  array_unique ( $file_arr );
    if ( file_exists ( $save_file )) @ unlink ( $save_file );
    $fp  =  fopen ( $save_file ,  "a+" ) or die( "Open save file " .  $save_file  . " failed" );
    foreach( $url_arr  as  $url ) {
        if (empty( $url )) continue;
        echo  "Get " .  $url  . "..." ;
        $html_str  =  get_url ( $url );
        echo  $html_str ;
        echo  $url ;
        exit;
        $obj_str  =  get_content_object ( $html_str );
        echo  " OK/n" ;
        fwrite ( $fp ,  $obj_str );
    }
    fclose ( $fp );
}
//遍历目录获取文件内容
function  get_dir ( $save_file ,  $dir ) {
    $dp  =  opendir ( $dir );
    if ( file_exists ( $save_file )) @ unlink ( $save_file );
    $fp  =  fopen ( $save_file ,  "a+" ) or die( "Open save file " .  $save_file  . " failed" );
    while(( $file  =  readdir ( $dp )) !=  false ) {
        if ( $file != "."  &&  $file != ".." ) {
            echo  "Read file " .  $file  . "..." ;
            $file_content  =  file_get_contents ( $dir  .  $file );
            $obj_str  =  get_content_object ( $file_content );
            echo  " OK/n" ;
            fwrite ( $fp ,  $obj_str );
        }
    }
    fclose ( $fp );
}

//获取指定url内容
function  get_url ( $url ) {
    $reg  =  '/^http:////[^//].+$/' ;
    if (! preg_match ( $reg ,  $url )) die( $url  . " invalid" );
    $fp  =  fopen ( $url ,  "r" ) or die( "Open url: " .  $url  . " failed." );
    while( $fc  =  fread ( $fp ,  8192 )) {
        $content  .=  $fc ;
    }
    fclose ( $fp );
    if (empty( $content )) {
        die( "Get url: " .  $url  . " content failed." );
    }
    return  $content ;
}

//使用socket获取指定网页
function  get_content_by_socket ( $url ,  $host ) {
    $fp  =  fsockopen ( $host ,  80 ) or die( "Open " .  $url  . " failed" );
    $header  =  "GET /" . $url  . " HTTP/1.1/r/n" ;
    $header  .=  "Accept: */*/r/n" ;
    $header  .=  "Accept-Language: zh-cn/r/n" ;
    $header  .=  "Accept-Encoding: gzip, deflate/r/n" ;
    $header  .=  "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Maxthon; InfoPath.1; .NET CLR 2.0.50727)/r/n" ;
    $header  .=  "Host: " .  $host  . "/r/n" ;
    $header  .=  "Connection: Keep-Alive/r/n" ;
    //$header .= "Cookie: cnzz02=2; rtime=1; ltime=1148456424859; cnzz_eid=56601755-/r/n/r/n";
    $header  .=  "Connection: Close/r/n/r/n" ;
    fwrite ( $fp ,  $header );
    while (! feof ( $fp )) {
        $contents  .=  fgets ( $fp ,  8192 );
    }
    fclose ( $fp );
    return  $contents ;
}

//获取指定内容里的url
function  get_content_url ( $host_url ,  $file_contents ) {
    $result  =  "" ;  //array();
//    $rex  =  "/([hH][rR][eE][Ff])/s*=/s*['/"]*([^>'/"/s]+)[/"'>]*/s*/i" ;
    $rex = '';
    $reg  =  '/^(down.*?/.html)$/i' ;
    preg_match_all  ( $rex ,  $file_contents ,  $r );
    foreach( $r  as  $c ) {
        if ( is_array ( $c )) {
            foreach( $c  as  $d ) {
                if ( preg_match ( $reg ,  $d )) {
                    $result  .=  $host_url  .  $d . "/n" ;
                }
            }
        }
    }
    return  $result ;
}

//获取指定内容中的多媒体文件
function  get_content_object ( $str ,  $split = "|--:**:--|" ) {
    //$regx  =  "/href/s*=/s*['/"]*([^>'/"/s]+)[/"'>]*/s*(.*?<//b>)/i" ;   // NOTE: BAD here
    $regx = '';
    preg_match_all ( $regx ,  $str ,  $result );
    if ( count ( $result ) ==  3 ) {
    $result [ 2 ] =  str_replace ( "多媒体： " ,  "" ,  $result [ 2 ]);
        $result [ 2 ] =  str_replace ( " " ,  "" ,  $result [ 2 ]);
        $result  =  $result [ 1 ][ 0 ] .  $split  . $result [ 2 ][ 0 ] .  "/n" ;
    }
    return  $result ;
}

?>
