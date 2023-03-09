<php? header('Content-type:text/html;charset=UTF-8'); ?>
<html>
<body>
    <form method="post" action="">
        <input type="text"name="todocont"size="30"maxlength="100">
        <?php
        $prioDisp=2;//表示する優先度    ０：低、1：高、2：すべて
        $selStr=array('','','');
        $prio=0;//Todoを追加するときに指定する優先度（0～2）
        if(isset($_POST['priority'])){
            $prio=(int)$_POST['priority'];
        }
        ?>
        <select name="priority">
            <option value="2">すべて
            <option value="1">高
            <option value="0">低
        </select>
        <br><br>
        <input type="submit" name="insert" value="追加">
        <input type="submit" name="search" value="検索">
        <input type="submit" name="delete" value="削除">
        <pre>
<?php
            //MySQLに接続する
            $dsn='mysql:dbname=zpdb;host=localhost;charset=utf8mb4';//DBへの接続情報
            $user='root';//ユーザ名
            $passward='admin';//パスワード
            try{
                $dbh=new PDO($dsn,$user,$passward);//DBへの接続
            }catch(PODException $e){
                die('Connect Error:'.$e->getCode());//DBへの接続エラー時
            }

            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);//エミュレート機能をOFF

            //追加ボタンが押された時の処理
            if (isset($_POST['insert'],$_POST['todocont'])&& $_POST['todocont']!='') {
                if ($prio<0||$prio>1) {//優先度が低でも高でもない場合
                    $prio=0;//低にする
                }
                $sql='INSERT INTO todolist(todo,prio, created)VALUES(?,?,CURDATE())';
                $sth=$dbh->prepare($sql);
                $sth->bindValue(1,$_POST['todocont'],PDO::PARAM_STR);
                $sth->bindValue(2,$prio,PDO::PARAM_INT);
                $sth->execute();//SQL実行
            }
            // TODOリストを表示する
            $sql='SELECT id,todo FROM todolist';
            $sth=$dbh->prepare($sql);
            $sth->execute(); //   SQL実行
            while($row = $sth->fetch(PDO::FETCH_ASSOC)){
                echo '<input type="checkbox" name="chktodo[]" value="';
                echo htmlspecialchars($row['id'],ENT_QUOTES,'UTF-8');
                echo '">';
                echo htmlspecialchars($row['todo'],ENT_QUOTES,'UTF-8'), PHP_EOL;
            }
            $dbh=null;
?>
        </pre>
    </form>
</body>
</html>