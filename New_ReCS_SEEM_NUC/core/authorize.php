<?php 
	function Authorize($db,$userid,$username,$password){
		if(!$db){
			error_log("Authorize: SQL error",0);
			die();
		}
		if(!mysql_select_db("chat_production",$db)){
			error_log("Authorize:DB SELECT ERROR",0);
			die();
		}
		$rows=mysql_query(
			"select * from users where id = $userid"
		);
		$row=mysql_fetch_assoc($rows);
		if(!$row){
			error_log("Authorize:INVALID USERID",0);
			return false;
		}
		
		
		
		$db_userid=$row["id"];
		$db_username=$row["name"];
		$db_userpassword=$row["password"];
		
		mysql_query(
			"update envs set value=PASSWORD('$password') where id ='temp0'"
		);
		
		
		$pw=GetEnvironment($db,"temp0");
		$result=(
			$userid==$db_userid
			&&
			$username==$db_username
			&&
			$pw==$db_userpassword
		);
		
		print("<br>".$userid."<br>".$username."<br>".$pw);
		print("<br>".$db_userid."<br>".$db_username."<br>".$db_userpassword);
		SetEnvironment($db,"temp0","");
		return $result;
		
	}

?>
