<?php
require_once("db.php");

//!ここでは環境変数をロードする
/*!
 * \param _id 環境変数のid
 * */

function GetEnvironment($db,$id){
	//~ $db=mysql_connect('localhost','root','');
	//~ $db_select = mysql_select_db('chat_production' ,$db);
	if(!$db){
		error_log("GetEnvironment:SQL ERROR");
		die();
	}
	if(!mysql_select_db("chat_production",$db)){
		error_log("GetEnvironment:DB SELECT ERROR",0);
		die();
	}
	$result=mysql_query("select value from envs where id ='$id'");
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	return mysql_fetch_assoc($result)["value"];
}
function SetEnvironment($db,$id,$value){
	//~ $db=mysql_connect('localhost','root','');
	//~ $db_select = mysql_select_db('chat_production' ,$db);
		if(!$db) {
			error_log("SetEnvironment: SQL ER",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)) {
			error_log("SetEnvironment: DB SELECT ERROR",0);
			die();
		}
	mysql_query("update envs set value='$value' where id ='$id'");
	}
?>
