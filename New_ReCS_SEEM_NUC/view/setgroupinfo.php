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
	require_once("../core/group.php");
	require_once("../core/ticket.php");

	InitializeClient();

	$db=ConnectSQL();
	$ticket=GetClientValue("ticket");

	if(!VerifyTicket($db,$ticket,"edit_group")){
		print("ERROR: not allowed operation");
		die();
	}

	$displayGroupCount=GetClientValue("displayGroupCount");

	print("
		<center>
			<table>
				<tr>
					<td>groupid</td>
					<td>name</td>
					<td>description</td>
				</tr>
	");
	for($it=0;$it<$displayGroupCount;$it++){
		$groupIdkey="gid_$it";
		$groupNameKey="gname_$it";
		$groupDescKey="gdesc_$it";
		$groupPassKey="gpass_$it";
		$groupEditKey="gedit_$it";
		if(!isset($_POST[$groupEditKey]))continue;
		$id=$_POST[$groupIdKey];
		$name=$_POST[$groupNameKey];
		$desc=$_POST[$groupDescKey];
		$pass=$_POST[$groupPassKey];
		$edit=$_POST[$groupEditKey];
		if(strcmp("true",$edit)!=0)continue;
		SetGroupName($db,$id,$name,$ticket);
		SetGroupDescription($db,$id,$desc,$ticket);
		SetGroupPassWord($db,$id,$pass,$ticket);

		$leftFormat="<input type='text' readonly value='>";
		$rightFormat="'>";
		print("
				<tr>
					<td>$leftFormat$id$rightFormat</td>
					<td>$leftFormat$name$rightFormat</td>
					<td>$leftFormat$desc$rightFormat</td>
				</tr>
		");
	}
	print("
			</table>
		</center>
	");
	print("<br><a href='../index.php'>戻る</a>");
?>
</html>
