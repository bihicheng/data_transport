<?php
/**
 * @Author: hichengbi@mugeda.com
 * @Date:   2015-10-15 14:51:12
 * @Last Modified by:   haicheng
 * @Last Modified time: 2015-10-15 16:26:45
 */

require_once(dirname(__FILE__) . '/sys_config.php');
require_once 'vendor/autoload.php';


class RedisTool {

	private static $_instance = null;
	private $client = null;	
	static function getInstance() {
		if(empty(self::$_instance)) { 
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct($connect_str=null) {
		if(empty($connect_str)) {
			$connect_str = REDIS_CONNECT_STRING;
		}
		$this->client = new Predis\Client($connect_str);
	}

	function __clone(){
		return null;
	}

	function getRedisClient() {
		return $this->client;
	}

	function quit() {
		return $this->client->quit();
	}

	function set($key, $val) {
		return $this->client->set($key, $val);
	}
	
	function get($key) {
		return $this->client->get($key);
	}
}
