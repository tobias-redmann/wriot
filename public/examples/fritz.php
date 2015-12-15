<?php

include('../FritzSmartHome/FritzSmartHome.php');

$username = 'YOUR USERNAME';
$password = 'YOUR PASSWORD';

// initialize
$fsh = new FritzSmartHome('http://192.168.100.1');

if ($fsh->connect($username, $password)) {

	$fsh->getConnectedDevices();
	$fsh->toggleSwitch('087610236230');

}