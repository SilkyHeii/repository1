<?php
	require_once("db.php");
	function GetServerConf($db) {
		if(!$db) {
			error_log("GetServerConf: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)) {
			error_log("GetServerConf: DB SELECT ERROR",0);
			die();
		}
		
		$serverconfs=mysql_query(
			"select * from envs where id like 'server%'"
			);
			
		$result=array();
		while($serverconf=mysql_fetch_assoc($serverconfs)) {
			$key=$serverconf["id"];
			$value=$serverconf["value"];
			$result[$key]=$value;
		}
		return $result;
	}
	function GetServerIpAddressFrom($conf) {
		return $conf["serveripaddress"];
	}
	function GetWebSocketPortFrom($conf) {
		return $conf["serverwebsocketport"];
	}
?>
