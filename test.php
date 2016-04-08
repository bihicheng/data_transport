<?php

require_once 'query_mongodb.php';
#add_user_login_record();
#userlogs();
stats_log(array('type'=>16, 'time'=>array('$gte' =>new MongoDate(strtotime('2016-04-01')), '$lt'=> new MongoDate(strtotime('2016-04-10')))));
