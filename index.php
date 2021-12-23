<?php
session_start();

if(!isset($_SESSION["username"])) {
  header("location:./login.php");
}
$yearstart = "09 January 2022";
$loggedinuser = $_SESSION["username"];

require("config.inc.php");

if (($_SERVER['REQUEST_METHOD'] == "POST") && (isset($_POST["create"]))) {
  if (!isset($_POST["fne_date"])) {
    exit("No fortnight selected.");
  }
  if (!preg_match('/\d{4}-\d{2}-\d{2}/',$_POST["fne_date"])) {
    exit("Invalid timesheet format.");
  } else {
    $fne = $_POST["fne_date"];
    $sql = "SELECT employees.id as employees_id, employees.novellname, timesheets.id as timesheets_id, timesheets.employee, timesheets.fne_date 
            FROM `employees`
            LEFT JOIN `timesheets` ON employees.id = timesheets.employee 
            WHERE employees.novellname = ? AND timesheets.fne_date = ?";
    $userStatement = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($userStatement, 'ss', $loggedinuser, $fne);
    mysqli_stmt_execute($userStatement);
    $result = mysqli_stmt_get_result($userStatement);
    if (mysqli_num_rows($result) == 1) {
      exit("Timesheet already exists.");
      } else {
      $sql = "SELECT employees.id as employee_id, employees.first, employees.last, employees.email, employees.novellname, 
              employees.toilcf, employees.default_daystart, employees.default_daystop, employees.default_break1start, 
              employees.default_break1stop, employees.default_break2start, employees.default_break2stop, 
              employees.default_break3start, employees.default_break3stop
              from employees 
              WHERE employees.novellname = ?";
      $userStatement = mysqli_prepare($conn,$sql);
      mysqli_stmt_bind_param($userStatement, 's', $loggedinuser);
      mysqli_stmt_execute($userStatement);
      $result = mysqli_stmt_get_result($userStatement);
      while($row = mysqli_fetch_assoc($result)) {
        $sql = "INSERT INTO `timesheets` (`id`, `employee`, `fne_date`) VALUES (NULL, ?, ?)";
        $insertStatement = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($insertStatement,'is',$row["employee_id"],$fne);
        mysqli_stmt_execute($insertStatement);
        $timesheet_id = mysqli_insert_id($conn); 
        
        $endTime = strtotime($fne);
        $startTime= strtotime("-13 days", $endTime);
        $x = 1;
        // Loop between timestamps, 24 hours at a time
        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
          $thisDate = date( 'Y-m-d', $i ); 
          $weekends = array(6,7,13,14);
          if (in_array($x,$weekends)) {
            $sql = "INSERT INTO `days` (`id`, `timesheet_id`, `dof`, `datex`, `daystart`, `daystop`, `break1start`, `break1stop`, `break2start`, `break2stop`, `break3start`, `break3stop`, `oncall`, `lvannual`, `lvsick`, `lvtoil`, `lvphcon`, `lvflex`, `oc1start`, `oc1stop`, `oc2start`, `oc2stop`, `oc3start`, `oc3stop`, `ot1start`, `ot1stop`, `ot2start`, `ot2stop`, `ot3start`, `ot3stop`, `toil1start`, `toil1stop`, `toil2start`, `toil2stop`, `toil3start`, `toil3stop`)
                    VALUES (NULL, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
            $insertStatement = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($insertStatement,'iis',$timesheet_id, $x, $thisDate);
          } else {
            $sql = "INSERT INTO `days` (`id`, `timesheet_id`, `dof`, `datex`, `daystart`, `daystop`, `break1start`, `break1stop`, `break2start`, `break2stop`, `break3start`, `break3stop`, `oncall`, `lvannual`, `lvsick`, `lvtoil`, `lvphcon`, `lvflex`, `oc1start`, `oc1stop`, `oc2start`, `oc2stop`, `oc3start`, `oc3stop`, `ot1start`, `ot1stop`, `ot2start`, `ot2stop`, `ot3start`, `ot3stop`, `toil1start`, `toil1stop`, `toil2start`, `toil2stop`, `toil3start`, `toil3stop`)
                    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
            $insertStatement = mysqli_prepare($conn,$sql);
            mysqli_stmt_bind_param($insertStatement,'iisssssssss',$timesheet_id, $x, $thisDate, $row["default_daystart"], $row["default_daystop"], $row["default_break1start"], $row["default_break1stop"], $row["default_break2start"], $row["default_break2stop"], $row["default_break3start"], $row["default_break3stop"]);
          }
          mysqli_stmt_execute($insertStatement);
          $x++;
        }
      }
    }
    header("location: /timesheet-edit.php?fne=".$fne);
    exit();
  }
header("location: /");
} //endif POST

