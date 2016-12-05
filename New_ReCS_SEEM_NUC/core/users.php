<?php
	//~ ユーザが主体的に関わることをここで処理
	require_once("envs.php");
	require_once("db.php");
	require_once("authorize.php");

	function RegisterUser($db,$username,$password){

			if(!$db){
				error_log("RegisterUser: SQL ERROR",0);
				die();
			}
			if(!mysql_select_db("chat_production",$db)){
				error_log("RegisterUser:DB ERROR",0);
				die();
			}
			$uid=GetEnvironment($db,"nouser");
			mysql_query(
				"insert into users values($uid,'$username',PASSWORD('$password'),'','normal','')"
			);
			SetEnvironment($db,"nouser",$uid+1);
			return $uid;
	}
	function DeleteUser($db,$userid,$username,$password){
		if(!$db){
			error_log("DeleteUser:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("DeleteUser :DB ERROR",0);
			die();
		}
		if(!Authorize($db,$userid,$username,$password)){
			return false;
		}
		mysql_query(
			"delete from users where id=$userid"
		);
		return true;
	}
	function GetUserGroups($db,$userid){
		if(!$db){
			error_log("GetUserGroup:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GetUserGroup:DB ERROR",0);
			die();
		}
		$groupss=mysql_query(
			"select groups from users where id=$userid"
		);
		$groups=mysql_fetch_assoc($groupss);
		if(!$groups){
			error_log("GetUserGroups:INVALID USER",0);
			die();
		}
		$groups=explode(",",$groups["groups"]);
		$result = array();
		foreach($groups as $group){
			if(strcmp("",$group)==0)continue;
			$result []=$group;
		}
		return $result;
	}
	function GetUserPasswordGroups($db,$userid){
		$groups=GetUserGroups($db,$userid);
		$result=array();

		foreach($groups as $group){
			$temp=mysql_query(
				"select id,name,description from groups where id=$group and password<>''"
			);
			$temp1=mysql_fetch_assoc($temp);
			if(!$temp1)continue;
			$result []=$temp1;
		}
		return $result;
	}
	function GetUserOwnerGroup($db,$userid){
		if(!$db){
			error_log("GetUserOwnerGroup:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GetUserOwnerGroup:DB ERROR",0);
			die();
		}
		$groups=mysql_query(
			"select id from groups where owners like '%,${userid}'"
		);
		$result = array();
		while($group = mysql_fetch_assoc($groups)){
			$result []= $group["id"];
		}
		return $result;
	}
	//add_20161204
	function GetUserPlacement($db,$userid){
		if(!$db){
			error_log("GetUserPlacement:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GetUserPlacement:DB ERROR",0);
			die();
		}
		$placements=mysql_query(
			"select placement from users where id=$userid"
			);
		$placement=mysql_fetch_assoc($placements);
		if(!$placement) {
			error_log("GetUserPlacement: INVALID PLACEMENT",0);
			die();
		}
		return $placement["placement"];
	}
	function ContainsUserGroup($db,$userid,$groupid){
		if(!$db){
			error_log("ContainsUserGroup:DB ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("ContainsUserGroup:SQL ERROR",0);
			die();
		}
		$users=mysql_query(
			"select * from users where id=$userid and groups like '%,${userid}'"
		);
		$user=mysql_fetch_assoc($user);
		if(!$user){
			return false;
		}else{
			return true;
		}
	}
	function SetUserGroups($db,$userid,$groupArray){
		if(!$db) {
			error_log("SetUserGroups: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("SetUserGroups:SQL ERROR",0);
			die();
		}
		$groupsStr =",";
		foreach($groupsArray as &$group) {
			$groupsStr = $groupsStr . $group . ",";
		}
		mysql_query(
			"update users set groups='$groupsStr' where id=$userid"
		);
	}
	function SetUserAddress($db,$userid,$address){
		if(!$db) {
			error_log("SetUserAddress: SQL ERROR",0);
			die();
		}
		mysql_query(
			"update users set address=$address where id=$userid"
		);
	}
	//20161204_add
	function SetUserPlacement($db,$userid,$placement){
		//var_dump($placement);
		if(!$db){
			error_log("SetUserPlacement:SQL ERROR",0);
			die();
		}
		mysql_query(
			"update users set placement='$placement' where id=$userid"
		);
	}
	function GetUserName($db,$userid){
		if(!$db) {
			error_log("GetUserName: SQL ERROR",0);
			die();
		}

		if(!mysql_select_db("chat_production",$db)){
			error_log("GetUserName: DB SELECT ERROR",0);
			die();
		}
		$usernames=mysql_query(
			"select name from users where id=$userid"
			);

		$username=mysql_fetch_assoc($usernames);

		if(!$username) {
			error_log("GetUserName: INVALID USER",0);
			die();
		}

		return $username["name"];
	}
	function GetUserMailAddress($db,$userid){
		if(!$db) {
			error_log("GetUserMailAddress: SQL ERROR",0);
			die();
		}

		if(!mysql_select_db("chat_production",$db)){
			error_log("GetUserMailAddress: DB SELECT ERROR",0);
			die();
		}
		$usermailaddresss=mysql_query(
			"select address from users where id=$userid"
		);

		$usermailaddress=mysql_fetch_assoc($usermailaddresss);

		if(!$usermailaddress){
			error_log("GetUserMailAddress:INVALID USER",0);
		}

		return $usermailaddress["address"];
	}
	function SetUserName($db,$userid,$username){
		if(!$db) {
			error_log("SetUserName: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("SetUserName: DB SELECT ERROR",0);
			die();
		}
		mysql_query(
			"update users set name='$username' where id=$userid"
		);
	}
	function UpdatePassWord($db,$userid,$username,$currentPassword,$newPassword){
		if(!Authorize($db,$userid,$username,$currentPassword)) {
			return false;
		}
		mysql_query(
			"update users set password=PASSWORD('$newPassword') where id=$userid"
		);
	}
	function GetOtherUsers($db,$userid){
		if(!$db) {
			error_log("GetOtherUsers: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)) {
			error_log("GetOtherUsers: DB SELECT ERROR",0);
			die();
		}
		$others=mysql_query(
			"select id,name from users where id<>$userid"
			);
		$result=array();
		while($other = mysql_fetch_assoc($others)) {
			$result []= $other;
		}
		return $result;
	}
	function GetAllUsers($db){
		if(!$db) {
			error_log("GetAllUsers: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)) {
			error_log("GetAllUsers: DB SELECT ERROR",0);
			die();
		}
		$allusers=mysql_query(
			"select id,name from users"
		);
		$result=array();
		while($user = mysql_fetch_assoc($allusers) ) {
			$result []= $user;
		}
		return $result;
	}
	function GetUserIdFrom($user){
		return $user["id"];
	}
	function GetUserNameFrom($user){
		return $user["name"];
	}
	function GetUserTypeFrom($user){
		return $user["usertype"];
	}
	function GetUserTalkButtons($db,$userid){
		if(!$db) {
			error_log("GetUserTalkButtons: SQL ER",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)) {
			error_log("GetUserTalkButtons: DB SELECT ER",0);
			die();
		}
		$talkbuttons=mysql_query(
			"select * from buttons where userid=$userid"
		);
		$result=array();
		while($talkbutton = mysql_fetch_assoc($talkbuttons)) {
			$result []= $talkbutton;
		}
		return $result;
	}
?>
