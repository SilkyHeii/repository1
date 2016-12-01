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


        <script type="text/javascript" src="../assets/javascript/home.js"></script>
    </head>
    <body>
        <h1>Hello, world!</h1>
        <br><a href='../index.php' class="btn btn-default">汝の在るべきページに戻れ</a>
        <!--タブ-->
        <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">タブ1</a></li>
        <li><a href="#tab2" data-toggle="tab">タブ2</a></li>
        </ul>
        <!-- / タブ-->
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="tab1">
                <p>コンテンツ1</p>
            </div>
            <div class="tab-pane fade" id="tab2">
                <p>コンテンツ2</p>
            </div>
        </div>

        <div id="columns-full">
          <div class="column" draggable="true"><header>A</header>
              <div class="count" data-col-moves="0">moves:0</div>
          </div>
          <div class="column" draggable="true"><header>B</header><div class="count" data-col-moves="0">moves:0</div></div>
          <div class="column" draggable="true"><header>C</header><div class="count" data-col-moves="0">moves:0</div></div>
        </div>
    </body>
</html>
