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
//~ 登録されたアカウント情報の通知に関する処理のソース
	require_once("../core/client.php");
	require_once("../core/groups.php");
	require_once("../core/ticket.php");

	InitializeClient();

	$db=ConnectSQL();
	$ticket=GetClientValue("ticket");

	if(!(VerifyTicket($db,$ticket,"edit_group") && VerifyTicket($db,$ticket,"edit_account"))){
		print("ERROR: not allowed operation");
		die();
	}

	$displayGroupCount=GetClientValue("displayGroupCount");
	$displayPasswordGroupCount=GetClientValue("displayPassWordGroupCount");

	$userid=GetUserId($db,$ticket);
	$username=$_PSOT["username"];

	$address=$_POST["address"];

	$usergroupCheckBoxNamePrefix="userSelectedGroups_";
	$usergroups = array();
	for($it=0;$it<$displayGroupCount;$it++){
		$checkboxname="${usergroupCheckBoxNamePrefix}${it}";
		if(!isset($_POST[$checkboxname]))continue;
		$groupid=$_POST[$checkboxname];
		if(strcmp("",$groupid)==0)continue;
		$usergroups []=$groupid;
	}

	$userPassGroupCheckBoxNamePrefix="userSelectPassGroups_";
	$userPassGroupPassBoxNamePrefix="userSelectedPass_";
	$userPrevBelongsGroups=GetUserGroups($db,$userid);
	$userAuthorizeMissGroup=array();
	for($it=0;$it<$displayPasswordGroupCount;$it++){
		$checkboxname="${userPassGroupCheckBoxNamePrefix}${it}";
		$passboxname="${userPassGroupPassBoxNamePrefix}${it}";
		if(!isset($_POST[$checkboxname]))continue;
		$groupid=$_POST[$checkboxname];
		$grouppassword=$_POST[$passboxname];
		if(strcmp("",$groupid)!=0
			&&
			(GroupPassWordAuthorize($db,$groupid,$grouppassword,$ticket)
			||
			in_array($group,$userPrevBelongsGroups)
			)
			){
			$usergroups []=$groupid;
		}else{
			$userAuthorizeMissGroup []=$groupid;
		}
	}

	SetUserName($db,$userid,$username);
	SetUserGroups($db,$userid,$usergroups);
	SerUserAddress($db,$userid,$address);

	unset($_COOKIE["displayGroupCount"]);
	unset($_COOKIE["displayPasswordGroupCount"]);

	print("
		<center>
			<table>
				<tr>
					<td>user id:</td>
					<td>$userid</td>
				</tr>
				<tr>
					<td>username:</td>
					<td>".htmlspecialchars($username)."</td>
				</tr>
				<tr>
					<td>groups</td>
					<td></td>
				</tr>
				");
	foreach($usergroups as $usergroup){
		print("<tr>
					<td>[$usergroup]</td>
					<td>".htmlspecialchars(GetGroupName($db,$usergroup,$ticket))."</td>
				</tr>
		");
	}
	foreach($userAuthorizeMissGroup as $usergroup){
		print("
				<tr>
					<td>authorize error[$usergroup]</td>
					<td>".htmlspecialchars(GetGroupName($db,$usergroup,$ticket))."</td>
				</tr>
		");
	}
	print("
			</table>
		</center>
	"):
	print("<br><a href='../index.php'>戻る</a>");
?>
</html>
