<?php

// author: hichengbi@mugeda.com

require_once(dirname(__FILE__) . '/sys_config.php');

class MongodbTool {

	private static $_instance = null;
	private $client = null;
	static function getInstance($connect_str=null) {
		if(empty(self::$_instance)) { 
			self::$_instance = new self($connect_str);
		}
		return self::$_instance;
	}

	public function __construct($connect_str=null) {
		$options = array();
		if(empty($connect_str)) {
			$connect_str = MONGODB_CONNECT_STRING;
		}

		try {
			$this->client = new Mongo($connect_str, $options);
		} catch (MongoConnectionException $e) {
		    	if ($this->client) {
	    			$this->client->close();
		    	}
			die('Failed to connect to MongoDB:' . $e->getMessage());
		}

		if($connect_str == MONGODB_CONNECT_STRING) {
			$this->_mugeda_db = $this->client->selectDB(MONGODB_DB_NAME);
			$this->_mugeda_db->setSlaveOkay();
			$this->_user_operations = $this->_mugeda_db->selectCollection('user_operations');
		} else {
			$this->_mugeda_db = $this->client->selectDB('mugeda');
			$this->_mugeda_db->setSlaveOkay();
			$this->_user_login_logs = $this->_mugeda_db->selectCollection('user_login_logs');
		}
	}

	function __clone(){
		return null;
	}

	function getMongoClient() {
		return $this->client;
	}

	function set($data){
		return $this->client->_test->insert($data);
	}

	function get_user_operations(){
		return $this->_user_operations;
	}
	function get_user_login_logs(){
		return $this->_user_login_logs;
	}
}

