<?php
	function InitializeClient() {
		session_start();
	}
	function GetClientValue($key) {
		return $_SESSION[$key];
	}
	function SetClientValue($key,$value) {
		$_SESSION[$key] = $value;
	}
	function IsSetClientValue($key) {
		return isset($_SESSION[$key]);
	}
	function ClearClientValue($key) {
		if(!IsSetClientValue($key)) return false;
		unset($_SESSION[$key]);
	}
?>
