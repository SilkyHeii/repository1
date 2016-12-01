<?php
	require_once("../core/client.php");
	require_once("../core/ticket.php");
	require_once("../core/db.php");
	require_once("../core/users.php");
	require_once("../core/groups.php");
	require_once("../core/schedule.php");
	require_once("../core/mailer.php");
	
	InitializeClient();
	
	$db=ConnectSQL();
	$ticket=GetClientValue("ticket");
	$editcheck=$_GET["edit"];
	$eventid=$_GET["eventid"];
	
	if(!(VerifyTicket($db,$ticket,"edit_schedule"))){
		print("ERROR: not allowed operation");
		die();
	}
	
	
	$userid=GetUserId($db,$ticket);
	
	$title=$_POST["menu"];
	$other=$_POST["otherbody"];
	$unit=$_POST["eventunit"];
	
	
	$startyear=$_POST["startyear"];
	$startmonth=$_POST["startmonth"];
	$startday=$_POST["startday"];
	$starthour=$_POST["starthour"];
	$startminutes=$_POST["startminutes"];
	$fromdatetime=$startyear."/".$startmonth."/".$startday."/".$starthour."/".$startminutes."/";
	
	
	$endyear=$_POST["endyear"];
	$endmonth=$_POST["endmonth"];
	$endday=$_POST["endday"];
	$endhour=$_POST["endhour"];
	$endminutes=$_POST["endminutes"];
	$todatetime=$endyear."/".$endmonth."/".$endday."/".$endhour."/".$endminutes."/";
	
	$detail=$_POST["detail"];
	if($_POST["sendmail"]=="send"){
		$sendmail="true";
		
	}else{
		$sendmail="false";
	}
	
	//~ sendmail=trueならメールを送信する
	if($sendmail=="true"){
		$destAddress="Al-Lab@cm.kansai-u.ac.jp";
		$fromAddress=GetUserMailAddress($db,$userid);
		SendMail($ticket,$userid,$destAddress,$fromAddress,$title,$detail);
	}
	
	//~ shareuserを処理
	if($unit==""){
		$shareusers="toAll";
	}else{
		$shareusers=$unit;
	}
	//~ ここでイベントを登録
	if($editcheck==true){
		EditSchedule($db,$ticket,$eventid,$userid,$title,$detail,$fromdatetime,$todatetime,$shareusers,$other,$sendmail);
	}else{
		RegisterSchedule($db,$ticket,$userid,$title,$detail,$fromdatetime,$todatetime,$shareusers,$other,$sendmail);
	}
	//~ print($userid);
	//~ print("<br>");
	//~ print($title);
	//~ print("<br>");
	//~ print($detail);
	//~ print("<br>");
	//~ print($fromdatetime);
	//~ print("<br>");
	//~ print($todatetime);
	//~ print("<br>");
	//~ print($shareusers);
	//~ print("<br>");
	//~ print($other);
	//~ print("<br>");
	//~ print($sendmail);
	//~ print("<br>");
	header("Location: ../index.php");
?>
