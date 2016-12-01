<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>delete account</title>
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
			<h3>delete account</h3>
			<?php
				require_once("../core/client.php");
				require_once("../core/ticket.php");

				InitializeClient();

				$db=ConnectSQL();
				$ticket=GetClientValue("ticket");
				$canEdit=VerifyTicket($db,$ticket,"edit_account");
				$userid=GetUserId($db,$ticket);
			?>
			<?php if($canEdit): ?>
				<form action="./deleteuser.php" method="post">
					<table>
						<tr><td>userid:</td><td><?php print($userid); ?></td></tr>
						<tr><td>username:</td><td><input type="text" name="username"></td></tr>
						<tr><td>password:</td><td><input type="password" name="password"></td></tr>
						<tr><td></td><td><input type="submit" value="deleteuser"></td></tr>
					</table>
				</form>
			<?php endif; ?>
		</center>
		<a href="../index.php">戻る</a>
	</body>
</html>
