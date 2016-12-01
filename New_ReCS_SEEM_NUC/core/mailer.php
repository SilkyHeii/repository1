<?php
	require_once("ticket.php");
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");

	function SendMail($ticketId,$userid,$destAddress,$fromAddress,$title,$msgbody){
		if(!VerifyTicket($db,$ticketId,"create_mail")){
			error_log("SendMail:Authrize ERROR");
			die();
		}
		$username=GetUserName($db,$userid);
		//~ メール送信処理を以下に書く
		if(!mb_send_mail($destAddress,$username.":".$title,$msgbody,$fromAddress)){
			error_log("SEND MAIL ERROR");
			die();
		}
	}
?>
