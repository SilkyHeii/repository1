<!DOCTYPE>
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
	require_once("../core/groups.php");
	require_once("../core/ticket.php");
	require_once("../core/db.php");

	InitializeClient();

	$db=ConnectSQL();
	$ticket=GetClientValue("ticket");
	if(!VerifyTicket($db,$ticket,"edit_group")){
		print("ERROR: not allowed operation");
		die();
	}
	$userid=GetUserId($db,$ticket);
	$name=$_POST["groupname"];
	$desc=$_POST["groupdescription"];
	$pass0=$_POST["grouppassword0"];
	$pass1=$_POST["grouppassword1"];
	if(strcmp($pass0,$pass1)!=0){
		print("ERROR: wrong pass");
		die();
	}
	$groupid=RegisterGroup($db,$name,$desc,$pass0,$ticket);
	SetGroupOwners($db,$groupid,array($userid),$ticket);
	print("
		<center>
			<table>
				<tr>
					<td>group id:</td>
					<td>$groupid</td>
				</tr>
				<tr>
					<td>name:</td>
					<td>".htmlspecialchars($name)."</td>
				</tr>
				<tr>
					<td>descripion:</td>
					<td>".htmlspecialchars($desc)."</td>
				</tr>
			</table>
		</center>
	");
	print("<br><a href='../index.php'>戻る</a>");
?>
</html>
