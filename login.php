<?php 
require_once "pdo.php";
require_once "bootstrap.php";
require_once "util.php";
session_start ();

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}
$salt = 'XyZzy12*_';
if(isset($_POST['pass'])){
$md5 = hash('md5', 'XyZzy12' , $_POST['pass']);
}



// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "email and password are required";
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
            $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $row !== false ) {

                $_SESSION['name'] = $row['name'];                
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['succes'] = 'succes';
                error_log("Login success ".$_POST['email']); 
                           
              
                header("Location: index.php");
                
                return;
            }    
            else{
                $_SESSION['error'] = "incorrect password";
            }
            }


            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email must have an at-sign (@)"; 
                }
            if(isset($_SESSION['error'])){
                error_log("Login fail ".$_POST['email']." $check");
                header("location: login.php");
                return;
            }
            }
        
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "head.php"; ?>
<title>Dhani van Ingen database</title>
<script>
function doValidate() {

console.log('Validating...');

try {

pw = document.getElementById('id_1723').value;
em = document.getElementById('nam').value;
check = em.includes('@', 0);


console.log("Validating pw="+pw);

if (pw == null || pw == "" || em == null || em == "") {

alert("Both fields must be filled out");

return false;

}
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
</head>
<body>
<div class="container">
<h1>Please Log In</h1>

<?php
flashmessage();
navBar();
?>
<form method="POST">
<label for="nam">email</label>
<input class="fields" type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input class="fields" type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
<style>
@import "opmaak.css";
</style>
</body>
</html>