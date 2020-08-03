<?php
function logincheck() {
if (!isset($_SESSION['succes'])) {
    die('Not logged in');
}
}


function flashmessage() {
if (isset($_SESSION['error'])){
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
if (isset($_SESSION['added'])){
    echo('<p style="color: green;">'.htmlentities($_SESSION['added'])."</p>\n");
    unset($_SESSION['added']);
}}


function datacheck(){
    if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
      && isset($_POST['headline']) && isset($_POST['summary'])  ) {
// checks if all info is set
if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['email']) < 1  || strlen($_POST['summary']) < 1) {
    return "All fields are required";
 }

 if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
     return "Email must have an at-sign (@)";
 }
else{
    return TRUE ;
}}}

function validatePos() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
  
      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        return "All fields are required ";
      }
  
      if ( ! is_numeric($year) ) {
        return "Position year must be numeric";
      }
      else{
        return TRUE;
      }
    }
   
  }

  function validateEdu() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['schoolyear'.$i]) ) continue;
      if ( ! isset($_POST['schoolname'.$i]) ) continue;
  
      $schoolyear = $_POST['schoolyear'.$i];
      $schoolname = $_POST['schoolname'.$i];
  
      if ( strlen($schoolyear) == 0 || strlen($schoolname) == 0 ) {
        return "All fields are required ";
      }
  
      if ( ! is_numeric($schoolyear) ) {
        return "Educttion year must be numeric";
      }
      else{
        return TRUE;
      }
    }
   
  }

  function loadPos($pdo , $profile_id) {
    $stmt2 = $pdo->prepare("SELECT * FROM position WHERE profile_id = :prof ORDER BY rank");
    $stmt2->execute(array(":prof" => $profile_id));
    $positions = array(); 
    while($row = $stmt2->fetch(PDO::FETCH_ASSOC) ){
        $positions[] = $row;

        }
        return $positions
        ;
        


  }
function loadProfile($pdo) {
  $stmt = $pdo->query("SELECT profile_id, user_id, first_name, headline FROM profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo '<table class="table" border="2">'."\n";
echo "<th>First name</th>";
echo "<th>headline</th>";
foreach ( $rows as $row ) {
    echo "<tr><td>";
    echo('<a href="view.php?profile_id='.$row['profile_id'].' ">');
    echo($row['first_name']);
    echo("</a></td><td>");
    echo($row['headline']);
    echo("</td><td>");
    if(isset($_SESSION['succes'] )){
        if($row['user_id'] === $_SESSION['user_id']){
    echo('<a href ="edit.php?profile_id='.$row['profile_id'].' ">Edit</a>/');
    echo('<a href ="delete.php?profile_id='.$row['profile_id'].'">Delete</a>/ ');
    echo("</td><td>");
    }
    echo("</td></tr>\n");
}
}
echo "</table>\n";

}
function loadEdu($pdo , $profile_id){
  $stmt = $pdo->prepare('SELECT year, name FROM education JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id = :pid ORDER BY rank');
  $stmt->execute(array(":pid" => $profile_id));
  $educations = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $educations;
  }


  function insertEducations($pdo , $profile_id){
    $rank = '1';
    for($j=1; $j<=9; $j++) {
      if ( ! isset($_POST['schoolyear'.$j]) ) continue;
      if ( ! isset($_POST['schoolname'.$j]) ) continue;
  
      $schoolyear = $_POST['schoolyear'.$j];
      $schoolname = $_POST['schoolname'.$j];
      $institution_id = false;
      $rank++;
      
$stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name = :schoolname');
$stmt->execute(array(':schoolname' => $schoolname));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row !== false){ $institution_id = $row['institution_id'];
}
if($institution_id  === false){

      $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:schoolname)');
$stmt->execute(array(':schoolname' => $schoolname));
  $institution_id = $pdo->lastInsertId();
}
$stmt = $pdo->prepare('INSERT INTO education (profile_id , institution_id, rank, year) VALUES (:pid, :inid , :schoolrank, :schoolyear)');
  
$stmt->execute(array(
':pid' => $profile_id,
':inid' => $institution_id,
':schoolrank' => $rank,
':schoolyear' => $schoolyear)
);
} 
}

function navBar(){
  if (isset($_SESSION['succes'])) {
    echo('<ul class="navbar"><li class="navbar"><a class="navbar" href ="add.php">Add New Entry</a></li> <li class="navbar"><a class="navbar" href ="logout.php">logout</a></li><li class="navbar"><a class="navbar" href="index.php">Index</a></li></ul>');
}
else{
    echo('<ul class="navbar"><li class="navbar"><a class="navbar" href="login.php">Please log in</a></li><li class="navbar"><a class="navbar" href="index.php">Index</a></li></ul> ');
}

}
