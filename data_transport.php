<?php

// author: hichengbi@mugeda.com
require_once 'operation.php';
require_once 'mongodb_tool.php';

$constants = Constants::getContants();

$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();

$seconds = intval($argv[1]);
if($seconds <= 0) $seconds = 10;

while(true) {
	foreach($constants as $constant) {
		$key = 'user_operation_' . $constant;
		echo $key . PHP_EOL;
		echo OperationLogger::count($key) . PHP_EOL;
		while(OperationLogger::count($key) > 0) {
			$val = OperationLogger::pop_record($key);
			$user_operation->insert(json_decode($val));	
		}
	}
	OperationLogger::quit(); // close redis connection
	sleep($seconds);
}
