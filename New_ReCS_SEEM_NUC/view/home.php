<?php
require_once("../core/envs.php");
$db=mysql_connect('localhost','root','');
$db_select = mysql_select_db('chat_production' ,$db);
print("よくぞ見つけた！！現在使われていないページを");
print("<br><a href='../index.php'>汝の在るべきページに戻れ</a>");
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Bootstrap Sample</title>
        <!-- BootstrapのCSS読み込み -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="../lib/bootstrap-3.3.7-dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="../lib/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <link rel="stylesheet" href="../assets/css/home.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="../lib/bootstrap-3.3.7-dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    </head>

    <body>
        <h1>Hello, world!</h1>
        <br><a href='../index.php' class="btn btn-default">汝の在るべきページに戻れ</a>

        <div id="columns" class="tabbox">
          <p class="tabs">
              <a href="#tab1" class="tab1 column">tab1</a>
              <a href="#tab2" class="tab2 column">tab2</a>
              <a href="#tab3" class="tab3 column">tab3</a>
          </p>
          <div class="clear"></div>
          <div id="tab1" class="column tab" draggable="true"><header>A</header>
              <div class="count" data-col-moves="0">moves:0</div>
          </div>
          <div id="tab2" class="column tab" draggable="true"><header>B</header><div class="count" data-col-moves="0">moves:0</div></div>
          <div id="tab3" class="column tab" draggable="true"><header>C</header><div class="count" data-col-moves="0">moves:0</div></div>
        </div>
        <script type="text/javascript" src="../assets/javascript/home.js"></script>
    </body>
</html>
