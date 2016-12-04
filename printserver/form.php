<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>sample</title>
</head>
<body>
	<form action="upload.php" method="post" enctype="multipart/form-data">
	ファイル(pdfファイルのみ受け付けます):
	<br /><br />
	<input type="file" name="upfile" size="30" />	
	<br /><br/>
  <input type="radio" name="mode" value="simplex" checked>片面印刷
  <input type="radio" name="mode" value="duplex" >両面印刷
  <br /><br/>
  部数：	
		<select name="nopublish">
		<?php
		for($i=1;$i<=50;$i++){
			print("<option value='$i'>$i</option>");		
		}
		?>
		<br>
		</select>
  <input type="submit" value="アップロード" />
</form>
</body>
</body>
</html>