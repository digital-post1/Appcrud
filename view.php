<?php
session_start();
require_once "pdo.php";
require_once "util.php";


$pos = 0;
$edu = 0;
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
    }

    $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

$m = htmlentities($row['first_name']);
$n = htmlentities($row['last_name']);
$p = htmlentities($row['headline']);
$t = htmlentities($row['email']);
$q = htmlentities($row['summary']);




if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header("Location: index.php");
    return;
}
?>



<html>
<head>
<title>Dhani van Ingen</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>View user</h1>

<?php 
flashmessage();
navBar();

?>
<table class="table" border="2">
<th>First name</th>
<th>Last name</th>
<th>email</th>
<th>headline</th>
<th>summary</th>
<tr><td>
    <?php 
    echo($m);
    echo("</td><td>");
    echo($n);
    echo("</td><td>");
    echo($p);
    echo("</td><td>");
    echo($t);
    echo("</td><td>");
    echo($q);
    echo("</td></tr>\n");
    echo ("</table>\n");
    echo('<p><b>Positons:</b>');


$profile_id = $_GET['profile_id'];
loadPos($pdo , $profile_id);
$positions = loadPos($pdo , $profile_id);

foreach ( $positions as $position ) {
    $pos++;
    echo('<ul class="PosAndEdu"><li>');
    echo($position['year']);
    echo('    :    ');
    echo($position['description']);
    echo("</li></ul></p>\n");
}
echo('<p><b>Educations</b></p>');
loadEdu($pdo , $profile_id);
$educations = loadEdu($pdo , $profile_id);
foreach ( $educations as $education ) {
    $edu++;
    echo('<ul class="PosAndEdu"><li>');
    echo($education['year']);
    echo('    :    ');
    echo($education['name']);
    echo("</li></ul></p>\n");
}

    ?>


</ul> 
</body>
<style>
@import "opmaak.css";
</style>
</html>