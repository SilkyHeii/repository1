<html>
	<head>
		<title>
			NewReCSverwithPHP
		</title>
		<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
		<!-- ※デフォルトのスタイル（style.css） -->
		<link rel="stylesheet" media="all" type="text/css" href="./assets/css/pc.css" />
		<!-- ※タブレット用のスタイル（tablet.css） -->
		<link rel="stylesheet" media="all" type="text/css" href="./assets/css/tablet.css" />
		<!-- ※スマートフォン用のスタイル（smart.css） -->
		<link rel="stylesheet" media="all" type="text/css" href="./assets/css/smart.css" />

		<!-- BootStrapの設定 -->
		<link rel="stylesheet" href="./lib/bootstrap-3.3.7-dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="./lib/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="./lib/bootstrap-3.3.7-dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	</head>
	<script src="assets/javascript/cookie.js"></script>
	<script src="assets/javascript/htmlEscape.js"></script>

	<link rel="stylesheet" type="text/css" href="assets/css/index.css">
	<body>
		<h3>New ReCS/SEEM written by PHP</h3>


		 <?php
			//~　ログインするユーザの情報を取得しウェブソケット導通に必要な情報を取得する。
			require_once("core/db.php");
			require_once("core/ticket.php");
			require_once("core/client.php");
			require_once("core/talks.php");
			require_once("core/buttons.php");
			require_once("core/server.php");
			require_once("core/users.php");
			require_once("core/groups.php");
			require_once("core/attend.php");
			require_once("core/authorize.php");
			require_once("core/envs.php");
			require_once("core/mailer.php");
			require_once("core/schedule.php");
			require_once("core/talks.php");



			InitializeClient();
			$db=ConnectSQL();
			$ticket=GetClientValue("ticket");
			$serverConfig=GetServerConf($db);
			$serverIpaddress=GetServerIpAddressFrom($serverConfig);
			$serverWebSocketPort=GetWebSocketPortFrom($serverConfig);
		?>
	<!--
		ログイン可能か？ー＞チケット認証
	-->
		<?php if(VerifyTicket($db,$ticket,"login")): ?>
	<!--
		ログイン可能な奴はここで処理される
	-->
			<form action="view/logout.php" method="post">
				welcom to New ReCS/SEEM
				<?php
					$userid=GetUserID($db,$ticket);
					$username=GetUserName($db,$userid);
					print("[$userid] $username");
				?>
				<input type="submit" id="logoutbutton" value="log out">
			</form>
		<?php else: ?>
	<!--
		チケット認証でログインの権利がなかった奴はこっちで処理する
	-->
		<form action="view/login.php" method="post">
			userid:<input type="text" name="userid"><br>
			username:<input type="text" name="username"><br>
			password:<input type="password" name="password"><br>
			<a href="view/newaccount.php" >new account</a>
			<br>
			<input type="submit" value="log in">
		</form>
		<?php endif; ?>

<!--
		editaccountの権限があるかないかで処理を分岐
-->
		<?php if(VerifyTicket($db,$ticket,"edit_account")): ?>
			<div>
				<a href="view/editaccount.php">edit account</a> /
				<a href="view/deleteaccount.php">delete account</a> /
				<a href="view/edittalkbutton.php">edit talk button</a>
			</div>
		<?php endif; ?>

<!--
		editgroupの権限があるか無いかで処理を分岐
-->
		<?php if(VerifyTicket($db,$ticket,"edit_group")):  ?>
			<div>
				<a href="view/editgroup.php">edit group</a> /
				<a href="view/newgroup.php">new group</a> /
				<a href="view/deletegroups.php">delete group</a>
			</div>
		<?php endif; ?>

		<?php if(VerifyTicket($db,$ticket,"manage")): ?>
			<div>
				<a href="view/manageaccount.php">manage account</a> /
			</div>
		<?php endif; ?>

		<?php
			//~ 様々な権限の認証確認作業
			$canLogin=VerifyTicket($db,$ticket,"login");
			$canRead=VerifyTicket($db,$ticket,"read_account");
			$canListen=VerifyTicket($db,$ticket,"listen");
			$canTalk=VerifyTicket($db,$ticket,"talk");
			$canReadEvent=VerifyTicket($db,$ticket,"read_schedule");
		?>


		<form name="partsselect">
			<select name="parts">
				<option value="calendar">カレンダー</option>
				<option value="chat">チャット</option>
				<option value="WB">ホワイトボード</option>
			</select>
			<input type="button" value="追加" onclick="hyoji1(0)">
			<input type="button" value="削除" onclick="hyoji1(1)">
			<input type="button" value="callIndex" onclick="callIndex()">
		</form>

