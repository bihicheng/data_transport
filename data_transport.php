<?php
// author: hichengbi@mugeda.com

require_once 'operation.php';
require_once 'mongodb_tool.php';

$constants = Constants::getContants();

$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();


$seconds = isset($argv[1]) ? intval($argv[1]) : 0;
if($seconds <= 0) $seconds = 10;

while(true) {
	foreach($constants as $constant) {
		$key = 'user_operation_' . $constant;
		echo $key . PHP_EOL;
		echo OperationLogger::count($key) . PHP_EOL;
		while(OperationLogger::count($key) > 0) {
			$val = OperationLogger::pop_record($key);
			try {
				$user_operation->insert(json_decode($val));	
			} catch (Exception $e) {
				file_put_contents('error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
				file_put_contents('transport.log', $val . PHP_EOL , FILE_APPEND);
			}
			
		}
	}
	OperationLogger::quit(); // close redis connection
	sleep($seconds);
}
