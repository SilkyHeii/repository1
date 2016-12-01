<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>edit event</title>
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
		<h3>edit event</h3>
		<?php
			require_once("../core/db.php");
			require_once("../core/ticket.php");
			require_once("../core/client.php");
			require_once("../core/schedule.php");

			InitializeClient();

			$db=ConnectSQL();
			$ticket=GetClientValue("ticket");
			$eventid=$_GET["eventid"];
			$event=GetEventFromId($db,$ticket,$eventid);
			$titlearray=array("遅刻","欠席","ミーティング","輪講","その他");
			$unitarray=array("全体","grid班","net班","web班","B4","M1","M2");

			$year=date("Y");
			$month=date("m");
			$fromdatetime=explode(" ",$event["fromdatetime"]);
			$fromdates=$fromdatetime[0];
			$fromtimes=$fromdatetime[1];
			$fromdate=explode("-",$fromdates);
			$fromyear=$fromdate[0];
			$frommonth=$fromdate[1];
			$fromday=$fromdate[2];
			$fromtime=explode(":",$fromtimes);
			$fromhour=$fromtime[0];
			$fromminite=$fromtime[1];

			$todatetime=explode(" ",$event["todatetime"]);
			$todates=$todatetime[0];
			$totimes=$todatetime[1];
			$todate=explode("-",$todates);
			$toyear=$todate[0];
			$tomonth=$todate[1];
			$today=$todate[2];
			$totime=explode(":",$totimes);
			$tohour=$totime[0];
			$tominite=$totime[1];

		?>
		登録した人の名前：<?php print(GetUserName($db,$event["userid"])); ?>
		<form name="editevent" action="seteventinfo.php?edit=true&eventid=<?php print($eventid); ?>" method="post">
			<div id="eventtitle">
				内容
				<br>
				<select id="title" name="menu" onChange="entryChange();">
					<?php
						foreach($titlearray as $t){
							if($event["title"]==$t){
								print("<option value='$t' selected='selected'>$t</option>");
							}else{
								print("<option value='$t'>$t</option>");
							}
						}
					?>
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
					entryChange();
				</script>
			</div>
			<div id="other" name="other" style="display:<?php if($event["title"]=="その他"){}else{print("none");} ?>;">
				内容を入力
				<br>
				<input type="text" name="otherbody" size="30" value="<?php print($event["otherbody"]); ?>">
			</div>

			<div id="unit" name="unit" style="display:<?php if($event["title"]=="ミーティング" || $event["title"]=="その他"){}else{print("none");} ?>;">
				イベント関係単位
				<br>
				<?php
					foreach($unitarray as $u){
							if($event["shareusers"]==$u){
								print("<label><input type='radio' name='eventunit' value='$u' checked>$u</label>");
							}else{
								print("<label><input type='radio' name='eventunit' value='$u' >$u</label>");
							}
						}
					//~ その他自作のグループをここに表示する
				?>
			</div>

			<div  id="start" name="start" style="display:<?php if($event["title"]=="ミーティング" || $event["title"]=="その他"){}else{print("none");} ?>;">
				開始時刻
				<br>
				<select id="startyear" name="startyear">
					<?php
						//~ 今年から±5年分を表示する
						$syear=$year-5;
						for($i=0;$i<10;$i++){
							if($syear==$fromyear){
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
							if($i==$frommonth){
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
							if($d==$fromday){
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
							if($i==$fromhour){
								print("<option value='$i' selected='selected'>$i</option>");
							}else{
								print("<option value='$i'>$i</option>");
							}
						}
					?>
				</select>
				:
				<select name="startminutes">
					<?php
						for($i=0;$i<60;$i++){
							if($i==$fromminite){
								print("<option value='$i' selected>$i</option>");
							}else{
								print("<option value='$i'>$i</option>");
							}
						}
					?>
				</select>
			</div>

			<div id="end" name="end" style="display:<?php if($event["title"]=="ミーティング" || $event["title"]=="その他"){}else{print("none");} ?>;">
				終了時刻
				<br>
				<select name="endyear">
					<?php
						//~ 今年から±5年分を表示する
						$syear=$year-5;
						for($i=0;$i<10;$i++){
							if($syear==$toyear){
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
							if($i==$tomonth){
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
							if($d==$today){
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
							if($i==$tohour){
								print("<option value='$i' selected>$i</option>");
							}else{
								print("<option value='$i'>$i</option>");
							}
						}
					?>
				</select>
				:
				<select name="endminutes">
					<?php
						for($i=0;$i<60;$i++){
							if($i==$tominite){
								print("<option value='$i' selected>$i</option>");
							}else{
								print("<option value='$i'>$i</option>");
							}
						}
					?>
				</select>
			</div>


			<div id="eventbody">
				詳細<br>
				<textarea id="body" name="detail" value="<?php print($event["detail"]); ?>"></textarea>
			</div>
			<div id="sendmailcheck">
				メール送信<br>
				<?php if($event["sendmail"]==send): ?>
					<input type="radio" name="sendmail" value="send" checked>送信する
					<input type="radio" name="sendmail" value="notsend">送信しない
				<?php else : ?>
					<input type="radio" name="sendmail" value="send">送信する
					<input type="radio" name="sendmail" value="notsend" checked>送信しない
				<?php endif;?>
			</div>
			<div id="registereventbutton">
				<input type="submit"  value="登録する" onClick="nullcheck();">
			</div>

		</form>
		<script type="text/javascript">
			//ここでヌルチェックがしたい
			function nullcheck(){
				//alert("submitが押されたのでヌルチェックしますよー");
			}
		</script>
		<a href="../index.php">戻る</a>  ,  <input type="button" value="消去" onClick="deletecheck();">
		<script type=text/javascript>
			function deletecheck(){
				if(window.confirm('本当に削除してもよろしいですか？')){
					location.href="deleteevent.php?eventid=<?php print($eventid); ?>";
				}else{
					window.alert('キャンセルされました');
					location.href="../index.php"
				}
			}
		</script>
	</body>
</html>
