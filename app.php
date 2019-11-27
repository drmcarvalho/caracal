<?php

require_once 'Caracal.php';

$app = new Caracal([
	'database_type' => 'mysql',
	'database_name' => 'walmirbd',
	'server' => 'localhost',
	'username' => 'root',
	'password' => ''
]);

require_once 'actions.php';
