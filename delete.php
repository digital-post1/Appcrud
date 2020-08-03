<?php
require_once "pdo.php";
require_once "util.php";
session_start();
logincheck();
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['added'] = 'Profile deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: name sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT profile_id, headline FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}
navBar();
?>
<html>
<head>
<title>Dhani van Ingen</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Confirm: Deleting <?= htmlentities($row['headline']) ?></h1>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="delete" onclick= "alert('confirm deleting profile');">
</form>
</body>
<style>
@import "opmaak.css";
</style>
</html>
