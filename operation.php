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

	static function getContants() {
		$reflection_class = new ReflectionClass(__CLASS__);
		return $reflection_class->getConstants();
	}
}

class OperationLogger {

	static function log($channel, $message, $level=Logger::INFO) {
		$logger = new Logger($channel);
		$redis_client = RedisClient::getInstance();
		$key = $channel;	
		$redis_handler = new RedisHandler($redis_client->client, $key, $level);
		$json_formater = new JsonFormatter();
		$redis_handler->setFormatter($json_formater);
		$logger->pushHandler($redis_handler);
		return $logger->addRecord($level, $message);
	}

	static function get_record_all($key, $start=0, $limit=-1) {
		$redis_client = RedisTool::getInstance();
		$val = $redis_client->getRedisClient()->lrange($key, $start, $limit);
		return $val;
	}

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

	static function filtered_users($data) {
		$urid = isset($data['urid']) ? $data['urid'] : null;
		$black_list = array('5594d7b9cc4e93c8490000da', '51541d2ee8ad7e6b5c000039');
		if(in_array($urid, $black_list)) {
			return true;
		} 
		return false;
	}

	static function quit() {
		$redis_client = RedisTool::getInstance();
		return $redis_client->quit();
	}

	/**
	 *  key: channel + type
	 */
	static function add_record($type, $data, $channel='user_operation') {
        error_log(json_encode($data));
		if(OPERATION_LOGGER_OPEN === true) {
			if(self::filtered_users($data) === false) {
				$redis_tool = RedisTool::getInstance();
				$redis_client = $redis_tool->getRedisClient();	
				$key = sprintf("%s_%s", $channel, $type);
				$default = array('time'=>date('Y-m-d H:i:s'), 'type'=>$type);
				$data = array_merge($default, $data);
				$json_string = json_encode($data, true);
				try{
					$ret = $redis_client->rpush($key, $json_string);	
				} catch (Exception $e){
					$mail_info = include (ROOT_PATH . "logger/mail_config.php");
					$content = sprintf("[redis error] [%s] %s\n %s\n", date('Y-m-d H:i:s'), $e->getMessage(), json_encode($e->getTrace(), true));
					SendEmail('', '', $mail_info['subject'], $content, $mail_info['tos']);
				}
				return $ret;
			}
		}
	}

	static function add_login_record($data){
		return self::add_record(Constants::USER_LOGIN, $data);
	}

	static function add_logout_record($data){
		return self::add_record(Constants::USER_LOGOUT, $data);
	}

	static function add_creative_new_record($data){
		return self::add_record(Constants::USER_CREATE_CREATIVE, $data);
	}

	static function add_creative_remove_record($data){
		return self::add_record(Constants::USER_DELETE_CREATIVE, $data);
	}

	static function add_creative_export_record($data){
		return self::add_record(Constants::USER_EXPORT_CREATIVE, $data);
	}

	static function add_creative_publish_record($data){
		return self::add_record(Constants::USER_PUBLISH_CREATIVE, $data);
	}

	static function add_buy_record($data){
		return self::add_record(Constants::USER_BUY_SERVICE, $data);
	}

	static function add_asset_new_record($data){
		return self::add_record(Constants::USER_ADD_ASSET, $data);
	}

	static function add_asset_remove_record($data){
		return self::add_record(Constants::USER_DELETE_ASSET, $data);
	}

	static function add_subaccount_new_record($data){
		return self::add_record(Constants::USER_ADD_SUBACCOUNT, $data);
	}

	static function add_subaccount_remove_record($data){
		return self::add_record(Constants::USER_DELETE_SUBACCOUNT, $data);
	}

	static function add_no_access_record($data) {
		return self::add_record(Constants::USER_NO_ACCESS, $data);
	}

    static function add_extract_share_creative($data) {
        return self::add_record(Constants::USER_EXTRACT_SHARE_CREATIVE, $data);
    }

}