// start date
$mydate = strtotime($yearstart);
$fn = array(date("Y-m-d", $mydate));
for ($x = 1; $x <= 26; $x++) {
  $mydate = strtotime("+2 weeks", $mydate);
  array_push($fn,date("Y-m-d", $mydate));
}

$openlist = array();
$closedlist = array();
$outlist = array();
$sql = "SELECT employees.first, employees.last, employees.email, timesheets.fne_date, timesheets.submitted 
          FROM employees 
          LEFT JOIN timesheets on employees.id = timesheets.employee
          WHERE timesheets.submitted is NULL AND employees.novellname = ?";
$userStatement = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($userStatement,'s',$loggedinuser);
mysqli_stmt_execute($userStatement);
$result = mysqli_stmt_get_result($userStatement);
while($row = mysqli_fetch_assoc($result)) {
    $first = $row["first"];
    $last = $row["last"];
    $email = $row["email"];
    $fne_date = $row["fne_date"];
    if (in_array($fne_date, $fn)) {
      array_push($openlist,$fne_date);
    } 
}
$sql = "SELECT employees.first, employees.last, employees.email, timesheets.fne_date, timesheets.submitted 
          FROM employees 
          LEFT JOIN timesheets on employees.id = timesheets.employee
          WHERE timesheets.submitted is NOT NULL AND employees.novellname = ?";
$userStatement = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($userStatement,'s',$loggedinuser);
mysqli_stmt_execute($userStatement);
$result = mysqli_stmt_get_result($userStatement);
while($row = mysqli_fetch_assoc($result)) {
    $first = $row["first"];
    $fne_date = $row["fne_date"];
    if (in_array($fne_date, $fn)) {
      array_push($closedlist,$fne_date);
    } 
}

foreach ($fn as $v) {
  if (!in_array($v,$openlist) && !in_array($v,$closedlist)) {
    array_push($outlist,$v);
  }
}

echo "<link rel=\"stylesheet\" href=\"timesheet.css\">";
echo "<a href=\"/logout.php\"><input type=\"button\" value=\"Logout\" style=\"position: fixed; top: 8px; right:8px\"></a>";

echo "<br><h1>Welcome ".$first."</h1>";

$status = $statusmessage = NULL;
echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";
echo "Create a new timesheet:<br>";
$v = $outlist[0];
$w = date_create($v);
$w = date_format($w,"dS F, Y");
if (count($openlist) > 0) {
  $status = "disabled";
  $statusmessage = "<br>You cannot create a new timesheet until timesheets are submitted.";
}
echo "<input type=\"hidden\" name=\"fne_date\" value=\"".$v."\">"; 
echo "<input type=\"submit\" name=\"create\" value=\"".$w."\" ".$status.">";
echo $statusmessage;
echo "  </form>";

$status = $statusmessage = NULL;
echo "<form>";
echo "Current timesheet:<br>";
if (count($openlist) == 0) {
  echo "<input type=\"button\" value=\"None\" disabled>";
  echo "<br>You must create a new timesheet to edit.";
} else {
  $v = $openlist[0];
  $w = date_create($v);
  $w = date_format($w,"dS F, Y");
  echo "<a href=\"/timesheet-edit.php?fne=".$v."\"><input type=\"button\" value=\"".$w."\"></a>";
}
echo "</form>";


echo "Previous (approved) timesheets:<br>";
foreach ($closedlist as $v) {
  $v = $openlist[0];
  $w = date_create($v);
  $w = date_format($w,"dS F, Y");
  echo "<a href=\"/timesheet-view.php?fne=".$v."\"><input type=\"button\" value=\"".$w."\"></a>";
}


echo "<br><br><br><br><br><br><br><br>";


    // if supervisor = true display timesheets that are ready for approval

    $sql = "SELECT timesheets.id as timesheet_id, timesheets.employee, fne_date, submitted, approvedby, approvedtime, employ.id, employ.first, employ.last, employ.supervisor, super.id, super.novellname FROM `timesheets` LEFT JOIN `employees` employ on timesheets.employee = employ.id LEFT JOIN `employees` super ON employ.supervisor = super.id WHERE submitted IS NOT NULL and approvedby IS NULL AND super.novellname = ?";
    $userStatement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($userStatement,'s',$loggedinuser);
    mysqli_stmt_execute($userStatement);
    $result = mysqli_stmt_get_result($userStatement);
    if (mysqli_num_rows($result) > 0) {
      echo "Timesheets for Approval:<br>";
      echo "<ul>";
      while($row = mysqli_fetch_assoc($result)) {
        echo "<ol>".$row["first"]." ".$row["last"].":  <a href=\"./timesheet-view.php?timesheet=".$row["timesheet_id"]."\">".$row["fne_date"]."</a></ol>";
      }
      echo "</ul>";
    }

?>
