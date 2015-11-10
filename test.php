<?php

require_once 'query_mongodb.php';
#add_user_login_record();
userlogs();
stats_log(array('type'=>1, 'time'=>array('$gte' =>new MongoDate(strtotime('2015-11-09')), '$lt'=> new MongoDate(strtotime('2015-11-10')))));
