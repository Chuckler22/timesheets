<?php
   ob_start();
   session_start();


   $msg = '';
   
   if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {			
       $ds=ldap_connect("ldap://ldap_server:389");  // must be a valid LDAP server!
       ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);                
       if ($ds) {
           $r=ldap_bind($ds, "cn=".$_POST['username'].",dc=testorg", $_POST['password']); 
               if ($r) {
                   echo "LDAP bind successful...";
                   $_SESSION['valid'] = true;
                   // $_SESSION['timeout'] = time();
                   $_SESSION['username'] = $_POST['username'];
                   header("location:./index.php");
               } else {
                   echo "LDAP bind failed...";
               }
           }
       } 


?>

<?
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>

<html lang = "en">
<head>
   <title>Timesheet Login</title>
</head>
	
<body>
   <h2>Enter Username and Password</h2>    
   <div class = "container">
      <form class = "form-signin" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">
         <h4 class = "form-signin-heading"><?php echo $msg; ?></h4>
         <input type = "text" class = "form-control" name = "username" required autofocus></br>
         <input type = "password" class = "form-control" name = "password" required>
         <button style="text-align: center;" type = "submit" name = "login">Login</button>
      </form>
   </div> 
</body>
</html>