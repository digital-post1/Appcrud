<?php
session_start();
require_once "pdo.php";
require_once "util.php";
logincheck();

datacheck();
$message = datacheck();
    if (is_string($message)){
        $_SESSION['error'] = $message;
        header("Location: add.php");
        return;
    }

    validatePos();
$message2 = validatePos();
if (is_string($message2)){
  $_SESSION['error'] = $message2;
  header("Location: add.php");
  return;
}
$message3 = validateEdu();
if (is_string($message3)){
  $_SESSION['error'] = $message3;
  header("Location: edit.php?profile_id=".$_GET['profile_id']);
  return;
}

if ($message === TRUE ){
      $stmt = $pdo->prepare('INSERT INTO Profile
        ( user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
      
      $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
      );
    $profile_id = $pdo->lastInsertId();
    $rank = 0;
if($message2 === TRUE){
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
      $rank++;

      $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

$stmt->execute(array(
  ':pid' => $profile_id,
  ':rank' => $rank,
  ':year' => $year,
  ':desc' => $desc)
);
}




    }
    if($message3 === TRUE){
      $sql = "DELETE FROM education WHERE profile_id = :zip";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':zip' => $_GET['profile_id']));


      insertEducations($pdo , $profile_id);
              }
              $_SESSION['added'] = 'resume updated';
              header("Location: index.php");
              return;
}




  






?>

<html>
<head>
<title>Dhani van Ingen </title>

<script src="jquery1.js"></script>
  <script src="jquery2.js"></script>
  <?php
  require_once "head.php";
  ?>

</head>

<body>
<div class="container">


<h1>resume database add</h1>
<p>
<?php
flashmessage();
navBar();
?>
</p>

<form method="post">
<p>First name:
<input class="fields" type="text" name="first_name" id="id_1723" ></p>
<p>Last name:
<input class="fields" type="text" name="last_name" id="id_1724"></p>
<p>email:
<input class="fields" type="text" name="email" id="id_1726" ></p>
<p>headline:
<input  class="fields" type="text" name="headline" id="id_1725" ></p>
<p>summary:
<input class="fields" type="text" name="summary" id="id_1727" size="150" ></p>
<p>
Position: <input type="submit" id="addPos" value="+"></p>
<div id="position_fields">
</div>

<p>School: 
<input type="submit" id="addschool" value="+"></p>
<div id="school_fields"></div>
<input type="submit" onclick="return doValidate();" name="Add" value="Add">
</form>
<script type="text/javascript">

countPos = 0;
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input class="fields" type="text" name="year'+countPos+'" value="" />\
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"> </p>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
countschool = 0;
$(document).ready(function(){
  


    $('#addschool').click(function(event){
        event.preventDefault();
        if ( countschool >= 9 ) {
            alert("Maximum of nine school entries exceeded");
            return;
        }
        countschool++;
        window.console && console.log("Adding school "+countschool);
        $('#school_fields').append(
            '<div id="school'+countschool+'"> \
            <p>Year: <input class="fields" type="text" name="schoolyear'+countschool+'" value="" />\
            <input type="button" value="-" \
                onclick="$(\'#school'+countschool+'\').remove();return false;"> </p>\
                School: <input  type="text" size="80" name="schoolname'+countschool+'" class="school" id="schoolname"value="" />\
            </div> ');



            $('.school').autocomplete({ source: "school.php" });
         window.console && console.log("sending autocomplete get");

    });

  });



</script>

</body>
<style>
@import "opmaak.css";
</style>
</html>

