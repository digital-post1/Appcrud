<?php
session_start();

require_once "pdo.php";
require_once "util.php";

$pos = 0;
$edu = 0;
logincheck();

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
    header("Location: edit.php?profile_id=".$_GET['profile_id']);
    return;
}

    //  Make sure that profile_id is present
 if ( ! isset($_GET['profile_id']) ) {
 $_SESSION['error'] = "Missing profile_id";
 header("Location: edit.php?profile_id=".$_GET['profile_id']);
 return;
 }

 $message = datacheck();
    if (is_string($message)){
        $_SESSION['error'] = $message;
        header("Location: edit.php?profile_id=".$_GET['profile_id']);
        return;
    }
$message2 = validatePos();
if (is_string($message2)){
  $_SESSION['error'] = $message2;
  header("Location: edit.php?profile_id=".$_GET['profile_id']);
  return;
}
$message3 = validateEdu();
if (is_string($message3)){
  $_SESSION['error'] = $message3;
  header("Location: edit.php?profile_id=".$_GET['profile_id']);
  return;
}


if($message === TRUE){

    $sql = "UPDATE profile SET 
        first_name  = :first_name, 
        last_name   = :last_name,
        headline    = :headline,
        email       = :email, 
        summary     = :summary,
        profile_id  = :xyz
    WHERE profile_id = :xyz";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ":xyz" => htmlentities($_GET['profile_id']),
        ':first_name' => htmlentities($_POST['first_name']),
        ':last_name' => htmlentities($_POST['last_name']),
        ':headline' => htmlentities($_POST['headline']),
        ':email' => htmlentities($_POST['email']),
        ':summary' => htmlentities($_POST['summary'])
        ));


    $profile_id = $row['profile_id'];
    $rank = 0;
    $sql = "DELETE FROM position WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_GET['profile_id']));
    if($message2 === TRUE){

            for($i=1; $i<=9; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;

                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];


                $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
        );
        $rank++;
    };
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
<!DOCTYPE html>
<html>
<head>
<title>Dhani van Ingen</title>
<?php
require_once "head.php";
?>
</head>
<body>
<div class="container">
<script>
function doValidate() {

console.log('Validating...');

try {

fn = document.getElementById('id_1723').value;
ln = document.getElementById('id_1724').value;
hl = document.getElementById('id_1725').value;
em = document.getElementById('id_1726').value;
sm = document.getElementById('id_1727').value;

console.log("Validating pw="+fn);

if (fn == null || fn == "" || ln == null || ln == "" || hl == null || hl == "" || em == null || em == "" || sm == null || sm == "" ) {

alert("all fields must be filled out");

return false;

}
check = em.includes("@", 0)
if (check === false) {

alert("Email must have an at-sign (@)");

return false;

}

return true;

} catch(e) {

return false;

}

return false;

}
</script>
<h1>Edit user</h1>
<?php 
flashmessage();
?>
<form method="post" id="target">
<p>First name:
<input class="fields" type="text" name="first_name" id="id_1723" value="<?= $m ?>"></p>
<p>Last name:
<input class="fields" type="text" name="last_name" id="id_1724" value="<?= $n ?>"></p>
<p>headline:
<input class="fields" type="text" name="headline" id="id_1725" value="<?= $p ?>"></p>
<p>email:
<input class="fields" type="text" name="email" id="id_1726" value="<?= $t ?>"></p>
<p>summary:
<input class="fields" type="text" name="summary" id="id_1727" size="150" value="<?= $q ?>"/></p>
<p><input type="hidden" name="profile_id"  value="<?= $_GET['profile_id'] ?>"/>
<p><input class="fields" type="submit" onclick="return doValidate();" value="Save"/>
<p>
Position: <input class="fields" type="submit" id="addPos" value="+"></p>

<?php 
navBar();
$profile_id = $_GET['profile_id'];


loadPos($pdo , $profile_id);
$positions = loadPos($pdo , $profile_id);
echo("<div id='position_fields'>\n");
foreach ( $positions as $position ) {
    $pos++;
    echo('<div id="position'.$pos.'"');
    echo("<p>Year: <input class='fields' type='text' ");
    echo('name="year'.$pos.'" value='.$position['year'].' >');
    echo("<input type='button' value='-' ");
    echo('onclick="$(\'#position'.$pos.'\').remove(); return false;" >');
    echo(' </p> ');
    echo('<p> <textarea name="desc'.$pos.'" rows="8" cols="80">');
    echo($position['description']);
    echo("</textarea></p>");
    echo(" </div>");}
echo('</div>');


loadEdu($pdo , $profile_id);
$educations = loadEdu($pdo , $profile_id);
echo("<div id='school_fields'>\n");
echo('<p>School: <input type="submit" id="addschool" value="+"></p>');
foreach ( $educations as $education ) {
    $edu++;  
    echo('<div id="school'.$edu.'"');
    echo("<p>Year: <input class='fields' type='text' ");
    echo('name="schoolyear'.$edu.'" value='.$education['year'].' >');
    echo("<input type='button' value='-' ");
    echo('onclick="$(\'#school'.$edu.'\').remove(); return false;" >');
    echo(' </p>');
    echo('<p> <input class="fields" type="text" name="schoolname'.$pos.'" cols="80" class="school" value='.$education['name'].'>');
    echo(" </p></div>");}

?>
</div>
<script type="text/javascript">

countPos = <?= $pos ?>;
countschool = <?= $edu ?>;

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
            <p>Year: <input class="fields"  type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove() ; return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});

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
              School: <input  type="text" size="80" name="schoolname'+countschool+'" class="school" value="" />\
          </div> <p></p>');


          $('.school').autocomplete({ source: "school.php" });
         window.console && console.log("sending autocomplete get"); 
  });

});

</script>
</form>
</body>
<style>
@import "opmaak.css";
</style>
</html>
