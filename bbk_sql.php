<?php
/*
@file: util.php
@author: wuqifu@gmail.com
@date: 2011-07-24
@see:
*/

require_once('common/common_db.class.php');
require_once('common/download.php');


function cached_download($url)
{   // table: pageCache~ delete/select/update
    $dbm = new CommonDB();  // delete old cache
    $sql="delete FROM pageCache where expire<date_sub(now(),INTERVAL 2 DAY);";
    $dbm->deleteInternal($sql);

    // see if a fresh page cache is in mysql
    $cachesql="SELECT * FROM pageCache where url='".$dbm->escape_string($url)."' and expire>=now()";
    ///print $cachesql;
    $result=$dbm->SELECTRows($cachesql);
    $sum=count($result);
    if($sum>0)
    {
        $page=$result[0]->page;
        return $page;
    }
    // really download
    if (is_server_busy()) {
        return NULL;
    }
    $newpage=download($url);
    if ($newpage == NULL) {
        set_server_busy();
        return NULL;
    }

    // record in cache
    if($newpage!=NULL && $newpage!="" && strlen($url)<250)
    {
        $sql="replace into pageCache(url,expire,page) values ('".
             $dbm->escape_string($url)
             ."',date_add(now(),INTERVAL 2 HOUR),'".$dbm->escape_string($newpage)."')";
        //  print $sql;
        $dbm->insertInternal($sql);
    }

    // In case of server error, old cache is rather ok.
    if($newpage==NULL || $newpage=="")
    {
        $cachesql="SELECT * FROM pageCache where url='".$dbm->escape_string($url)."'";
        $result=$dbm->SELECTRows($cachesql);
        $sum=count($result);
        if($sum>0)
        {
            $page=$result[0]->page;
            return $page;
        }
    }
    return $newpage;
}

function is_server_busy() {
    $dbm = new CommonDB();
    $busysql="SELECT * FROM pageCache where url='___SERVER_BUSY___' and expire>=now()";
    $result=$dbm->SELECTRows($busysql);
    $sum=count($result);
    if($sum>0)
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

function set_server_busy() {
    $server_busy_timeout_mins = "2";
    $dbm = new CommonDB();
    $sql="replace into pageCache(url,expire) values ('___SERVER_BUSY___', " .
         "date_add(now(),INTERVAL " . $server_busy_timeout_mins . " MINUTE))";
    $dbm->insertInternal($sql);
}

function pending($uid,$showOrder,$query) {
    deleteOldPendings();
    $dbm = new CommonDB();
    //print $query;
    $insertsql="insert into pending (uid,query,showOrder,deadline) values (".$dbm->escape_string($uid).",'".$dbm->escape_string($query)."','".$dbm->escape_string($showOrder)."',date_add(now(),INTERVAL 1 HOUR))";
    // print $insertsql;
    $pid = $dbm->insertInternal($insertsql);
    return $pid;
}

function deleteOldPendings()
{
    $dbm = new CommonDB();
    //print $query;
    $flag=$dbm->begin();
    // delete old pendings
    $deletesql="delete FROM pending where deadline<now()";

//  print $deletesql;

    $dbm->deleteInternal($deletesql);
    $dbm->commit();
}

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

function GetUid() {
    if(isset($_COOKIE["search_cookie"]))
    {
        $search_cookie=$_COOKIE["search_cookie"];
    }
    else
    {
        $search_cookie="";
    }
    $uid = UserCookie($search_cookie);
    return $uid;
}

// get cookie FROM table: users
function UserCookie($search_cookie)
{
    //print $search_cookie;
    $dbm = new CommonDB();
    $usersql="SELECT * FROM users where cookie='".$dbm->escape_string($search_cookie)."'";
    //	 print $usersql;
    $result=$dbm->SELECTRows($usersql);
    $sum=count($result);
    if($sum>0)
    {
        $uid=$result[0]->uid;
        $cookie=$result[0]->cookie;
        $_COOKIE["search_cookie"]=$cookie;
        return $uid;
        //	print "database:".$_COOKIE["uid"];
    }
    else
    {
        $dbm = new CommonDB();
        $lifeTime = 30 * 24 * 3600; // 1 month

        $num=randStr(24);
        if($num!="")
        {
            $insertsql="insert into users(cookie,lasttime) values ('".$num."', now())";
            $uid=$dbm->insertInternal($insertsql);
            SetCookie("search_cookie",$num,time()+$lifeTime);
            return $uid;
        }
    }
    if($uid=="")
    {
        return "";
    }
}

function GetVote($pid, $choosenum, $uid)
{   // get data FROM table: pending
    deleteOldPendings();
    $dbm = new CommonDB();
    $pendingsql = "SELECT showOrder,query FROM pending where pid=" .
                  $dbm->escape_string($pid) .
                  " and uid=" . $dbm->escape_string($uid) .
                  " limit 1";

// print $pendingsql;

    $result=$dbm->SELECTRows($pendingsql);
    $sum=count($result);
    if($sum==0)
    {
        return NULL;
    }
    $showOrder=$result[0]->showOrder;
    $query = $result[0]->query;
    if(($choosenum==1 and $showOrder=="googlebaidu") or
            ($choosenum==2 and $showOrder=="baidugoogle"))
    {
        $choose = "google";
    }
    else if(($choosenum==2 and $showOrder=="googlebaidu") or
            ($choosenum==1 and $showOrder=="baidugoogle"))
    {
        $choose = "baidu";
    }
    else
    {
        $choose = "";
    }
    return array("choose" => $choose, "query" => $query);
}

function GetVoteSum($uid)
{
    $dbm = new CommonDB();
    if ($uid!=NULL)
    {
        $statsql="SELECT choose,count(*) as c FROM vote where uid=" .
                 $dbm->escape_string($uid) . " group by choose";
    }
    else
    {
        $statsql="SELECT choose,count(*) as c FROM vote WHERE type=0 group by choose";
    }

    $results=$dbm->SELECTRows($statsql);
    $googlesum = 0;
    $baidusum = 0;
    for($i=0; $i<count($results); $i++)
    {
        if($results[$i]->choose=="google")
        {
            $googlesum=$results[$i]->c;
        }
        else if($results[$i]->choose=="baidu")
        {
            $baidusum=$results[$i]->c;
        }
    }
    $total = $googlesum + $baidusum;
    return array("google" => $googlesum, "baidu" => $baidusum, "total" => $total);
}

?>
