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

//~ トークボタンの削除に関わる処理を記述しているソース
	require_once("../core/db.php");
	require_once("../core/client.php");
	require_once("../core/buttons.php");
	require_once("../core/ticket.php");

	InitializeClient();

	$ticket=GetClientValue("ticket");
	$db=ConnectSQL();
	if(!VerifyTicket($db,$ticket,"edit_account")){
		print("ERROR: not allowed operation");
		die();
	}
	print("
		delete talk-buttons
		<br>
		<table>
			<tr>
				<td>id</td>
				<td>message</td>
			</tr>
		</table>
	");
	$tbsize=GetClientValue("tbsize");
	for($it=0;$it<$tbsize;$it++){
		if(!isset($_POST["tbdel_$it"])){
			continue;
		}
		$tbid=$_POST["tbid_$it"];
		$talkbutton=GetTalkButton($id,$ticket,$tbid);
		$msg=GetTalkButtonMessageFrom($talkbutton);
		print("
			<tr>
				<td>".$tbid."</td>
				<td>".htmlspecialchars($msg)."</td>
			</tr>
		");

		DeleteTalkButtons($db,$ticket,$tbid);
	}
	print("</table>");
	print("<br><a href='../index.php'>戻る</a>");
?>
</html>
