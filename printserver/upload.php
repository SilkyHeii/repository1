<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>sample</title>
	</head>
	<body>
	<p><?php
	$db = mysql_connect('localhost', 'al-lab', 'A1gorithm');
	if (!$db) {
		echo '接続失敗です。';
		die('接続失敗です。');
	}
	mysql_select_db("printerserver");
	$table=mysql_query("select * from envs where name='nofiles'");
	$tuple=mysql_fetch_assoc($table);
	if(!$tuple){
		echo "データベース設定エラーです";
		die();
	}
	$nofiles =$tuple["value"];
	$nextnofiles = $nofiles+1;
	mysql_query("update envs set value = '$nextnofiles' where name='nofiles' ");
	$close_flag = mysql_close($db);
	if (!$close_flag){
		print('<p>切断に成功しました。</p>');
		die();
	}
	$mode = $_POST["mode"];
	$nopublish = $_POST["nopublish"];
	if(is_uploaded_file($_FILES["upfile"]["tmp_name"])){
		if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "files/" . $_FILES["upfile"]["name"])) {
			chmod("files/" . $_FILES["upfile"]["name"], 0644);
			echo $_FILES["upfile"]["name"] . "をアップロードしました。";
			$oldname=$_FILES["upfile"]["name"];
			$filename = "$nofiles.pdf";
			rename ("files/"."$oldname", "files/"."$filename" );
			if(strcmp($mode , "simplex")==0){	//片面
				for($i=0;$i<$nopublish;$i++){
				system(" AcroRd32.exe /t \"C:/Apache24/htdocs/files/$filename\" \"EPSON LP-M6000\"   ");
				}
				echo "印刷を実行しました　プリンタ名=EPSON LP-M6000";
			}else if(strcmp($mode , "duplex")==0){	//両面
				for($i=0;$i<$nopublish;$i++){
				system(" AcroRd32.exe /t \"C:/Apache24/htdocs/files/$filename\" \"Canon LBP5910 LIPSLX\"   ");
				}
				echo "印刷を実行しました　プリンタ名=Canon LBP5910 LIPSLX";
			}else{
				echo "印刷モードが不適切です。";
			}
		}else{
			echo "ファイルをアップロードできません。";
		}
	}else{
		echo "ファイルが選択されていません。";
	}
	
	
	?></p>
	<a href="http://192.168.11.94/form.php">もう一度印刷する</a>
	</body>
</html>