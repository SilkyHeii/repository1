<?php 
	require_once("envs.php");
	function ConnectSQL(){
		$db=mysql_connect("localhost","root","");
		if(!$db){
			error_log("ConnectSQL:SQL ERROR",0);
			die();
		}
		return $db;
	}
	function DisposeSQL($db){
		if(!$db){
			error_log("DisposeSQL:SQL ERROR",0);
			die();
		}
		mysql_close($db);
	}
	function PassWordSQL($db,$word){
		if(!$db){
			error_log("PassWordSQL:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chatproduction",$db)){
			error_log("PassWordSQL:DB SELECT ERROR",0);
			die();
		}
		mysql_query(
			"update envs set value=PASSWORD('$word') where name ='temp0'"
		);
		$result=GetEnvironment($db,"temp0");
		SetEnvironment($db,"temp0","");
		return $result;
	}
?>
