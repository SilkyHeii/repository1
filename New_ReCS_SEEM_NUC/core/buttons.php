<?php
	require_once("ticket.php");
	require_once("db.php");
	require_once("envs.php");
	
	
	function RegisterTalkButtons($db,$ticketId,$message){
		if(!VerifyTicket($db,$ticketId,"edit_account")){
			error_log("RegisterTalkButtons:AUTHORIZE ERROR",0);
			die();
		}
		$userid=GetUserId($db,$ticketId);
		$talkbuttonid=GetEnvironment($db,"notalkbuttons");
		mysql_query(
			"insert into buttons values($talkbuttonid,$userid,'$message',NOW())"
		);
		SetEnvironment($db,"notalkbuttons",$talkbuttonid+1);
		return $talkbuttonid;
	}
	function DeleteTalkButtons($db,$ticketId,$talkbuttonid){
		if(!VerifyTicket($db,$ticketId,"edit_account")){
			error_log("DeleteTalkButtons:AUTHORIZE ERROR",0);
			die();
		}
		mysql_query(
			"delete from buttons where id='$talkbuttonid'"
		);
	}
	function GetTalkButton($db,$ticketId,$talkButtonId){
		if(!VerifyTicket($db,$ticketId,"read_account")){
			error_log("GetTalkButtons:AUTORIZE ERROR",0);
			die();
		}
		$talkbuttons=mysql_query(
			"select * from buttons where id=$talkButtonId"
		);
		$talkbutton=mysql_fetch_assoc($talkbuttons);
		if(!$talkbutton){
			error_log("GetTalkButton:INVALID TALKBUTTON",0);
			die();
		}
		return $talkbutton;
	}
	function GetTalkButtonIdFrom($talkButton){
		return $talkButton["id"];
	}
	function GetTalkButtonOwnerUserIdFrom($talkButton){
		return $talkButton["userid"];
	}
	function GetTalkButtonMessageFrom($talkButton){
		return $talkButton["message"];
	}
	function GetTalkButtonPublishDateTimeFrom($talkButton){
		return $talkButton["publish"];
	}
?>
