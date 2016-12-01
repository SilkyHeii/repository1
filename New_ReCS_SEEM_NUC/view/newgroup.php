<html>
<!--
	新しいグループ作成に関わるページのソース
-->
	<head>
		<title>new group</title>
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

	<body>
		<center>
			<h3>new group</h3>
			<?php
				require_once("../core/client.php");
				require_once("../core/groups.php");
				require_once("../core/ticket.php");
				require_once("../core/db.php");

				InitializeClient();

				$db=ConnectSQL();
				$ticket=GetClientValue("ticket");
				$canEdit=VerifyTicket($db,$ticket,"edit_group");
			?>
			<form action="registergroup.php" method="post">
				<table>
					<tr>
						<td>group name:</td>
						<td><input type="text" name="groupname"></td>
					</tr>
					<tr>
						<td>description</td>
						<td><input type="text" name="groupdescription"></td>
					</tr>
					<tr>
						<td>group password:</td>
						<td><input type="password" name="grouppassword0"></td>
					</tr>
					<tr>
						<td>group password(double check):</td>
						<td><input type="password" name="grouppassword1"></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="register"></td>
					</tr>
				</table>
			</form>
		</center>
		<a href="../index.php">戻る</a>
	</body>
</html>
