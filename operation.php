<?php
/**
 * @Author: hichengbi@mugeda.com
 * @Date:   2015-10-15 14:51:12
 * @Last Modified by:   haicheng
 * @Last Modified time: 2015-10-15 16:26:45
 */
require_once dirname(__FILE__) . "/redis_tool.php";

use Monolog\Logger;
use Monolog\Handler\RedisHandler;
use Monolog\Formatter\JsonFormatter;

class Constants {
	const USER_LOGIN = 1;
	const USER_LOGOUT = 2;
	const USER_CREATE_CREATIVE = 3;
	const USER_DELETE_CREATIVE = 4;
	const USER_EXPORT_CREATIVE = 5;
	const USER_PUBLISH_CREATIVE = 6;
	const USER_BUY_SERVICE = 7;
	const USER_ADD_ASSET = 8;
	const USER_DELETE_ASSET = 9;
	const USER_ADD_SUBACCOUNT = 10;
	const USER_DELETE_SUBACCOUNT = 11;
	const USER_NO_ACCESS = 12;
    const USER_EXTRACT_SHARE_CREATIVE = 13;
    const USER_VISIT_PRICE_PAGE = 14;
    const USER_VISIT_SERVICE_PAGE = 15;
    const USER_VISIT_PAY_PAGE = 16;
    const USER_VISIT_PAY_RESULT_PAGE = 17;
    const USER_VISIT_PAY_NOW_PAGE = 18;

	static function getContants() {
		$reflection_class = new ReflectionClass(__CLASS__);
		return $reflection_class->getConstants();
	}
}

class OperationLogger {

	static function count($key) {
		$redis_client = RedisTool::getInstance();
		$val = $redis_client->getRedisClient()->llen($key);
		return $val;
	}

	static function pop_record($key){
		$redis_client = RedisTool::getInstance();
		$val = $redis_client->getRedisClient()->rpop($key);
		return $val;
	}
	
	static function trim($key, $start=0, $end=0) {
		$redis_client = RedisTool::getInstance();
		$val = $redis_client->getRedisClient()->ltrim($key, $start, $end);
		return $val;
	}
	
	static function delete_key($key) {
		$redis_client = RedisTool::getInstance();
		$val = $redis_client->getRedisClient()->del($key);
		return $val;
	}

	static function quit() {
		$redis_client = RedisTool::getInstance();
		return $redis_client->quit();
	}
}


