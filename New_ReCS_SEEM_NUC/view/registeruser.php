<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
		<!-- ※デフォルトのスタイル（style.css） -->
		<link rel="stylesheet" media="all" type="text/css" href="../assets/css/style.css" />
		<!-- ※タブレット用のスタイル（tablet.css） -->
		<link rel="stylesheet" media="all" type="text/css" href="../assets/css/tablet.css" />
		<!-- ※スマートフォン用のスタイル（smart.css） -->
		<link rel="stylesheet" media="all" type="text/css" href="../assets/css/smart.css" />

		<!-- BootStrapの設定 -->
		<link rel="stylesheet" href="../lib/bootstrap-3.3.7-dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="../lib/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="../lib/bootstrap-3.3.7-dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</head>
<?php
	require_once("../core/client.php");
	require_once("../core/ticket.php");
	require_once("../core/users.php");
	InitializeClient();

	$username=$_POST["username"];
	$password0=$_POST["password0"];
	$password1=$_POST["password1"];


	if(strcmp($username,"")==0){
		print("<center>ERROR : empty user name</center>");
		die();
	}

	if(strcmp($password0,$password1)!=0){
		print("<center>ERROR : empty password</center>");
		die();
	}


	$db=ConnectSQL();
	$userId = RegisterUser($db,$username,$password0);

	print("<center>register info</center>");
	print("<center><table><tr><td>userid:</td><td><font color=red>$userId</font></td></tr></center>");
	print("<tr><td>username:</td><td><font color=red>");
	print(htmlspecialchars($username)."</font></td></td></tr></table></center>");
	print("<br><a href=\"../index.php\">return to login page</a>");


	$ticket=PublishTicket(
		$db,$userId,$username,$password0,3,
		array("login","edit_account","read_account","edit_group","read_group","listen","talk")
	);
	SetClientValue("ticket",$ticket);
	SetClientValue("userid",$userId);
	SetClientValue("username",$username);
	print("<br><a href='../index.php'>戻る</a>");

?>
</html>
