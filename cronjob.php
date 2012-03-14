#!/usr/bin/php
<?php
	include "settings.inc.php";
	include "request.class.php";
	include "database.class.php";
	
	$request = new Request();
	$db = new Database();
	
	$data = json_decode($request->getLastHour(), true);	
	$values = '"'. implode('","', $data['val']) .'"';	
	
	$db->addHourlyData($data['tm'], $data['un'], $data['dt'], $values);
?>