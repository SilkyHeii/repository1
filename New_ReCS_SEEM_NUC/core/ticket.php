
<?php
	require_once("users.php");
	require_once("authorize.php");
//~ チケットの生成を行う
	function PublishTicket($db,$userid,$username,$password,$lifeInDay,$actionsArray){
		
			//~ 無効な寿命なチケットか判定
			if(!($lifeInDay>=0)){	
				error_log("PublishTicket: INVALID LIFE OF TICKET",0);
				die();
			}
			
			//~ 認証が通らなかったら何も返さない
			if(!Authorize($db,$userid,$username,$password)){
				return NULL;
			}
			
			//~ データベースにアクセスできるか確認
			if(!mysql_select_db("chat_production",$db)){
				error_log("PublishTicket: DB SELECT ERROR",0);
			}
			
			
			//~ 環境変数から取得
			$ticketIterator=GetEnvironment($db,"notickets");
			//~ 暗号化された文字列の生成ー＞チケット文字列
			$ticketStr="#A%!DSF${ticketIterator}";
			mysql_query(
				"update envs set value=PASSWORD('$ticketStr')where id = 'temp0'"
			);
			$ticketIterator++;
			SetEnvironment($db,"notickets",$ticketIterator);
			$ticketId=GetEnvironment($db,"temp0");
			SetEnvironment($db,"temp0","");
			$actionsStr=",";
			foreach($actionsArray as $action){
				if(strcmp($action,"")==0)continue;
				$actionsStr=$actionsStr . $action . ",";
			}
			mysql_query(
				"insert into tickets values('$ticketId',$userid,$lifeInDay,CURDATE(),'$actionsStr')"
			);
			return $ticketId;
	}
	//~ チケットの認証を行う
	function VerifyTicket($db,$ticketId,$action){
		if(!mysql_select_db("chat_production",$db)){
			error_log("VerifyTicket:DB SELECT ERROR",0);
			//~ die();
		}
		$rows=mysql_query(
			"select * from tickets where id = '$ticketId' and life >= DATEDIFF(CURDATE(),publish) and actions like '%,${action},%'"
		);
		$row=mysql_fetch_assoc($rows);
		if(!$row){
			return false;
		}else{
			return true;
		}
	}
	//~ チケットを削除する
	function DisposeTicket($db,$ticketId){
		if(!mysql_select_db("chat_production",$db)){
			error_log("DisposeTicket:DB SELECT ERROR",0);
			die();
		}
		if(!VerifyTicket($db,$ticketId,"edit_account")){
			return false;
		}
		mysql_query(
			"delete from tickets where id = '$ticketId'"
		);
		return true;
	}
	function GetTicketAllowedActions($db,$ticketId){
		if(!$db){
			error_log("GetTicketAllowedActions: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GetTicketAllowedActions:DB SELECT ERROR",0);
			die();
		}
		$actionss=mysql_query(
			"select actions from tickets where id=$ticketId"
		);
		$actions=mysql_fetch_assoc($actionss);
		$actions=explode(",",$actions["actions"]);
		
		$result = array();
		foreach($actions as $action){
			if(strcmp("",$action)==0)continue;
			$result []= $action;
		}
		return $result;
	}
	function GetTicketOwnerUserId($db,$ticketId){
		if(!$db){
			error_log("GetTicketOwnerUserId: SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GetTicketOwnerUserId:DB SELECT ERROR",0);
			die();
		}
		$userid=mysql_query(
			"select userid from tickets where id=$ticketId"
		);
		$userid=mysql_fetch_assoc($userid);
		if(!$userid){
			return NULL;
		}
		return $userid["userid"];
	}
	function GetUserId($db,$ticketId){
		if(!VerifyTicket($db,$ticketId,"read_account")) {
			return NULL;
		}
		
		if(!mysql_select_db("chat_production",$db)) {
			error_log("GetUserID: DB SELECT ERROR",0);
			die();
		}
		
		$tickets=mysql_query(
			"select * from tickets where id='$ticketId'"
			);
		$ticket=mysql_fetch_assoc($tickets);
		if(!$ticket) {
			error_log("GetUserID: INVALID TICKET",0);
			die();
		}
		return $ticket["userid"];
	}
	
?>
