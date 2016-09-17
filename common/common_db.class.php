<?php
require_once ('config.php');

/*
@class: CommonDB
@called: mysqli
*/
class CommonDB{
	private $conn = NULL;
	private $dbHostName = NULL;
	private $dbName = NULL;
	private $userName=NULL;
	private $userPassword=NULL;
	private $dbType = NULL;
	static public $logger = NULL;
	static public $className = 'CommonDB';

	function __construct($hostName = NULL, $username=NULL, $password=NULL, $dbname=NULL)
	{
		//init db connection
		$this->dbHostName = $hostName;
                $this->dbName = $dbname;
                $this->userName=$username;
		$this->userPassword=$password;
		$this->conn = $this->connect();
	}

	function __call($name, $args)
	{
		//echo "sss";
		//echo count($args).chr(10);
		if(count($args)==1||3)
		{
			$name = $name."Internal";
			//echo $name;
			if(method_exists(self::$className, $name))
			{
				//echo 'exist';
				return call_user_func_array(array(self::$className, $name), $args);
			}
		}
		else
		{
			$name = $name."DB";
			//echo $name;
			if(method_exists(self::$className, $name))
			{
				//echo 'exist';
				return call_user_func_array(array(self::$className, $name), $args);
			}
		}
	}

	static function logAndDie($info)
	{
		self::log($info);
		die();
	}

	static function log($info)
	{
	}

	static function processConnectErrors()
	{
		//echo ('error');
		$info = "MySQL error ".mysqli_connect_errno().": ".mysqli_connect_error();
		self::log($info);

	}

	static function processErrors($errorSource)
	{
		//echo ('error');
		$info = "MySQL error ".$errorSource->errno.": ".$errorSource->error;
		self::log($info);
		//...
	}

	function setDatabaseType($type)
	{
		$this->dbType = $type;
		if (empty($this->dbType)) $this->dbType='mysql';
	}

	function setUserName($name)
	{
		$this->userName = $name;
	}

	function setUserPassword($pass)
	{
		$this->userPassword = $pass;
	}

	function setDatabaseName($db)
	{
		$this->dbName = $db;
	}

	function setDatabaseHost($host)
	{
		$this->dbHostName = $host;
	}

	function setConn($conn)
	{
		$this->conn = $conn;
	}

	function getDatabaseType()
	{
		return $this->dbType;
	}

	function getUserName()
	{
		return $this->userName ;
	}

	function getUserPassword()
	{
		return $this->userPassword;
	}

	function getDatabaseName()
	{
		return $this->dbName;
	}

	function getDatabaseHost()
	{
		return $this->dbHostName;
	}

	function &getConn()
	{
		return $this->conn;
	}

	function resetSettings()
	{
		if(!isset($this->conn) && !empty($this->conn)){
			mysql_close($this->conn);
		}
		self::setUserName('root');
		self::setUserPassword('mYsql!@#');
		self::setDatabaseHost('localhost');
		self::setDatabaseName('bbk');
		$this->conn = self::getConnection();
	}

	function connect()
	{
		return $this->getConnection($this->dbHostName, $this->userName, $this->userPassword, $this->dbName);
	}

	static function getConnection($dbHostName = NULL, $userName =NULL, $userPassword = NULL, $dbName =NULL)
	{
		//get config db info
		if(is_null($dbHostName)&&is_null($userName)&&is_null($userPassword)&&is_null($dbName))
		{
				global $system_config;
				$dbHostName= HOST;
				$userName= USER;
				$userPassword =PASSWD;
				$dbName = DATABASE;
		}
		$conn = new mysqli($dbHostName, $userName, $userPassword, $dbName);
        /* check connection */
        if ($conn->connect_errno) {  // the same to mysqli_connect_errno()
            printf("Connect failed: %s\n", $conn->connect_error);
            self::processConnectErrors();
            return NULL;
        }

		$CHARSET="set names ".CHARSETCODE;
		$result = $conn->query($CHARSET);
        if(!$result)
        {
            printf("query failed: %s\n", $conn->connect_error);
            self::processErrors($conn);
            return NULL;
		}
		return $conn;
	}

	function queryInternal($sql)
	{
		return self::queryDB($sql, $this->conn);
	}

	static function queryDB($sql,&$conn)
	{

		$result = NULL;
		if(is_null($sql))return NULL;

		$result = $conn->query($sql);

		if(!$result)
		{
			self::processErrors($conn);
			return NULL;
		}

		return $result;
	}

	function selectInternal($sql)
	{
		return self::selectDB($sql, $this->conn);
	}

	static function selectDB($sql,&$conn)
	{

		return self::queryDB($sql, $conn);
	}

	function selectRowInternal($sql)
	{
		return self::selectRowDB($sql, $this->conn);
	}

	static function selectRowDB($sql,&$conn)
	{
		$result = NULL;
		if(is_null($sql))return NULL;

		$result = $conn->query($sql);
		if(!$result)
		{
			self::processErrors($conn);
			return NULL;
		}

		$row = $result->fetch_assoc();


		self::freeResult($result);



		return $row;
	}

	function selectRowsInternal($sql)
	{
		return self::selectRowsDB($sql, $this->conn);
	}

