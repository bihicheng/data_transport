<?php
// author: hichengbi@mugeda.com

require_once 'operation.php';
require_once 'mongodb_tool.php';

$seconds = isset($argv[1]) ? intval($argv[1]) : 0;
if($seconds <= 0) $seconds = 10;

$constants = Constants::getContants();
try {
    $mongo_tool = MongodbTool::getInstance();
    $user_operation = $mongo_tool->get_user_operations();
} catch (Exception $e) {
    file_put_contents('logs/error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    exit;
}

while(true) {
	foreach($constants as $constant) {
		$key = 'user_operation_' . $constant;
        try {
            $count = OperationLogger::count($key);
            $exists = $count > 0;
		    #echo $key, ' => ', $count. PHP_EOL;
        } catch (Exception $e) {
            file_put_contents('logs/error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
            exit;
        }
		if($exists) {
            try {
			    $val = OperationLogger::pop_record($key);
            } catch (Exception $e) {
                file_put_contents('logs/error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
                exit;
            }
			try {
				$data = json_decode($val, true);
				$data['time'] = new MongoDate(strtotime($data['time']));
                if(isset($data['urid'])) {
                    try {
				        $data['urid'] = new MongoId($data['urid']);
                    } catch(Exception $e) {
                        $data['urid'] = '';
                    }
                }
                $options = array('safe' => true);
				$user_operation->insert($data, $options);	
			} catch (Exception $e) {
				file_put_contents('logs/error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
				file_put_contents('logs/transport.log', $val . PHP_EOL , FILE_APPEND);
                exit;
			}
		}
	}

    try {
	    OperationLogger::quit(); // close redis connection
    } catch (Exception $e) {
        file_put_contents('logs/error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
        exit;
    }
	sleep($seconds);
}
