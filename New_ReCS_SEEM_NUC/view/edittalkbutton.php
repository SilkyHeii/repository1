<html>
<!--
テンプレートボタン編集に関わるページについてのソース
-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>edit talk button</title>
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
			<h3>edit talk button</h3>
			<form action="registertalkbutton.php" method="post">
				<table>
					<tr>
						<td>talk button message:</td>
						<td><input type="text" name="talkbutton"></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="register"></td>
					</tr>
				</table>
			</form>
			<h3>your button</h3>
			<form action="deletetalkbutton.php" method="post">
				<table>
					<tr>
						<td>id</td>
						<td>publish</td>
						<td>message</td>
						<td>delete</td>
					</tr>
					<?php
						require_once("../core/db.php");
						require_once("../core/client.php");
						require_once("../core/buttons.php");
						require_once("../core/users.php");


						InitializeClient();

						$ticket=GetClientValue("ticket");
						$db=ConnectSQL();
						$userid=GetUserId($db,$ticket);
						$buttons=GetUserTalkButtons($db,$userid);
						$it=0;
						foreach($buttons as $button){
							$publish=GetTalkButtonPublishDateTimeFrom($button);
							$message=GetTalkButtonMessageFrom($button);
							$buttonid=GetTalkButtonIdFrom($button);
							$tbid="tbid_$it";
							$tbdel="tbdel_$it";
							print("
								<tr>
									<td><input type='text' readonly name='$tbid' value='$buttonid'></td>
									<td>$publish</td>
									<td>".htmlspecialchars($message)."</td>
									<td><input type='checkbox' value='true' name='$tbdel'></td>
								</tr>
							");
							$it++;
						}
						SetClientValue("tbsize",$it);
					?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><input type="submit" value="delete"></td>
					</tr>
				</table>
			</form>
		</center>
		<a href="../index.php">戻る</a>
	</body>
</html>
