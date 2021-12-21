<?php

session_start();
ob_start();

if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {			
    $ds=ldap_connect("ldap://ldap_server:389");  // must be a valid LDAP server!
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);                
    if ($ds) {
        $r=ldap_bind($ds, "cn=".strtolower($_POST['username']).",dc=testorg", $_POST['password']); 
        if ($r) {
            // echo "LDAP bind successful...";
            $_SESSION['valid'] = true;
            $_SESSION['username'] = strtolower($_POST['username']);
            header("location: ./index.php");
        } else { 
            $error = "username/password incorrect";
            $_SESSION["error"] = $error;
            header("Location: /login.php"); //send user back to the login page.
        }
    }
}




?>