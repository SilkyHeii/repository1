<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>delete event</title>
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
		<?php
			require_once("../core/db.php");
			require_once("../core/client.php");
			require_once("../core/schedule.php");
			require_once("../core/ticket.php");
			require_once("../core/users.php");

			InitializeClient();

			$db=ConnectSQL();
			$ticket=GetClientValue("ticket");
			$eventid=$_GET["eventid"];
			$event=GetEventFromId($db,$ticket,$eventid);
		?>
		<h3>delete event</h3>
			<br>
			削除されたイベント内容は以下のものです。
			<br>
			eventid:<?php print($event["id"]); ?>
			<br>
			registered user:<?php print(GetUserName($db,$event["userid"])); ?>
			<br>
			title:<?php print($event["title"]); ?>
			<br>
			otherbody:<?php print($event["otherbody"]); ?>
			<br>
			unit:<?php print($event["unit"]); ?>
			<br>
			fromdatetime:<?php print($event["fromdatetime"]); ?>
			<br>
			todatetime:<?php print($event["todatetime"]); ?>
			<br>
			detail:<?php print($event["detail"]); ?>
			<br>
			sendmail:<?php print($event["sendmail"]); ?>
			<br>
			<?php
				DeleteSchedule($db,$ticket,$eventid);
			?>
			<a href="../index.php">戻る</a>
	</body>
	</body>
</html>
