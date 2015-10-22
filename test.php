<?php

require_once "mongodb_tool.php";


$mongo_tool = MongodbTool::getInstance();
$user_operation = $mongo_tool->get_user_operations();
$cursor = $user_operation->find();
echo $cursor->count();exit;
foreach($cursor as $data) {
	var_dump($data);
}


