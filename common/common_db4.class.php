<?php
require_once ('Log.php');
require_once ('config.php');

/*
@class: CommonDB
called: mysql_connect
*/
class CommonDB{
	var $username;
	var $password;
	var $database;
	var $hostname;
	var $link;
	var $result;

	function CommonDB()
	{
  		if(is_null($this->hostname)&&is_null($this->username)&&is_null($password)&&is_null($database))
		{
			$this->hostname= HOST;
			$this->username= USER;
			$this->password =PASSWD;
			$this->database = DATABASE;
		}
 	    $this->connectdb();
     	$this->selectdb();
   }



	function connectdb(){ //这个函数用于连接数据库

	$this->link=mysql_connect($this->hostname,$this->username,$this->password) or die("Sorry,can not connect to database");
	return $this->link;
	}

	function selectdb(){ //这个函数用于选择数据库
	mysql_select_db($this->database,$this->link);
	}


	function escape_string($string)
	{
	//	print $string;
        return mysql_real_escape_string($string,$this->link);
	}

	function query($sql){ //这个函数用于送出查询语句并返回结果，常用。
		if($this->result=mysql_query($sql,$this->link)) return $this->result;
		else {
		echo "SQL_ERROR: ".mysql_error();
		return false;
		}
	}

	function selectRowsDB($sql)
	{

		$results = NULL;

		if(is_null($sql))return NULL;

		$results = $this->query($sql);
		if(!$results)
		{
			return NULL;
		}
		$rows =$this->getResults($results);
		return $rows;
	}


	function selectRows($sql)
	{
		return  $this->selectRowsDB($sql);
	}


	function getResults(&$result)
	{
		if(is_null($result))return NULL;
		$rows = NULL;
		$i = 0;
		while($row = mysql_fetch_object($result))
		{
			$rows[$i]=$row;
			$i++;
		}
		return $rows;
	}

	function freeResult(&$result)
	{
	  if(is_null($result))return false;
		else
		{
			 $result->free();
			 return true;
		}
	}
	function deleteInternal($sql)
	{
		return $this->deleteDB($sql,$this->link);
	}

	function deleteDB($sql,&$conn)
	{
		return $this->updateDB($sql,$this->link);
	}

	 function updateDB($sql)
	{
			$count = NULL;
			if(is_null($sql))return NULL;
			$result = $this->query($sql);
			if(!$result)
			{
				return NULL;
			}
			$count = $this->link->affected_rows;
			return $count;


	}

	function insertInternal($sql)
	{
		return $this->insertDB($sql,$this->link);
	}

	function insertDB($sql)
	{
		$id = NULL;
		if(is_null($sql))return NULL;

		$result = $this->query($sql,$this->link);
		if(!$result)
		{
			return NULL;
		}
		$id = mysql_insert_id($this->link);
		return $id;
	}

	function begin()
	{
		return 1;
	}


	function commit()
	{

	}

	function rollback()
	{

	}

}
?>
