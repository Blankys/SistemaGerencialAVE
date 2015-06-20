<?php
class DATABASE_CONFIG {

	public $default = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'marlonmm',
		'password' => '114i2012mm10020',
		'database' => 'sigave',
		'prefix' => '',
		'encoding' => 'utf8'
	);

	public $test = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'user',
		'password' => '',
		'database' => 'database_name',
		'prefix' => '',
		'encoding' => 'utf8'
	);
}
