<?php

require_once "mongodb_tool.php";
require_once "sys_config.php";

$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();
$cursor = $user_operation->find()->sort(array('time'=>-1));


$site_mongo = new MongodbTool(SITE_MONGODB_CONNECT_STRING);
$login_logs = $site_mongo->get_user_login_logs();
$login_cursor = $login_logs->find()->sort(array('time'=>-1));

function logcount() {
	global $cursor;
	echo $cursor->count() . PHP_EOL;
}

function stats_log($cond) {
	global $user_operation;
	$cursor = $user_operation->find($cond)->sort(array('time'=>-1));
	foreach($cursor as $l) {
		echo date('Y-m-d H:i:s', $l['time']->sec);
		var_dump($l);break;
	}
}

function forloop() {
	global $cursor;
	global $user_operation;
	$counter = 10;
	foreach($cursor as $data) {
		echo date('Y-m-d H:i:s', $data['time']->sec);
		print_r($data); echo PHP_EOL;
		$counter -= 1;
		if($counter == 0) break;
	}
}

function forloop_update() {
	global $cursor;
	global $user_operation;
	foreach($cursor as $data) {
		if(gettype($data['time']) != 'object'){
			$user_operation->update(array('_id'=>$data['_id']), array('$set'=>array('time'=>new MongoDate(strtotime($data['time'])), 
											'urid'=>new MongoId($data['urid']))));
		}
	}
}

function userlogs() {
	global $login_cursor;
	echo $login_cursor->count() . PHP_EOL;
	foreach($login_cursor as $login_log) {
		var_dump(date('Y-m-d H:i:s', $login_log['time']->sec));break;
	}
}

function add_user_login_record() {
	global $user_operation;
	global $login_logs;
	$cursor = $user_operation->find(array('type'=>1));
	foreach($cursor as $login_log) {
		unset($login_log['message']);
		unset($login_log['type']);
		$login_logs->insert($login_log);
	}
}

function main() {
	#logcount();
	#userlogs();
	#forloop();
}