<div id="columns" class="row" >
	<div id="calendar" class="column col-lg-6 container-fluid" draggable="true">
		<?php if($canReadEvent): ?>
			<br>
			<!--
						以下にスケジュールを表示
			-->
			<div id="calendar-view">
				<?php
					//~ 現在の年月を取得する
					$year=date("Y");
					$month=date("n");
					$today=date("j");
					$m=date("m");
					//~ イベントを取得する
					if($canReadEvent){
						$events=GetEventFromYM($db,$ticket,$year,$m);
					}
				?>
				<table id="calendarbody" class="table table-bordered table-striped">
					<tr>
						<?php if($month==1): ?>
							<th colspan="2" ><?php print("<a href='createindex.php?year=".($year-1)."&month=".(($month-1-1+12)%12+1)."'>") ?><<<?php print(($month-1-1+12)%12+1); ?>月</a></th>
							<th colspan="3" ><?php print($month); ?>月<?php print($year); ?>年</th>
							<th colspan="2" ><?php print("<a href='createindex.php?year=$year&month=".(($month-1+1)%12+1)."'>") ?><?php print(($month-1+1)%12+1); ?>月>></a></th>
						<?php elseif($month==12): ?>
							<th colspan="2" ><?php print("<a href='createindex.php?year=$year&month=".(($month-1-1+12)%12+1)."'>") ?><<<?php print(($month-1-1+12)%12+1); ?>月</a></th>
							<th colspan="3" ><?php print($month); ?>月<?php print($year); ?>年</th>
							<th colspan="2" ><?php print("<a href='createindex.php?year=".($year+1)."&month=".(($month-1+1)%12+1)."'>") ?><?php print(($month-1+1)%12+1); ?>月>></a></th>
						<?php else: ?>
							<th colspan="2" ><?php print("<a href='createindex.php?year=$year&month=".(($month-1-1+12)%12+1)."'>") ?><<<?php print(($month-1-1+12)%12+1); ?>月</a></th>
							<th colspan="3" ><?php print($month); ?>月<?php print($year); ?>年</th>
							<th colspan="2" ><?php print("<a href='createindex.php?year=$year&month=".(($month-1+1)%12+1)."'>") ?><?php print(($month-1+1)%12+1); ?>月>></a></th>
						<?php endif; ?>
					</tr>
					<tr>
						<th>日</th>
						<th>月</th>
						<th>火</th>
						<th>水</th>
						<th>木</th>
						<th>金</th>
						<th>土</th>
					</tr>
					<tr>
						<?php
							//~ カレンダーの中身を表示する処理
							//~ $year = 2016;
							//~ $month = 6;
							$day = 1;

							//~ 最初の曜日によって表示位置をずらす処理
							$wd1=date("w",mktime(0,0,0,$month,1,$year));
							for($i=0;$i<$wd1;$i++){
								print("<td>  </td>");
							}
							//~ カレンダー中身表示
							while(checkdate($month,$day,$year)){
								if($day==$today){
									print("
										<td class='today'><a href='view/registerevent.php?year=$year&month=$month&day=$day'>".$day."</a>
											<div name=".$year."/".$month."/".$day.">
											"
									);
									//~ カレンダーのcontentsを表示する処理をここに挿入する
									print(
											"<table>
												<tr>
									");
									foreach($events as $event){
										$eventfromdate=explode(" ",$event["fromdatetime"]);
										if($eventfromdate[0]==$year."-".$m."-".$day){
											$userid=$event["userid"];
											$username=GetUserName($db,$userid);
											$eventid=$event["id"];
											print(
													"<td>
														<a href='view/editevent.php?eventid=$eventid'>".$userid.":".$username.":".$event["title"]."</a>
													</td>"
											);
										}
									}
									print("
												</tr>
											</table>"
									);

									print("
											</div>
										</td>"
									);
								}else{
									print(
										"<td>
											<a href='view/registerevent.php?year=$year&month=$month&day=$day'>".$day."</a>
											<div name=".$year."/".$month."/".$day.">
										"
									);
									//~ contents
									print(
										"<table>
											<tr>
									");
									foreach($events as $event){
										$eventfromdate=explode(" ",$event["fromdatetime"]);
										if($eventfromdate[0]==$year."-".$m."-".$day){
											$userid=$event["userid"];
											$username=GetUserName($db,$userid);
											$eventid=$event["id"];
											print("
												<td>
													<div class='".$event["title"]."'>
														<a href='view/editevent.php?eventid=$eventid'>".$userid.":".$username.":".$event["title"]."</a>
													</div>
												</td>
											");
										}
									}
									print("
											</tr>
										</table>"
									);
									print("
											</div>
										</td>"
									);
								}
								if(date("w",mktime(0,0,0,$month,$day,$year))==6){
									print("</tr>");
									if(checkdate($month,$day+1,$year)){
										print("<tr>");
									}
								}
								$day++;
							}
							//~ 最後の週の土曜日まで表示
							$wdx=date("w",mktime(0,0,0,$month+1,0,$y));
							for($i=0;$i<7-$wdx;$i++){
								print("<td>  </td>");
							}
						?>
					</tr>
				</table>
			</div>
		</div>
		<?php endif; ?>

	<div id="chat" class="column col-lg-6 container-fluid" draggable="true" >
		<?php if($canTalk): ?>

			<br>
			<form id="receiverform">
				<div class="alt-table-responsive">
					<table class="table table-bordered" >
						<tr>
							<td>group</td>
							<td>sendto</td>
						</tr>
						<tr>
							<td>pass less</td>
							<td>
								<?php
									//~ パスが設定されていないグループの一覧を表示するための処理
									$passlessgroups=GetAllPassWordLessGroup($db,$ticket);
									foreach($passlessgroups as $group){
										$groupid=GetGroupIdFrom($group);
										$groupname=GetGroupNameFrom($group);
										$label="[$groupid]$groupname";
										print("$label<input type='checkbox' id='#G${groupid}_${groupname}'>");
									}
								?>
							</td>
						</tr>
						<tr>
							<td>with pass</td>
							<td>
								<?php
									//~ pass有りのグループの一覧を表示するための処理
									$passGroups=GetUserPasswordGroups($db,$userid);
									foreach($passGroups as $group){
										$groupid=GetGroupIdFrom($group);
										$groupname=GetGroupNameFrom($group);
										$label="[$groupid]$groupname";
										print("$label<input type='checkbox' id='#G${groupid}_${groupname}'>");
									}
								?>
							</td>
						</tr>

						<tr>
							<td>others</td>
							<td>sendto</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<?php
									//~ 宛先ユーザ一覧を表示するための処理
									$others=GetOtherUsers($db,$userid);
									foreach($others as $other){
										$otherid=GetUserIdFrom($other);
										$othername=GetUserNameFrom($other);
										$label="[$otherid]".htmlspecialchars($othername);
										print("$label<input type='checkbox' id='#U${otherid}_${othername}'>");
									}
								?>
							</td>
						</tr>
					</table>
				</div>
			</form>



<!--
			現在ログイン中のユーザを表示するための領域。随時動的に表示を変更する
-->
				login now
				<table id="loginuserstable">
					<tr>
					</tr>
				</table>
				<br>


				<form id="talkform" >
					<table >
						<tr>
							<td>talk :</td>
							<td><textarea id="talk" ></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="button" value="send" id="sendtalk" class="btn btn-primary"></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td>talk-button:</td>
							<td>
								<?php
									//~ print("この下にトークボタンが表示されるはずだよー");
									$talkbuttons=GetUserTalkButtons($db,$userid);
									foreach($talkbuttons as $talkbutton){
										$talkbuttonmessage=GetTalkButtonMessageFrom($talkbutton);
										$talkbuttonId=GetTalkButtonIdFrom($talkbutton);

										$script="(function(){
												document.forms.talkform.talk.value='$talkbuttonmessage';"
												.
												"document.forms.talkform.sendtalk.click();
											}
										)();";
										print("<input type='button' id='talkbutton_$talkbuttonId' class='btn btn-default' value='$talkbuttonmessage' onClick=\"$script\">");
									}
								?>
							</td>
						</tr>
					</table>
				</form>





<!--
			ウェブソケット導通に関する処理を行なっているjavascriptのソースを呼び出す
			出来ればここでwebsocketの操作に関するjavascriptを呼び出したかったなぁ。また今度やろう。
-->
		<script>
			(function() {
			//~ var mappedCookie = GetMappedCookie(document.cookie);
			var ticket = <?php print("'$ticket'"); ?>;
			var username = <?php print("'$username'"); ?>;
			var userid = <?php print("'$userid'"); ?>;
			var ws = new WebSocket(
				<?php
					//~ "ws://192.168.11.49:9000/talk"
					print("'ws://${serverIpaddress}:${serverWebSocketPort}/talk'");
				?>
			);
			ws.onerror=function(e) {
				alert("lose connection");
			};
			ws.onclose=function(e) {
				alert("lose connection");
			};
			ws.onopen=function(e) {
				var data = {};
				data["ticket"] = ticket;
				data["action"] = "login";
				data["userid"]=userid;
				data["message"] = "";
				data["receiveruserids"] = [];
				data["receivergroupids"]=[];
				var json = JSON.stringify(data);
				ws.send(json);
			};
			ws.onmessage=function(e) {
				var msg=JSON.parse(e.data);
				switch(msg["action"]) {

					case "login": {
						var userid=msg["userid"];
						var username=msg["username"];
						var row=document.getElementById("loginuserstable").rows.item(0);
						row.insertCell(0).innerHTML="["+userid+"]"+username;
						break;
					}

					case "logout": {
						var userid=msg["userid"];
						var username=msg["username"];
						var cells=document.getElementById("loginuserstable").rows.item(0).cells;
						for(var it=0;it<cells.length;it++){
							var item=cells[it].innerHTML;
							var temp=item.split("[")[1].split("]");
							var cUserid=temp[0];
							var cUsername=temp[1];
							if(cUserid==userid && cUsername==username) {
								document.getElementById("loginuserstable").rows.item(0).deleteCell(it);
								break;
							}
						}

						break;
					}

					case "error": {
						alert(msg["message"]);
						break;
					}

					case "talk": {
						var chattable=document.getElementById("chattable");
						var row=chattable.insertRow(1);
						var id=row.insertCell(0);
						id.align="left";
						var sender=row.insertCell(1);
						sender.align="right";
						var receivers=row.insertCell(2);
						receivers.align="right";
						var date=row.insertCell(3);
						date.align="right";
						var talkCell=row.insertCell(4);
						talkCell.align="right";
						var talk = msg["talk"];
						var users = msg["users"];
						var talkid=talk["id"];
						var senderid=talk["senduserid"];
						var receiverids=[];
						(function(){
						var temp=talk["receiveduserids"].split(",");
						for(var it=0;it<temp.length;it++){
							if(temp[it]=="")continue;
							receiverids.push(temp[it]);
							}
						})();
						var talkmsg=talk["talks"];
						var publish=talk["publish"];
						id.innerHTML=talkid;
						sender.innerHTML="["+senderid+"]"+users[senderid];
						(function(){
							var first=true;
							for(var it=0;it < receiverids.length;it++ ){
								var receiverid=receiverids[it];
								receivers.innerHTML+= (first?"":",")+"["+receiverid+"]"+escapeHtml(users[receiverid]);
								first=false;
							}
						})();
						date.innerHTML=publish;
						talkCell.innerHTML=escapeHtml(talkmsg);
						break;
					}
				}
			};
			(function(){
				document.getElementById("sendtalk").onclick=function() {
				var textArea=document.getElementById("talk");
				var talkmsg=textArea.value;
				textArea.value = "";
				if(talkmsg=="")return;
				var data = {};
				data["ticket"] = ticket;
				data["action"] = "talk";
				data["userid"] =userid;
				data["message"] = talkmsg;
				var receiverids=[];
				var receivergroupids=[];
				var receiverFormElements=document.forms.receiverform.elements;
				for(var it=0;it<receiverFormElements.length;it++){
					var item=receiverFormElements[it];
					if(!item.checked==true)continue;
					var itemID=item.id;
					if(itemID.substr(0,1) != "#") continue;
					switch(itemID.substr(1,1)) {

						case "U": {
								receiverids.push(itemID.substr(2,itemID.length-2).split("_")[0]);
							break;
						}

						case "G": {
							receivergroupids.push(itemID.substr(2,itemID.length-2).split("_")[0]);
							break;
						}
					}
				}
				data["receiveruserids"] = receiverids;
				data["receivergroupids"] = receivergroupids;
				var json=JSON.stringify(data);
				ws.send(json);
				};
			})();
		})();
		</script>
		<?php endif; ?>



<!--
		以下にチャットのトークを貼っつけていく領域を用意する。
-->
		<?php if($canListen): ?>
			<br>
			<div class="alt-table-responsive">
				<table id="chattable" class="table table-bordered">
					<tr>
						<td>id</td>
						<td>sender</td>
						<td>receivers</td>
						<td>date</td>
						<td>talk</td>
					</tr>
					<?php
						//~ データベースから過去のチャットログを取ってきて表示する。

						$talks=GetMyTalk($db,$ticket);

						foreach($talks as $talk){
							$talkid=GetTalkId($talk);
							$talksenduserid=GetSendUserId($talk);
							$talksendusername=GetUserName($db,$talksenduserid);
							$talkdate=GetPublishDateTimeFrom($talk);
							$talkmsg=GetTalkMessageFrom($talk);
							$talkreceiverids=GetReceiverUsersFrom($talk);
							$talkreceivernames=array();

							foreach($talkreceiverids as $talkreceiverid){
								$talkreceivername=GetUserName($db,$talkreceiverid);
								$talkreceivernames []=$talkreceivername;
							}

							$talkreceiversStr="";
							$talkreceiverSize=count($talkreceiverids);
							$first=true;
							for($it=0;$it<$talkreceiverSize;$it++){
								$talkreceiverid=$talkreceiverids[$it];
								$talkreceivername=$talkreceivernames[$it];
								$talkreceiversStr=$talkreceiversStr.($first?"":",")."[$talkreceiverid]".htmlspecialchars($talkreceivername);
								$first=false;
							}
							print("
								<tr>
									<td align='right'>$talkid</td>
									<td align='right'>[$talksenduserid]$talksendusername</td>
									<td align='right'>$talkreceiversStr</td>
									<td align='right'>$talkdate</td>
									<td align='right'>$talkmsg</td>
								</tr>
							");
						}
					?>
				</table>
			</div>
		<?php endif; ?>
	</div>

	<div id="WB" class="column container-fluid" draggable="true">
		aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
	</div>
</div>


		<?php if(!$canLogin): ?>
			welcome to new ReCS/SEEM. In this site , you can chatting with account.
		<?php endif; ?>


		<script src="assets/javascript/draggable.js"></script>
		<script type="text/javascript">
		function callIndex(){
			var calendarindex = $(".column").index($("#calendar"))
			var chatindex = $(".column").index($("#chat"))
			var WBindex = $(".column").index($("#WB"))
			//alert("calendarindex="+calendarindex);
			//alert("chatindex="+chatindex);
			//alert("WBindex="+WBindex);
		}
		</script>
		<script type="text/javascript">
		var placementIndexArray = new Array(3);
		function hyoji1(num)
		{
		  var partsname = document.partsselect.parts.value;
		  if (num == 0)
		  {
		    document.getElementById(partsname).style.display="block";
			var column = document.getElementsByClassName("column");
			[].forEach.call(column,function(col){
					col.style.width="33%";
			});

			if(document.getElementById("calendar").style.display != "none"){
				var calendarindex = $(".column").index($("#calendar"))
			}else{
				var calendarindex = null;
			}
			if(document.getElementById("chat").style.display != "none"){
				var chatindex = $(".column").index($("#chat"))
			}else{
				var chatindex=null;
			}
			if(document.getElementById("WB").style.display != "none"){
				var WBindex = $(".column").index($("#WB"))
			}else{
				var WBindex=null;
			}
			if(calendarindex!=null){placementIndexArray[calendarindex]="calendar";}
			if(chatindex!=null){placementIndexArray[chatindex]="chat";}
			if(WBindex!=null){placementIndexArray[WBindex]="WB";}
			//alert(placementIndexArray);

		  }
		  else
		  {
		    document.getElementById(partsname).style.display="none";
			var column = document.getElementsByClassName("column");
			[].forEach.call(column,function(col){
					col.style.width="50%";
			});
			if(document.getElementById("calendar").style.display != "none"){
				var calendarindex = $(".column").index($("#calendar"))
			}else{
				var calendarindex = null;
			}
			if(document.getElementById("chat").style.display != "none"){
				var chatindex = $(".column").index($("#chat"))
			}else{
				var chatindex=null;
			}
			if(document.getElementById("WB").style.display != "none"){
				var WBindex = $(".column").index($("#WB"))
			}else{
				var WBindex=null;
			}
			if(calendarindex!=null){placementIndexArray[calendarindex]="calendar";}
			if(chatindex!=null){placementIndexArray[chatindex]="chat";}
			if(WBindex!=null){placementIndexArray[WBindex]="WB";}
			//alert(placementIndexArray);

		  }
		}
		</script>
	</body>
</html>
