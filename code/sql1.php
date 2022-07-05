<?php
header('content-type:text/html;charset=utf-8');
$username=$_GET['username'];
$password=md5($_GET['password']);
try{
    $pdo=new PDO('mysql:host=mysqldb;dbname=dvwa','root','root');
    $sql="select * from users where user='$username' and password='$password'";
    echo $sql;
    $stmt=$pdo->prepare($sql);
    $stmt->execute();
    echo "共查询到".$stmt->rowCount()."条数据<br>";
    echo "<table border=1>";
    echo "<th>ID</th><th>用户名</th><th>密码</th>";
    while($row=$stmt->fetch()){
        echo "<tr>";
    			echo "<td>".$row['user_id']."</td>";
    			echo "<td>".$row['user']."</td>";
                echo "<td>".$row['password']."</td>";
		echo "</tr>";
    }
    echo "</table>";
}catch(PDOException $e){
    echo $e->getMessage();
}
?>
