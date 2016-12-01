<?php
	require_once("ticket.php");
	require_once("db.php");
	
	
	function attendCheck($db,$userid){
		//~ いまどこに登録されているかまた登録されていないかを確認する
		if(!$db){
				error_log("ATTENDCHECK:SQL ERROR",0);
				die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("ATTENDCHECK: DB SELECT ERROR",0);
			die();
		}
		
		$states=mysql_query(
			"select id from attend where userid=$userid"
		);
		$state=mysql_fetch_assoc($states);
		if($state["id"]!=""){
			return "attend";
		}
		
		$states=mysql_query(
			"select id from away where userid=$userid"
		);
		$state=mysql_fetch_assoc($states);
		if($state["id"]!=""){
			return "away";
		}
		
		$states=mysql_query(
			"select id from gohome where userid=$userid"
		);
		$state=mysql_fetch_assoc($states);
		if($state["id"]!=""){
			return "gohome";
		}
		
		return "absence";
		
	}
	
	function Attend($db,$userid,$attendtype){
		if(!$db){
			error_log("ATTEND:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("ATTEND: DB SELECT ERROR",0);
			die();
		}
		
		$state=attendCheck($db,$userid);
		if($state=="attend"){
			error_log("attendcheckerror: attending now",0);
			die();
		}
		
		//~ 出席に関する処理を行う
		$attendid=GetEnvironment($db,"noattend");
		$attendextension="";
		mysql_query(
			"insert into attend value($attendid,$userid,$attendtype,NOW(),$attendextension)"
		);
		mysql_query(
			"delete from $state where userid=$userid"
		);
		SetEnvironment($db,"noattend",$attendid+1);
	}
	
	function away($db,$userid,$awaytype){
		if(!$db){
			error_log("AWAY:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("AWAY: DB SELECT ERROR",0);
			die();
		}
		
		$state=attendCheck($db,$userid);
		if($state=="away"){
			error_log("attendcheckerror: away now",0);
			die();
		}
		$awayid=GetEnvironment($db,"noaway");
		$awayextension="";
		//~ awayに登録
		mysql_query(
			"insert into away value($awayid,$userid,$awaytype,NOW(),$awayextension)"
		);
		mysql_query(
			"delete from $state where userid=$userid"
		);
		SetEnvironment($db,"noaway",$awayid+1);
	}
	
	function gohome($db,$userid,$gohometype){
		if(!$db){
			error_log("GO HOME:SQL ERROR",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("GOHOME: DB SELECT ERROR",0);
			die();
		}
		
		$state=attendCheck($db,$userid);
		if($state=="gohome"){
			error_log("attendcheckerror: already came back home",0);
			die();
		}
		$gohomeid=GetEnvironment($db,"nogohome");
		$gohomeextension="";
		//~ awayに登録
		mysql_query(
			"insert into gohome value($gohomeid,$userid,$gohometype,NOW(),$gohomeextension)"
		);
		mysql_query(
			"delete from $state where userid=$userid"
		);
		SetEnvironment($db,"nogohome",$awayid+1);
	}
	
	
?>
