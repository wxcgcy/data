<?php
define("DB_HOST",'localhost');//数据库配置
define("DB_USERNAME",'root');
define("DB_PASSWORD",'');
define("DB_NAME",'data');

class Mysqls{
	private $host = DB_HOST;//数据库地址
	private $username = DB_USERNAME;//用户名
	private $password = DB_PASSWORD;//密码
	private $dbname = DB_NAME;//数据库名
	private $dblink;//数据库连接
		
	function __construct($is_slave=false,$p=array()){
		if(isset($p['host'])){//程序中配置数据库连接
			$this->host = $p['host'];
			$this->username = $p['username'];
			$this->password = $p['password'];
			$this->dbname = $p['dbname'];
			return true;
		}
	}
	function query($sql){
		if(!$this->dblink){//只有执行sql的时候才有数据库链接
			$this->dblink = mysql_connect($this->host,$this->username,$this->password) or die('连接失败:' . mysql_error());
			mysql_select_db($this->dbname,$this->dblink) or die('连接失败:'.mysql_error());
			mysql_query("set names utf8");
		}
		return mysql_query($sql,$this->dblink);
	}
	function getRow($sql){	//取出一条数据
		$query = $this->query($sql);
		if($query){
			$data = mysql_fetch_array($query,MYSQL_ASSOC);
		}
		return $data;
	}
	function getRows($sql){		//取出多条数据
		$query = $this->query($sql);
		$i=0;
		$data = array();
		while($row = mysql_fetch_array($query,MYSQL_ASSOC)){
			$data[$i] = $row;
			$i++;
		}
		return $data;
	}
	function insert($table, $data, $return = false,$debug=false) {//插入数据,debug为真返回sql
		if(!$table) {
			return false;
		}
		$fields = array();
		$values = array();
		foreach ($data as $field => $value){
			$fields[] = '`'.$field.'`';
			$values[] = "'".addslashes($value)."'";
		}
		if(empty($fields) || empty($values)) {
			return false;
		}
		$sql = 'INSERT INTO `'.$table.'` 
				('.join(',',$fields).') 
				VALUES ('.join(',',$values).')';
		if($debug){
			return $sql;
		}
		$query = $this->query($sql);
	        return $return ? mysql_insert_id() : $query;
	}
	function update($table, $condition, $data, $limit = 1) {//更新数据
		if(!$table) {
			return false;
		}
		$set = array();
		foreach ($data as $field => $value) {
			$set[] = '`'.$field.'`='."'".addslashes($value)."'";
		}
		if(empty($set)) {
			return false;
		}
		$sql = 'UPDATE `'.$table.'` 
				SET '.join(',',$set).' 
				WHERE '.$condition.' '.
				($limit ? 'LIMIT '.$limit : '');
		return $this->query($sql);
	}
}
?>
