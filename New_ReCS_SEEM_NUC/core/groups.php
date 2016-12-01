<?php
	require_once("ticket.php");
	function GetUsers($db,$groupid,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_group")){
				error_log("GetUsers:AUTHORIZE ERROR",0);
				die();
		}
		$users=mysql_query(
			"select id,name,groups from users where groups like '%,${groupid},%'"
		);
		$result=array();
		
		while($user = mysql_fetch_assoc($users)){
			$result []= $user;
		}
		return $result;
	}
	function RegisterGroup($db,$groupname,$description,$password,$ticketId){
		if(!VerifyTicket($db,$ticketId,"edit_group")){
			error_log("RegisterGroup:AUTHORIZE ERROR",0);
			die();
		}
		$groupid=GetEnvironment($db,"nogroup");
		mysql_query(
			"insert into groups values($groupid,'$groupname','$description',PASSWORD('$password'),'')"
		);
		SetEnvironment($db,"nogroup",$groupid+1);
		return $groupid;
	}
	function DeleteGroup($db,$groupid,$ticketId){
		if(!VerifyTicket($db,$ticketId,"edit_group")){
			error_log("DeleteGroup:AUTHORIZE ERROR",0);
			die();
		}
		mysql_query(
			"delete from group where id=$groupid"
		);
	}
	function GetGroupData($db,$groupId,$ticketId){
		if(!VerifyTicket($db,$groupid,"read_group")){
			error_log("GetGroupData:AUTHORIZE ERROR");
			die();
		}
		$groups=mysql_query(
			"select * from groups where id=$groupid"
		);
		$group=mysql_fetch_assoc($groups);
		if(!$group){
			error_log("GetGroupData:INVALID GROUP",0);
			die();
		}
		return $group;
	}
	function GetGroupName($db,$groupId,$ticketId){
		return GetGroupData($db,$groupId,$ticketId)["name"];
	}
	function GetGroupDescription($db,$groupId,$ticketId){
		return GetGroupData($db,$groupId,$ticketId)["description"];
	}
	function GetAllGroup($db,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_group")){
			error_log("GetAllGroup:AUTHORIZE ERROR",0);
			die();
		}
		$groups=mysql_query(
			"select id,name,description from groups "
		);
		$result = array();
		while($group = mysql_fetch_assoc($groups)){
			$result []= $group;
		}
		return $result;
	}
	function GetAllPassWordGroup($db,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_group")){
			error_log("GetAllPassWordGroup:AUTHORIZE ERROR",0);
			die();
		}
		$groups=mysql_query(
			"select id,name,description from groups where password<>''"
		);
		$result=array();
		while($group = mysql_fetch_assoc($groups)){
			$result []= $group;
		}
		return $result;
	}
	function GetAllPassWordLessGroup($db,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_group")){
			error_log("GetAllPassWordLessGroup",0);
			die();
		}
		$groups=mysql_query(
			"select id,name,description from groups where password=''"
		);
		$result = array();
		while($group = mysql_fetch_assoc($groups)){
			$result = $group;
		}
		return $result;
	}
	function GetGroupIdFrom($group){
		return $group["id"];
	}
	function GetGroupNameFrom($group){
		return $group["name"];
	}
	function GetGroupDescriptionFrom($group){
		return $group["description"];
	}
	function GroupPassWordAuthorize($db,$gid,$password,$ticketId){
		if(!$db){
			error_log("GroupPassWordAuthorize:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GroupPassWordAuthorize:DB SELECT ERROR",0);
			die();
		}
		$groupdata=GetGroupData($db,$gid,$ticketId);
		$sqlPass=PassWordSQL($db,$password);
		return strcmp($sqlPass,$groupdata["password"])==0;
	}
	function GetGroupOwners($db,$groupId,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_group")){
			error_log("GetGroupOwners:AUTHORIZE ERROR",0);
			die();
		}
		$ownerss=mysql_query(
			"select owners from groups where id=$groupId"
		);
		$owners=mysql_fetch_assoc($ownerss);
		if(!$owners){
			error_log("GetGroupOwners:INVALID GROUP",0);
			die();
		}
		$ownersStrs=explode(",",$owners["owners"]);
		$result = array();
		foreach($ownersStrs as $ownerStr){
			if(strcmp("",$ownerStr)==0)continue;
			$result []= $ownerStr;
		}
		return $result;
	}
	function SetGroupOwners($db,$groupId,$ownersArray,$ticketId){
		if(!VerifyTicket($db,$ticketId,"edit_group")){
			error_log("SetGroupPwners:AUTHORIZE ERROR",0);
			die();
		}
		$ownersStr=",";
		foreach($ownersArray as $owner){
			$ownersStr = $ownersStr . $owner . ",";
		}
		mysql_query(
			"update groups set owners='$ownersStr' where id=$groupId"
		);
	}
	function SetGroupProperty($db,$groupId,$property,$value,$ticketId){	
		if(!VerifyTicket($db,$ticketId,"edit_group")){
			error_log("SetGroupProperty:AUTORIZE ERROR",0);
			die();
		}
		if(strcmp("id",$property)==0){
			error_log("SetGroupProperty:INVALID OPERATION",0);
			die();
		}
		mysql_query(
			"update groups set $property='$value' where id=$groupId"
		);
	}
	function SetGroupName($db,$groupId,$groupname,$ticketId){
		SetGroupProperty($db,$groupId,"name",$groupname,$ticketId);
	}
	function SetGroupDescription($db,$groupId,$description,$ticketId){
		SetGroupProperty($db,$groupId,"description",$description,$ticketId);
	}
	function SetGroupPassWord($db,$groupId,$password,$ticketId){
		$pwd=PassWordSQL($db,$password);
		SetGroupProperty($db,$groupId,"password",$pwd,$ticketId);
	}
?>
