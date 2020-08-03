<?php
require_once "pdo.php";
session_start();
// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

//checks if years and miles are numeric
if (! is_numeric($_POST['mileage']) || ! is_numeric($_POST['year'])) {
    $failure = 'millegae and year must be numeric ';
}
else{
//check if make field is filled in
if (strlen($_POST['make']) < 1){
    $failure = 'Make is required';
}
if (strlen($_POST['year']) < 1){
    $failure = 'year is required';
}
if (strlen($_POST['mileage']) < 1){
    $failure = 'mileage is required';
}
if ($failure == false){
$stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES ( :mk, :yr, :mi)');
$stmt->execute(array(
':mk' => htmlentities($_POST['make']),
':yr' => htmlentities($_POST['year']),
':mi' => htmlentities($_POST['mileage'])
));
echo ('Record inserted');}}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dhani van Ingen Automobile Tracker</title>
</head>
<body>
<div class="container">
<h1>Tracking Autos for <?php echo $_GET['name']?></h1>
<form method="post">
<p>Make:
<input type="text" name="make" size="60"/></p>
<p>Year:
<input type="text" name="year"/></p>
<p>Mileage:
<input type="text" name="mileage"/></p>
<input type="submit" value="Add">
<input type="submit" name="logout" value="Logout">
</form>
<?php
if ( $failure !== false ) {
    echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
}
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo '<table border="1">'."\n";
foreach ( $rows as $row ) {
    echo "<tr><td>";
    echo($row['make']);
    echo("</td><td>");
    echo($row['mileage']);
    echo("</td><td>");
    echo($row['year']);
    echo("</td></tr>\n");
}
echo "</table>\n";
?>
</div>
</html>