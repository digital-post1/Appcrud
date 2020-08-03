<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc', 'fred', 'zap');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//try { $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :xyz')
//$stmt->execute(array(":xyz" => $_GET[name]))}
//catch (exception $ex) {
//    echo(.$ex->getMessage());
//    return;
//}
?>