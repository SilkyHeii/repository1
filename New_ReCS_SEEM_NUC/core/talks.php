<?php 
	require_once("users.php");
	require_once("groups.php");
	require_once("ticket.php");

	function TalkTo($db,$receiveUserIdArray,$talkAbout,$ticketId){
		if(!VerifyTicket($db,$ticketId,"talk")) {
			error_log("TalkTo: AUTHORIZE ERROR",0);
			die();
		}
		$senduserID=GetUserID($db,$ticketId);
		$receiveusersStr = ",";
		foreach($receiveUserIdArray as $userid) {
			if(strcmp($userid,"")==0)continue;
			$receiveusersStr = $receiveusersStr . $userid . ",";
		}
		$talkID=GetEnvironment($db,"notalks");
		mysql_query(
			"insert into talks values($talkID,$senduserID,'$receiveusersStr','$talkAbout',NOW())"
			);
		SetEnvironment($db,"notalks",$talkID+1);
		return $talkID;
	}
	function RespondTo($db,$talkAbout,$ticketId,$returnToId){
		if(!VerifyTicket($db,$ticketId,"talk")) {
			error_log("RETURNTO: AUTHORIZE ER",0);
			die();
		}
		//~ 返信するためのソースを以下に書く、どのメッセージに対して返信なのかを特定する必要がある
		$senduserID=GetUserID($db,$ticketId);
		$talkID=GetEnvironment($db,"notalks");
		mysql_query(
			"insert into talks values($talkID,$senduserID,'$returnToId','$talkAbout',NOW())"
			);
		SetEnvironment($db,"notalks",$talkID+1);
		return $returnID;
		
		
	}
	function TalkToGroupsAndUsers($db,$groups,$receivers,$talkAbout,$ticketId){
		if(!VerifyTicket($db,$ticketId,"talk")) {
			error_log("TalkToGroupsAndUsers: AUTHORIZE ERROR",0);
			die();
		}
		$targets = array();
		foreach($receivers as $receiver) {
			$targets []= $receiver;
		}
		foreach($groups as $group) {
			$userlist = GetUsers($db,$group,$ticketId);
			foreach($userlist as $user) {
				$userid=GetUserIDFrom($user);
				if(in_array($userid,$targets))continue;
				$targets []= $userid;
			}
		}
		return TalkTo($db,$targets,$talkAbout,$ticketId);
	}
	function ListenTalk($db,$ticketId){
		if(!VerifyTicket($db,$ticketId,"listen")) {
			error_log("ListenTo: AUTHORIZE ERROR",0);
			die();
		}
		$userid=GetUserID($db,$ticketId);
		$talks=mysql_query(
			"select * from talks where receiveuserids like '%,${userid},%' order by -publish"
			);
		$result = array();
		while($talk = mysql_fetch_assoc($talks)) {
			$result []= $talk;
		}
		return $result;
	}
	function GetMyTalk($db,$ticketId){
		if(!VerifyTicket($db,$ticketId,"listen")) {
			error_log("ListenTo: AUTHORIZE ERROR",0);
			die();
		}
		$userid=GetUserID($db,$ticketId);
		$talks=mysql_query(
			"select * from talks where receiveduserids like '%,${userid},%' or senduserid=$userid order by -publish"
			);
		$result = array();
		while($talk = mysql_fetch_assoc($talks)) {
			$result []= $talk;
		}
		return $result;
	}
	function LookForTalk($db,$word,$property,$ticketId){
		if(!VerifyTicket($db,$ticketId,"listen")) {
			error_log("LookForTalk: AUTHORIZE ERROR",0);
			die();
		}
		$userid=GetUserID($db,$ticketId);
		$talks=mysql(
			"select * from talks where $property like '%${word}%' order by -publish"
			);
		$result = array();
		while($talk = mysql_fetch_assoc($talks)) {
			$result []= $talk;
		}
		return $result;
	}
	function LookForTalkBySendUserName($db,$sendUserName,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_account")) {
			error_log("LookForTalkByUserName: AUTHORIZE ERROR",0);
			die();
		}
		$receiveUserID=GetUserID($db,$ticketId);
		$userids=mysql_query(
			"select id from users where name like '%${sendUserName}%'"
			);
		$result = array();
		while($userid = mysql_fetch_assoc($userids)) {
			$uid = $userid["id"];
			$talks=mysql_query(
				"select * from talks where senduserid=$uid and receiveuserids like '%,${receiveUserID},%' order by -publish"
				);
			
			while($talk = mysql_fetch_assoc($talks)) {
				$result []= $talk;
			}
		}
		return $result;
	}
	function LookForByTalk($db,$word,$ticketId){
		return LookForTalk($db,$word,"talk",$ticketId);
	}
	function LookForBySpan($db,$beginDateTime,$tailDateTime,$ticketId){
		if(!VerifyTicket($db,$ticketId,"listen")) {
			error_log("LookForBySpan: AUTHORIZE ERROR",0);
			die();
		}
		
		$userid=GetUserID($db,$ticketId);
		$talks=mysql_query(
			"select * from talks where receiveuserids like '%,${userid},%' and publish between '$beginDateTime' and '$endDateTime' order by -publish"
			);
		$result = array();
		while($talk = mysql_fetch_assoc($talks)) {
			$result []= $talk;
		}
		return $result;
	}
	function GetReceiveUsers($db,$talkId,$ticketId){
		if(!VerifyTicket($db,$ticketId,"listen")) {
			error_log("GetReceiveUsers: AUTHORIZE ERROR",0);
			die();
		}
		$useridss=mysql_query(
			"select receiveuserids from talks where id=$talkID"
			);
		$useridsStr=mysql_fetch_assoc($useridss);
		if(!$useridsStr) {
			return NULL;
		}
		$result = array();
		$userids=explode(",",$useridsStr["receiveuserids"]);
		foreach($userids as $userid) {
			if(strcmp($userid,"")==0)continue;
			$result []= $userid;
		}
		return $result;
	}
	function GetReceiverUsersFrom($talk){
		$userids=explode(",",$talk["receiveuserids"]);
		$result = array();
		foreach($userids as $userid) {
			if(strcmp($userid,"")==0)continue;
			$result []= $userid;
		}
		return $result;
	}
	function GetPublishDateTimeFrom($talk){
		return $talk["publish"];
	}
	function GetTalkMessageFrom($talk){
		return $talk["talks"];
	}
	function GetTalkId($talk){
		return $talk["id"];
	}
	function GetSendUserId($talk){
		return $talk["senduserid"];
	}
	function GetTalk($db,$talkId,$ticketId){
		if(!VerifyTicket($db,$ticketId,"listen")) {
			error_log("GetTalk: AUTHORIZE ERROR",0);
			die();
		}
		mysql_select_db("chat_production",$db);
		$talks=mysql_query(
			"select * from talks where id=$talkId"
			);
		$talk=mysql_fetch_assoc($talks);
		return $talk;
	}
	function DeleteTalk($db,$talkId,$ticketId){
		if(!VerifyTicket($db,$talkId,$ticketId)){
			error_log("DeleteTalk:AUTHORIZE ERROR",0);
			die();
		}
		mysql_query(
			"delete from talks where id = $talkId"
		);
	}
?>
