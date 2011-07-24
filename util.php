<?php
// bytemind@gmail.com


////////////////////////////////////////////////////////////
//downloading a url
require_once('database/common_db.class.php');
function cached_download($url)
{
	// delete old cache.
	 $dbm = new CommonDB();
         $sql="delete from pageCache where expire<date_sub(now(),INTERVAL 2 DAY);";
         $dbm->deleteInternal($sql);

	// see if a fresh page cache is in mysql
	 $cachesql="select * from pageCache where url='".$dbm->escape_string($url)."' and expire>=now()";
	///print $cachesql; 
    $result=$dbm->selectRows($cachesql);
	 $sum=count($result);
	 if($sum>0)
	 {
		$page=$result[0]->page;	
		return $page;
	 }
    ///print 'sum='.$sum;
	$newpage=download($url);
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
		 $cachesql="select * from pageCache where url='".$dbm->escape_string($url)."'";
		 $result=$dbm->selectRows($cachesql);
		 $sum=count($result);
		 if($sum>0)
		 {
			$page=$result[0]->page;	
			return $page;
		 }
	}
	return $newpage;
}

function download($url) {
  if (is_server_busy()) {
    return NULL;
  }
    print $url;
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
  if (substr($status, 0, 1)=="5") {
    set_server_busy();
  }
  if ($status != 200) {
    return NULL;
  }
  else {
    return $content;
  }
}

function is_server_busy() {
  $dbm = new CommonDB();
  $busysql="select * from pageCache where url='___SERVER_BUSY___' and expire>=now()";
  $result=$dbm->selectRows($busysql);
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

?>
<?php

function pending($uid,$showOrder,$query){
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
  $deletesql="delete from pending where deadline<now()";

//  print $deletesql;

  $dbm->deleteInternal($deletesql);
  $dbm->commit();
}


function randStr($len=24) {
$chars='ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789#%@'; // characters to build the password from
mt_srand((double)microtime()*1000000 + getmypid()); // seed the random number generater (must be done)
$num='';   
while(strlen($num)<$len)   
$num.=substr($chars,(mt_rand()%strlen($chars)),1);   
return $num;   
} 


function randOrder($len=1) {   
$chars='12'; // characters to build the password from   
mt_srand((double)microtime()*1000 + getmypid()); // seed the random number generater (must be done)   
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
  $uid=UserCookie($search_cookie);
  return $uid;
}

function UserCookie($search_cookie)
{
//print $search_cookie;
  $dbm = new CommonDB();
  $usersql="select * from users where cookie='".$dbm->escape_string($search_cookie)."'";
  //	 print $usersql;
  $result=$dbm->selectRows($usersql);
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
{
  deleteOldPendings();
  $dbm = new CommonDB();
  $pendingsql = "select showOrder,query from pending where pid=" .
                $dbm->escape_string($pid) .
                " and uid=" . $dbm->escape_string($uid) .
                " limit 1";

 // print $pendingsql;

  $result=$dbm->selectRows($pendingsql);
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
    $statsql="select choose,count(*) as c from vote where uid=" .
        $dbm->escape_string($uid) . " group by choose";
  }
  else
  {
    $statsql="select choose,count(*) as c from vote where type=0 group by choose";
  }

  $results=$dbm->selectRows($statsql);
  $googlesum = 0;
  $baidusum = 0;
  for($i=0; $i<count($results);$i++)
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
