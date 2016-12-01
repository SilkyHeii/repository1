<?php
	require_once("../core/client.php");
	require_once("../core/ticket.php");
	require_once("../core/db.php");
	require_once("../core/users.php");
	require_once("../core/authorize.php");

	InitializeClient();

	$username=$_POST["username"];
	$userid=$_POST["userid"];
	$password=$_POST["password"];



	//~　ログインするユーザにチケットを発行。権利を与える。
	$db=ConnectSQL();
	$user=mysql_query(
		"select * from users where id=$userid"
	);
	$nowuser=mysql_fetch_assoc($user);
	if(GetUserTypeFrom($nowuser)=="manage"){
		$ticketId=PublishTicket(
				$db,$userid,$username,$password,3,
				array("login","edit_account","read_account","edit_group","read_group","listen","talk","edit_schedule","read_schedule","create_mail","manage")
			);
	}else{
		$ticketId=PublishTicket(
				$db,$userid,$username,$password,3,
				array("login","edit_account","read_account","edit_group","read_group","listen","talk","edit_schedule","read_schedule","create_mail")
			);
	}
	if($ticketId==NULL){
		DisposeSQL($db);
		header("Location: ../index.php");
		die();
	}

	$userid=GetUserId($db,$ticketId);
	$username=GetUserName($db,$userid);
	SetClientValue("ticket",$ticketId);
	SetClientValue("userid",$userid);
	SetClientValue("username",$username);
	DisposeSQL($db);
	header("Location: ../index.php");

?>
