<?php
	require_once("db.php");
	require_once("ticket.php");
	

	function RegisterSchedule($db,$ticketId,$userid,$title,$detail,$fromDateTime,$toDateTime,$shareUsers,$otherbody,$sendmail){
		if(!$db){
			error_log("REGISTERSCHEDULE:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("REGISTERSCHEDULE:DB SELECT ERROR",0);
			die();
		}
		if(!VerifyTicket($db,$ticketId,"edit_schedule")){
			error_log("RegisterSchedule:Authorize ERROR",0);
			die();
		}
		//~ 
		$eventid=GetEnvironment($db,"noevent");
		mysql_query(
			"insert into schedules values($eventid,$userid,'$title','$detail','$fromDateTime','$toDateTime','$shareUsers','$otherbody',$sendmail)"
		);
		SetEnvironment($db,"noevent",$eventid+1);
		
	}
	function DeleteSchedule($db,$ticketId,$eventId){
		if(!$db){
			error_log("",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("",0);
			die();
		}
		if(!VerifyTicket($db,$ticketId,"edit_schedule")){
			error_log(":Authorize ERROR",0);
			die();
		}
		//~
		mysql_query(
			"delete from schedules where id=$eventId"
		);
		
		
		 
	}
	function EditSchedule($db,$ticketId,$eventid,$userid,$title,$detail,$fromDateTime,$toDateTime,$shareUsers,$otherbody,$sendmail){
		if(!$db){
			error_log("",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("",0);
			die();
		}
		if(!VerifyTicket($db,$ticketId,"edit_schedule")){
			error_log(":Authorize ERROR",0);
			die();
		}
		//~ 
		mysql_query(
			"Update schedules set title=$title , detail=$detail , fromdatetime=$fromDateTime , todatetime=$toDateTime , shareusers=$shareUsers , otherbody=$otherbody , sendmail=$sendmail where id=$eventId"
		);
		
		
	}
	function GetEventFromId($db,$ticketId,$eventId){
		if(!$db){
			error_log("GETMYSCHEDULES:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GETMYSCHEDULES:DB SELECT ERROR",0);
			die();
		}
		if(!VerifyTicket($db,$ticketId,"read_schedule")){
			error_log("GETMYSCHEDULES:Authorize ERROR",0);
			die();
		}
		//~ 
		$events=mysql_query(
			"select * from schedules where id=$eventId"
		);
		
		$event=mysql_fetch_assoc($events);
		
		if(!$events){
			error_log("GETEVENT:INVALID EVENT",0);
			die();
		}
		return $event;
	}
	function GetEventFromYM($db,$ticketId,$year,$month){
		//~ yearとmonthを受け取ってfromdatetimeがその年月にあるものを返す
		if(!$db){
			error_log("GETEVENTFROMYM:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GETEVENTFROMYM:DB SELECT ERROR",0);
			die();
		}
		if(!VerifyTicket($db,$ticketId,"read_schedule")){
			error_log("GETEVENTFROMYM:Authorize ERROR",0);
			die();
		}
		$events=mysql_query(
			"select * from schedules where fromdatetime like '".$year."-".$month."%'"
		);
		$result = mysql_fetch_assoc($events);
		while($event = mysql_fetch_assoc($events)){
			$result []= $event;
		}
		return $result;
	}
	function GetEventIdFrom($event){
		return $event["id"];
	}
	function GetEventTitleFrom($event){
		return $event["title"];
	}
	function GetEventBodyFrom($event){
		return $event["body"];
	}
	function GetEventFromDateTimeFrom($event){
		return $event["fromdatetime"];
	}
	function GetEventToDateTimeFrom($event){
		return $event["todatetime"];
	}
	function GetEventShareUsersFrom($event){
		return $event["shareusers"];
	}
	function ShowCalendar($year,$month){
		//~カレンダーを表示する処理を書く 
		print("<table id='calendar-header'>
					<tr>
						<th colspan='2' ><<(($month-1-1+12)%12+1)月</th>
						<th colspan='3' >".$month."月".$year年."</th>
						<th colspan='2' >($month-1+1)%12+1月>></th>
					</tr>
				</table>
				<table id='calendar-body'>
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
		");
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
								contents
							</div>
					</td>"
				);
				}else{
					print(
						"<td>
							<a href='view/registerevent.php?year=$year&month=$month&day=$day'>".$day."</a>
							<div name=".$year."/".$month."/".$day.">
								contents
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
		print(
			"</tr>
		</table>"
		);
	}

?>
