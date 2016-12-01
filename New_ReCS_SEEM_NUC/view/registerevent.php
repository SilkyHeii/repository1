<html>
<!--
	スケジュールにイベントを登録するためのページに関するソース
-->
	<?php
		require_once("../core/db.php");
		require_once("../core/users.php");
		require_once("../core/ticket.php");
		require_once("../core/client.php");

		InitializeClient();

		$db=ConnectSQL();
		$ticket=GetClientValue("ticket");
		if(!VerifyTicket($db,$ticket,"edit_schedule")){
			print("REGISTEREVENT: not allowed operation");
			die();
		}

		//~ 名前を取ってくる
		$userid=GetUserId($db,$ticket);
		$username=GetUserName($db,$userid);

		//~ 年月日受け取る
		$year=$_GET["year"];
		$month=$_GET["month"];
		$day=$_GET["day"];

	?>
	<head>
		<title>register event</title>
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
		<h3>register event</h3>
		名前：<?php print($username); ?>
		<form id="newevent" name="newevent" action="./seteventinfo.php" method="post">
			<div id="eventtitle">
				内容
				<br>
				<select id="title" name="menu" onChange="entryChange();">
					<option value="遅刻">遅刻</option>
					<option value="欠席">欠席</option>
					<option value="ミーティング">ミーティング</option>
					<option value="輪講">輪講</option>
					<option value="その他">その他</option>
				</select>
				<script type="text/javascript">
					function entryChange(){
						menu=document.getElementsByName("menu");

						if(menu[0].value=='その他'){
							document.getElementById('other').style.display="";
						}else{
							document.getElementById('other').style.display="none";
						}

						if(menu[0].value=='遅刻' || menu[0].value=='欠席'){
							document.getElementById('unit').style.display='none';
							document.getElementById('start').style.display='none';
							document.getElementById('end').style.display='none';
						}else{
							document.getElementById('unit').style.display='';
							document.getElementById('start').style.display='';
							document.getElementById('end').style.display='';
						}
					}
				</script>
			</div>
			<div id="other" name="other" style="display:none;">
				内容を入力
				<br>
				<input type="text" name="otherbody" size="30">
			</div>

			<div id="unit" name="unit" style="display:none;">
				イベント関係単位
				<br>
				<label><input type="radio" name="eventunit" value="全体" >全体</label>
				<label><input type="radio" name="eventunit" value="grid班" >grid班</label>
				<label><input type="radio" name="eventunit" value="net班" >net班</label>
				<label><input type="radio" name="eventunit" value="web班" >web班</label>
				<label><input type="radio" name="eventunit" value="B4" >B4</label>
				<label><input type="radio" name="eventunit" value="M1" >M1</label>
				<label><input type="radio" name="eventunit" value="M2" >M2</label>
				<?php
					//~ その他自作のグループをここに表示する
				?>
			</div>

			<div  id="start" name="start" style="display:none;">
				開始時刻
				<br>
				<select id="startyear" name="startyear">
					<?php
						//~ 今年から±5年分を表示する
						$syear=$year-5;
						for($i=0;$i<10;$i++){
							if($syear==$year){
								print("<option selected='selected' value='$syear'>$syear</option>");
							}else{
								print("<option value='$syear'>$syear</option>");
							}
							$syear++;
						}
					?>
				</select>
				<select id="startmonth" name="startmonth">
					<?php
						$month=$month+0;
						for($i=1;$i<=12;$i++){
							if($i==$month){
								print("<option selected='selected' value='$i'>".$i."月</option>");
							}else{
								print("<option value='$i'>".$i."月</option>");
							}
						}
					?>
				</select>
				<select name="startday">
					<?php
						$d=1;
						while(checkdate($month,$d,$year)){
							if($d==$day){
								print("<option selected='selected' value='$day'>".$d."日</option>");
							}else{
								print("<option value='$day'>".$d."日</option>");
							}
							$d++;
						}
					?>
				</select>
				-
				<select name="starthour">
					<?php
						for($i=0;$i<24;$i++){
							print("<option value='$i'>$i</option>");
						}
					?>
				</select>
				:
				<select name="startminutes">
					<?php
						for($i=0;$i<60;$i++){
							print("<option value='$i'>$i</option>");
						}
					?>
				</select>
			</div>

			<div id="end" name="end" style="display:none;">
				終了時刻
				<br>
				<select name="endyear">
					<?php
						//~ 今年から±5年分を表示する
						$syear=$year-5;
						for($i=0;$i<10;$i++){
							if($syear==$year){
								print("<option selected='selected' value='$syear'>$syear</option>");
							}
							print("<option value='$syear'>$syear</option>");

							$syear++;
						}
					?>
				</select>
				<select name="endmonth">
					<?php
						for($i=1;$i<=12;$i++){
							if($i==$month){
								print("<option selected='selected' value='$i'>".$i."月</option>");
							}
							print("<option value='$i'>".$i."月</option>");
						}
					?>
				</select>
				<select name="endday">
					<?php
						$d=1;
						while(checkdate($month,$d,$year)){
							if($d==$day){
								print("<option selected='selected' value='$day'>".$d."日</option>");
							}
							print("<option value='$day'>".$d."日</option>");
							$d++;
						}
					?>
				</select>
				-
				<select name="endhour">
					<?php
						for($i=0;$i<24;$i++){
							print("<option value='$i'>$i</option>");
						}
					?>
				</select>
				:
				<select name="endminutes">
					<?php
						for($i=0;$i<60;$i++){
							print("<option value='$i'>$i</option>");
						}
					?>
				</select>
			</div>


			<div id="eventbody">
				詳細<br>
				<textarea id="body" name="detail"></textarea>
			</div>
			<div id="sendmailcheck">
				メール送信<br>
				<input type="radio" name="sendmail" value="send">送信する
				<input type="radio" name="sendmail" value="notsend">送信しない
			</div>
			<div id="registereventbutton">
				<input type="submit"  value="登録する" onClick="nullcheck();">
			</div>
		</form>

		<script type="text/javascript">
		// ここでヌルチェックがしたい
			function nullcheck(){
				//alert("submitが押されたのでヌルチェックしますよー");
			}
		</script>
		<a href="../index.php">戻る</a>
	</body>
</html>
