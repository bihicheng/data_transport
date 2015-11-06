<?php

require_once "mongodb_tool.php";

$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();
$cursor = $user_operation->find();


$site_mongo = new MongodbTool('mongodb://mugeda_prod:niGsq0K0crXIy6y8KHhfgq80bLmAJEs7@10.171.237.164/mugeda');
$login_logs = $site_mongo->get_user_login_logs();
$login_cursor = $login_logs->find();

function logcount() {
	global $cursor;
	echo $cursor->count() . PHP_EOL;
}

function forloop() {
	global $cursor;
	global $user_operation;
	$counter = 10;
	foreach($cursor as $data) {
		print_r($data); echo PHP_EOL;
		$user_operation->update(array('_id'=>$data['_id']), array('$set'=>array('time'=>new MongoDate(strtotime($data['time'])), 
											'urid'=>new MongoId($data['urid']))));
		$counter -=1;
		if($counter == 0) {
			break;
		}
	}
}

function userlogs() {
	global $login_cursor;
	echo $login_cursor->count() . PHP_EOL;
	foreach($login_cursor as $login_log) {
		var_dump($login_log);break;
	}
}

function add_user_login_record() {
	global $user_operation;
	global $login_logs;
	$login_logs->remove();
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
