<?php

require_once "sys_config.php";
require_once "mongodb_tool.php";

$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();

$options = getopt('a:');
if(isset($options['a']) && $options['a'] == 'all') {
	$cursor = $user_operation->find();
} else if(isset($options['a']) && $options['a'] == 'lastday') {
	// yearstoday
	$lastday = date('Y-m-d', strtotime('-1 day'));
	// today 
	$today = date('Y-m-d', strtotime('today'));
	$cursor = $user_operation->find(array('time'=>array('$gte'=>new MongoDate(strtotime($lastday)), 
							    '$lt'=>new MongoDate(strtotime($today)))));
} else {
	echo "Usage: php import_login_log.php -a [all | lastday]";exit;
}

$site_mongo = new MongodbTool(SITE_MONGODB_CONNECT_STRING);
$login_logs = $site_mongo->get_user_login_logs();
$login_cursor = $login_logs->find();


function import_user_login_record() {
	global $cursor;
	global $login_logs;
	foreach($cursor as $login_log) {
		unset($login_log['message']);
		unset($login_log['type']);
		$condition = array('_id'=>$login_log['_id']);
		$record_count = $login_logs->find($condition)->count();
		if($record_count > 0){
			continue;
		}
		$login_logs->insert($login_log);
	}
}


import_user_login_record();
