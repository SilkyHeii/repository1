<?php
	namespace Wrench\Application;

	use Wrench\Application\Application;
	use Wrench\Application\NamedApplication;

	require("../core/talks.php");
	

	
	class ChatApp extends Application {
		
		private $loginusers = array();
		
		public function onDisconnect($connection){
			$this->Logout($connection);
		}
		public function onConnect($connection){
		}
		public function onData($data,$connection){
			$msg=json_decode($data);
			$ticket=$msg->{"ticket"};
			$action=$msg->{"action"};
			$message=$msg->{"message"};
			$receivers=$msg->{"receiveruserids"};
			$receivergroups=$msg->{"receivergroupids"};
			$userid=$msg->{"userid"};
			switch($action) {
				case "login": $this->Login($connection,$userid,$ticket); break;
				case "talk": $this->Talk($connection,$ticket,$message,$receivers,$receivergroups); break;
			}
		}
		public function Talk($connection,$ticket,$talk,$receivers,$receivergroups) {
			$db=ConnectSQL();
			if(!(
				VerifyTicket($db,$ticket,"talk") && 
				VerifyTicket($db,$ticket,"listen") 
				)) {
				$connection->send(
					json_encode(
						array(
							"action"=>"error",
							"message"=>"authorize error"
							)
							)
							);
				DisposeSQL($db);
				return;
			}
			$talkid=TalkToGroupsAndUsers($db,$receivergroups,$receivers,$talk,$ticket);
			$talk=GetTalk($db,$talkid,$ticket);
			
			$senderid=GetUserID($db,$ticket);
			$sendername=GetUserName($db,$senderid);
			
			$users=array();
			foreach(GetAllUsers($db) as $user) {
				$users[GetUserIDFrom($user)] = GetUserNameFrom($user);
			}
			
			$msg=array(
				"action"=>"talk",
				"talk"=>$talk,
				"users"=>$users
				);
			$json=json_encode($msg);
			
			$groupreceivers=array();
			foreach($receivergroups as $groupid) {
				foreach(GetUsers($db,$groupid,$ticket) as $groupuser) {
					$groupreceivers []= GetUserIDFrom($groupuser);
				}
			}
			
			foreach($this->loginusers as $it) {
				$userid=$it["userid"];
				if(	in_array($userid,$receivers) ||
					in_array($userid,$groupreceivers) ||
					$it["userid"]==$senderid){
						
					$it["connection"]->send($json);
				}
			}
			
		}
		public function Logout($connection) {
			$db=ConnectSQL();
			$userid=NULL;
			$username=NULL;
			foreach($this->loginusers as $index=>$it) {
				if($it["connection"] == $connection) {
					$userid=$it["userid"];
					$username=$it["username"];
					unset($this->loginusers[$index]);
					break;
				}
			}
			$this->loginusers = array_values($this->loginusers);
			if(!($userid!=NULL&&$username!=NULL)) {
				print("#Logout:invalid user logout\n");
				return;
			}
			$msg=array(
				"action"=>"logout",
				"userid"=>$userid,
				"username"=>$username
				);
			$json=json_encode($msg);
			foreach($this->loginusers as $it) {
				$it["connection"]->send($json);
			}
		}
		public function Login($connection,$userid,$ticket) {
			
			$db=ConnectSQL();
			if(!VerifyTicket($db,$ticket,"login")) {
				$connection->send(
					json_encode(
						array(
							"action"=>"error",
							"message"=>"authorize error"
							)
							)
							);
				DisposeSQL($db);
				return;
			}
			
			$username=GetUserName($db,$userid);
			$loginuser=array(
				"connection"=>$connection,
				"userid"=>$userid,
				"username"=>$username
				);
			$msg=array(
				"action"=>"login",
				"userid"=>$userid,
				"username"=>$username
				);
			$json=json_encode($msg);
			foreach($this->loginusers as $it) {
				$it["connection"]->send($json);
				$mymsg=array(
					"action"=>"login",
					"userid"=>$it["userid"],
					"username"=>GetUserName($db,$it["userid"])
					);
				$connection->send(json_encode($mymsg));
			}
			$this->loginusers []= $loginuser;
			DisposeSQL($db);
		}
		
	}
	
?>
