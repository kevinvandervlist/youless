<?php
session_start();

if (isset($_POST['login']) && $_POST['login'] == 1) {
	include "classes/hash.class.php";
	$hash = new Hash();

	$u = addslashes($_POST['user']);
	$p = $hash->generatePasswordHash($_POST['user'], $_POST['pass']);
	
	$udb = new Database();
	$r = $udb->getLogin($u, $p);

	if ($r) {
		$_SESSION['user_id'] = $r;
	} else {
		$_SESSION['user_id'] = false;
	}
}

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
		unset($_SESSION['user_id']);
}

if (!isset($_SESSION['user_id']) || isset($_SESSION['user_id']) && !$_SESSION['user_id']) {
	header("Location: login.php");
	exit;
}

?>