	static function selectRowsDB($sql,&$conn)
	{

		$results = NULL;
		if(is_null($sql))return NULL;

		$results = $conn->query($sql);
		if(!$results)
		{
			self::processErrors($conn);
			return NULL;
		}
		$rows = self::getResults($results);

		self::freeResult($results);


		return $rows;
	}

	function selectLimitRowsInternal($sql, $begin=0, $count=20)
	{

		return self::selectLimitRowsDB($sql, $begin, $count, $this->conn);
	}

	static function selectLimitRowsDB($sql, $begin=0, $count=20,&$conn)
	{
		$results = NULL;
		if(is_null($sql))return NULL;
		$limitSql = " limit ".$begin.",".$count." ";
		$sql = $sql.$limitSql;
		$results = $conn->query($sql);
		//echo $sql;
		if(!$results)
		{
			self::processErrors($conn);
			return NULL;
		}
		$rows = self::getResults($results);

		self::freeResult($results);
		return $rows;
	}

	function insertInternal($sql)
	{
		return self::insertDB($sql, $this->conn);
	}

	static function insertDB($sql,&$conn)
	{
		$id = NULL;
		if(is_null($sql))return NULL;

		$result = $conn->query($sql);
		if(!$result)
		{
				self::processErrors($conn);
				return NULL;
		}
		$id = $conn->insert_id;
		return $id;
	}

	function updateInternal($sql)
	{
			return self::updateDB($sql, $this->conn);
	}

	static function updateDB($sql,&$conn)
	{
			CommonDB::log($sql);
			$count = NULL;
			if(is_null($sql))return NULL;
			$result = $conn->query($sql);
			if(!$result)
			{
					self::processErrors($conn);
					return NULL;
			}
			$count = $conn->affected_rows;
			return $count;
	}

	function deleteInternal($sql)
	{
		return self::deleteDB($sql, $this->conn);
	}

	static function deleteDB($sql,&$conn)
	{
		return self::updateDB($sql, $conn);
	}

	static function convertBlob($blob)
	{
		if(is_null($blob))return NULL;
		return addslashes($blob);
	}

	static function convert($value, $type)
	{
		//echo "value=".$value."|";
		if(is_null($value)&&($type!='now')) return 'NULL';
		$defaultFlag =true;
		switch($type)
		{
			case 'boolean':
			case 'int':
			case 'long':
			case 'float':
			case 'double':
						$defaultFlag = false;

						break;
			case 'String':
						$value ="'".$value."'";
						$defaultFlag = false;
						break;
			case 'date':
			case 'time':
					$value ="'".$value."'";
					$defaultFlag = false;
					break;
			case 'now': //insert current time
					$value ="now()";
					$defaultFlag = false;
					break;
		}
		if($defaultFlag)$value ="'".$value."'";

		return $value;
	}

	static function reconvert($field, $type)
	{
		if(is_null($field)) return 'NULL';
		switch($type)
		{
			case 'date':
					$field ="DATE_FORMAT('$field','%Y-%m-%d') as $field";
					break;
			case 'time':
			case 'now' :
					$field =$field;
					break;
		}
		return $field;
	}

	static function getDate($sourceTime)
	{
		if(is_null($sourceTime)) return 'NULL';

		//echo $sourceTime;
		$dateDatas = preg_split("/ /", $sourceTime);
		//print_r($dateDatas);
		if(isset($dateDatas[0]))
				return $dateDatas[0];
		else
				return $sourceTime;
	}

	function createClassName($classFileName)
	{

			$classNameParts = split("_", $classFileName);
			$length = count($classNameParts);
			$className = NULL;
			for($i=0;$i<$length;$i++)
			{
				$classNameParts[$i] = ucfirst($classNameParts[$i]);
				$className.=$classNameParts[$i];
			}
			return $className;
	}


	static function getResult(&$result)
	{
		if(is_null($result))return NULL;
		$row = $result->fetch_assoc();
		return $row;
	}

	static function getResults(&$result)
	{
		if(is_null($result))return NULL;
		$rows = NULL;
		$i = 0;
		while($row = $result->fetch_object())
		{
			$rows[$i]=$row;
			$i++;
		}
		return $rows;
	}

	static function getLimitResults(&$result, $begin=0, $count=20)
	{
		if(is_null($result))return NULL;
		$rows = NULL;
		$i = 0;
		//echo 'offset='.$begin;
		$result->data_seek($begin);
		while($row = $result->fetch_object())
		{
			if($i>=$count)break;
			$rows[$i]=$row;
			$i++;
		}
		return $rows;
	}

	static function freeResult(&$result)
	{
	  if(is_null($result))return false;
		else
		{
			 $result->free();
			 return true;
		}
	}
	function escape_string($string)
	{
          return $this->conn->real_escape_string($string);
	}

	static function close(&$conn)
	{
		if(!is_null($conn))
				$conn->close();
	}

	function closeConn()
	{
		if(!is_null($this->conn) )
				$this->conn->close();
	}

	function begin()
	{
		 $flag = $this->conn->autocommit(false);
		 return $flag;
	}

	function commit()
	{
		 $this->conn->commit();
	}

	function rollback()
	{
		$this->conn->rollback();
	}

	function __destruct()
	{
		$this->closeConn();
		$this->dbHostName = NULL;
                $this->dbName = NULL;
                $this->userName = NULL;
		$this->userPassword = NULL;
	}
}
?>