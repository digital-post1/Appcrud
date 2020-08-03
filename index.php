

<html>
<head>
<title>Dhani van Ingen </title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1 class="head">Welcome to the Resume database</h1>
<?php
session_start();
require_once "pdo.php";
require_once "util.php";


?>
<p>
<?php
flashmessage();
loadProfile($pdo);
navBar();
?>
</p>
</div>
</body>
<style>
@import "opmaak.css";
</style>
</html>

