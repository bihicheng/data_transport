<?php

require_once "mongodb_tool.php";


$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();
$cursor = $user_operation->find();

function logcount() {
	global $cursor;
	echo $cursor->count();
}

function forloop() {
	global $cursor;
	foreach($cursor as $data) {
		echo PHP_EOL; print_r($data); echo PHP_EOL;
	}
}

function main() {
	logcount();
	forloop();
}
