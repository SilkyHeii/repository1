<html>
<!--
	アカウント情報の編集に関するページに関するソース
-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>edit account</title>
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
			require_once("../core/client.php");
			require_once("../core/db.php");
			require_once("../core/groups.php");

			InitializeClient();

			$db=ConnectSQL();
			$ticket=GetClientValue("ticket");
			$canEdit=VerifyTicket($db,$ticket,"edit_account");
		?>
		<center>
			<h3>edit account</h3>
			<?php if(!$canEdit): ?>
				not allowed action
			<?php else : ?>
				<?php
					$userid=GetUserId($db,$ticket);
					$username=GetUserName($db,$userid);
					$usergroups=GetUserGroups($db,$userid);
					$passlessgroups=GetAllPassWordLessGroup($db,$ticket);
					$passwordgroups=GetAllPassWordGroup($db,$ticket);
				?>
				<form id="accountForm" action="setaccountinfo.php" method="post">
					<table>
						<tr>
							<td></td>
							<td>user id:</td>
							<td><?php print($userid); ?></td>
						</tr>
						<tr>
							<td></td>
							<td>username:</td>
							<td><input type="text" name="username" value=<?php print("'$username'"); ?>></td>
						</tr>
						<tr>
							<td></td>
							<td>user mail address:</td>
							<td><input type="text" name="address" size="50"></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>user groups:</td>
							<td></td>
							<td>password</td>
						</tr>
						<?php
							$displayGroupCount=0;
							foreach($passlessgroups as $group){
								$gid=$group["id"];
								$gname=$group["name"];
								$gdesc=$group["description"];
								$userBelongs=ContainsUserGroup($db,$userid,$gid);
								$checkboxName="userSelectedGroups_$displayGroupCount";
								$checkThis=($userBelongs)?"checked":"";

								print("
									<tr>
										<td>[$gid]$gname</td>
										<td>($gdesc)</td>
										<td><input type='checkbox' name='$checkboxName' value='$gid' $checkThis></td>
									</tr>
								");

								$displayGroupCount++;
							}
							SetClientValue("displayGroupCount",$displayGroupCount);

							$displayPasswordGroupCount=0;
							foreach($passwordgroups as $group){
								$gid=$group["id"];
								$gname=$group["name"];
								$gdesc=$group["description"];
								$userBelongs=ContainsUserGroup($db,$userid,$gid);
								$checkboxName="userSelectPassGroups_$displayPasswordGroupCount";
								$passwordBoxName="userSelectPass_$displayPasswordGroupCount";
								$checkThis=($userBelongs)?"Checked":"";

								print("
									<tr>
										<td>[$gid]$gname</td>
										<td>($gdesc)</td>
										<td><input type='password' name='$passworrdBoxName'><input type='checkbox' name='$checkboxName' value='$gid' $checkThis></td>
									</tr>
								");
								$displayPasswordGroupCount++;
							}
							SetClientValue("displayPasswordGroupCount",$displayPasswordGroupCount);
						?>
						<tr>
							<td></td>
							<td></td>
							<td><input type="submit" value="save"></td>
						</tr>
					</table>
				</form>
				<form action="setuserpass.php" method="post">
					<table>
						<tr>
							<td>current password</td>
							<td><input type="password" name="userpassword"></td>
						</tr>
						<tr>
							<td>new password</td>
							<td><input type="password" name="userpassword0"></td>
						</tr>
						<tr>
							<td>new password(double check)</td>
							<td><input type="password" name="userpassword1"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="change password"></td>
						</tr>
					</table>
				</form>
				<?php endif; ?>
		</center>
		<a href="../index.php">戻る</a>
	</body>
</html>
