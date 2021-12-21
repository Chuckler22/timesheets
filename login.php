<?php
   session_start();
?>

<html lang = "en">
<head>
   <title>Timesheet Login</title>
   <link rel="stylesheet" href="timesheet.css">
</head>
	
<body>
<form action="login-check.php" method="post">
  <div class="imgcontainer">
    <img src="/images/avatar.png" alt="Avatar" class="avatar">
  </div>
 
<?php
if(isset($_SESSION["error"])){
   $error = $_SESSION["error"];
   echo "<div id=\"loginerror\">$error</div>";
}
?>  

  <div class="container">
    <label for="uname"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="username" required>

    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="password" required>

    <button type="submit" id="login" name="login" value="login">LOGIN</button>
  </div>
</form>
</html>
<?php
    unset($_SESSION["error"]);
?>