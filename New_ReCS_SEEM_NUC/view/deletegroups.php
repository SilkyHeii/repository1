<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>delete group</title>
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
			<h3>delete group</h3>
			<?php
				require_once("../core/client.php");
				require_once("../core/groups.php");
				InitializeClient();
				$db=ConnectSQL();
				$ticket=GetClientValue("ticket");
				//~ チケットを確認し、権限があるか確認する
				$canEdit=VerifyTicket($db,$ticket,"edit_group");
				$userid=GetUserId($db,$ticket);
				$ownerGroups=GetUserOwnerGroup($db,$userid);
			?>
<!--
			権限があれば消す。
-->
			<?php if($canEdit): ?>
				<form action="deleteregistergroups.php">
					<table>
						<tr>
							<td>group id</td>
							<td>group name</td>
							<td>description</td>
							<td>unregister</td>
						</tr>
						<?php
							$displayGroupCount=0;
							foreach($ownerGroups as $group){
								$gname=GetGroupName($db,$group,$ticket);
								$gdesc=GetGroupDescription($db,$group,$ticket);
								$groupIdKey="gid_$displayGroupCount";
								$groupNameKey="gname_$displayGroupCount";
								$groupDescKey="gdesc_$displayGroupCount";
								$groupPassKey="gpass_$displayGroupCount";
								$groupEditKey="gedit_$displayGroupCount";

								print("<tr><td>");
								print("<input type='text' name='$groupIdKey' readonly value='$group'></td><td>");
								print("<input type='text' name='$groupNameKey' readonly value='$gname'></td><td>");
								print("<input type='text' name='$groupDescKey' readonly value='$gdesc'></td>");
								print("<td><input type='checkbox' name='$groupEditKey' value='true'></td></tr>");

								$displayGroupCount++;
							}
							SetClientValue("displayGroupCount",$displayGroupCount);
						?>
						<tr><td></td><td></td><td></td><td></td><td><input type="submit" value="unregister"></td></tr>
					</table>
				</form>
			<?php endif; ?>
		</center>
		<a href="../index.php">戻る</a>
	</body>
</html>
