<?php 
require 'environment.php';

global $db;
global $config;

$config = array();
if (ENVIRONMENT == 'development') 
{
	define("BASE_URL", "http://localhost/webservice_stock");
	$config['dbname'] = 'webservice_stock';
	$config['host'] = 'localhost';
	$config['dbuser'] = 'root';
	$config['dbpass'] = '';
	$config['jwt_secret_key'] = 'secret_123!';
} 
else 
{
	define("BASE_URL", "http://localhost/webservice_stock");
	$config['dbname'] = '';
	$config['host'] = 'localhost';
	$config['dbuser'] = 'root';
	$config['dbpass'] = '';
	$config['jwt_secret_key'] = 'secret_123!';
}

try
{
	$db = new PDO("mysql:dbname=".$config['dbname'].";host=".$config['host'], 
	$config['dbuser'], $config['dbpass'],
		array(
		    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
		    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		    PDO::FETCH_ASSOC,
		 ));
} 
catch(PDOException $e) 
{
	echo "ERRO: ".$e->getMessage();
}

