<?php

function randStr($len=24) {
    $chars='ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789#%@'; // characters to build the password FROM
    mt_srand((double)microtime()*1000000 + getmypid()); // seed the random number generater (must be done)
    $num='';
    while(strlen($num)<$len)
        $num.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $num;
}

function randOrder($len=1) {
    $chars='12';    // characters to build the password FROM
    mt_srand((double)microtime()*1000 + getmypid());    // seed the random number generater (must be done)

    $num='';
    while(strlen($num)<$len)
        $num.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $num;
}

?>
