<?php
   session_start();
   if(!isset($_SESSION["username"])) {
    header("location:./login.php");
  }
  
  $loggedinuser = $_SESSION["username"];
  require("config.inc.php");

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
  require 'vendor/autoload.php';


  if($_SERVER['REQUEST_METHOD'] == "POST") { 
    if(!empty($_POST["home"])) {
      header("Location: /");
      exit("Header redirect no worky");
    }
    if(!empty($_POST["logout"])) {
      header("Location: /logout.php");
      exit("Logout Redirect");
    }
  }

  // error handling
  // if we're coming to this page using the timesheet $_GET variable, check we're a supervisor for this timesheet
  if (isset($_GET["timesheet"])) {
    $sql = "SELECT timesheets.id as timesheet_id,timesheets.employee,`fne_date`,`submitted`,`approvedby`,`approvedtime`, employ.id, employ.first, employ.last, employ.email, employ.novellname, employ.supervisor, super.id as superid, super.novellname as supernovell, super.first as superfirst, super.last as superlast, super.email as superemail FROM `timesheets` LEFT JOIN `employees` employ on timesheets.employee = employ.id LEFT JOIN `employees` super ON employ.supervisor = super.id WHERE submitted IS NOT NULL AND timesheets.id = ? AND super.novellname = ?";
    $userStatement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($userStatement,'is',$_GET["timesheet"],$loggedinuser);
    mysqli_stmt_execute($userStatement);
    $result = mysqli_stmt_get_result($userStatement);
    if (mysqli_num_rows($result) == 1) {
      $getData = mysqli_fetch_assoc($result);
      $fne = $getData["fne_date"];
      $novellname = $getData["novellname"];
      $superid = $getData["superid"];
      $superfirst = $getData["superfirst"];
      $superlast = $getData["superlast"];
      $superemail = $getData["superemail"];
      $employemail = $getData["email"];
    } else {
      exit("You must select a fortnight!");
    }
  }
  if (!isset($_GET["fne"]) && (!isset($fne))) {
    exit("You must select a fortnight!");
  }
   // $_GET["fne"] must be in the format YYYY-MM-DD
  if (isset($_GET["fne"])) {
    if (!preg_match('/\d{4}-\d{2}-\d{2}/',$_GET["fne"])) {
      exit("Invalid timesheet format.");
    } else {
      $fne = $_GET["fne"];
      $sql = "SELECT employees.id as employees_id, employees.novellname, timesheets.id as timesheets_id, timesheets.employee, timesheets.fne_date 
              FROM `employees`
              LEFT JOIN `timesheets` ON employees.id = timesheets.employee 
              WHERE employees.novellname = ? AND timesheets.fne_date = ?";
      $userStatement = mysqli_prepare($conn,$sql);
      mysqli_stmt_bind_param($userStatement, 'ss', $loggedinuser, $fne);
      mysqli_stmt_execute($userStatement);
      $result = mysqli_stmt_get_result($userStatement);
      if (mysqli_num_rows($result) == 1) {
        $getData = mysqli_fetch_assoc($result);
        $timesheet = $getData['timesheets_id'];
        $novellname = $getData['novellname'];
      } else {
        // header("Location: /");
        exit("Unable to find timesheet.");
      }
    }
  }

  if(!empty($_POST["approve"])) {
    $sql = "UPDATE `timesheets`, `employees`
            SET  timesheets.approvedtime = ".$now.", timesheets.approvedby = ?
            WHERE timesheets.employee = employees.id
            AND timesheets.id = ?";
    $updateStatement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($updateStatement,"ii",$superid,$_GET["timesheet"]);
    mysqli_stmt_execute($updateStatement);
    $output = "true";
    // header("Location: /");
    // exit("Header redirect no worky");
  }
  
  $sql = "SELECT employees.id as employees_id, employees.first, employees.last, employees.email, timesheets.flexcf, timesheets.toilcf, employees.novellname,
    timesheets.id as timesheets_id, timesheets.employee, timesheets.fne_date, timesheets.submitted, timesheets.approvedby, timesheets.approvedtime,
    days.timesheet_id, days.dof, .days.datex as datex, days.daystart, days.daystop, days.break1start, days.break1stop, days.break2start, days.break2stop, days.break3start, days.break3stop, days.oncall, days.lvannual, days.lvsick, days.lvtoil, days.lvphcon, days.lvflex, days.oc1start, days.oc1stop, days.oc2start, days.oc2stop, days.oc3start, days.oc3stop, days.ot1start, days.ot1stop, days.ot2start, days.ot2stop, days.ot3start, days.ot3stop, days.toil1start, days.toil1stop, days.toil2start, days.toil2stop, days.toil3start, days.toil3stop,
    super.first as firstname, super.last as lastname
  FROM `employees`
  LEFT JOIN `timesheets` ON employees.id = timesheets.employee 
  LEFT JOIN `days` ON timesheets.id = days.timesheet_id
  LEFT JOIN `employees` super ON employees.supervisor = super.id
  WHERE employees.novellname = '".$novellname."' AND timesheets.fne_date = '".$fne."'";
  // $html .= $sql;
  $result = mysqli_query($conn, $sql);
  while($row = mysqli_fetch_assoc($result)) {
      $first = $row["first"];
      $last = $row["last"];
      $email = $row["email"];
      $flexcf = $row["flexcf"] == "" ? "00:00" : substr($row["flexcf"],0,-3);
      $toilcf = $row["toilcf"] == "" ? "00:00" : substr($row["toilcf"],0,-3);
      $novellname = $row["novellname"];
      $submitted = $row["submitted"];
      $approved = $row["approvedby"];
      $approvedby = $row["firstname"] . " " . $row["lastname"];
      $approvedtime = $row["approvedtime"];
      ${"day" . $row["dof"] . "start"} = substr($row["daystart"],0,-3);
      ${"day" . $row["dof"] . "stop"} = substr($row["daystop"],0,-3);
      ${"day" . $row["dof"] . "break1start"} = substr($row["break1start"],0,-3);
      ${"day" . $row["dof"] . "break1stop"} = substr($row["break1stop"],0,-3);
      ${"day" . $row["dof"] . "break2start"} = substr($row["break2start"],0,-3);
      ${"day" . $row["dof"] . "break2stop"} = substr($row["break2stop"],0,-3);
      ${"day" . $row["dof"] . "break3start"} = substr($row["break3start"],0,-3);
      ${"day" . $row["dof"] . "break3stop"} = substr($row["break3stop"],0,-3);
      ${"day" . $row["dof"] . "oncall"} = $row["oncall"];
      ${"day" . $row["dof"] . "lvannual"} = substr($row["lvannual"],0,-3);
      ${"day" . $row["dof"] . "lvsick"} = substr($row["lvsick"],0,-3);
      ${"day" . $row["dof"] . "lvtoil"} = substr($row["lvtoil"],0,-3);
      ${"day" . $row["dof"] . "lvphcon"} = substr($row["lvphcon"],0,-3);
      ${"day" . $row["dof"] . "lvflex"} = substr($row["lvflex"],0,-3);
      ${"day" . $row["dof"] . "oc1start"} = substr($row["oc1start"],0,-3);
      ${"day" . $row["dof"] . "oc1stop"} = substr($row["oc1stop"],0,-3);
      ${"day" . $row["dof"] . "oc2start"} = substr($row["oc2start"],0,-3);
      ${"day" . $row["dof"] . "oc2stop"} = substr($row["oc2stop"],0,-3);
      ${"day" . $row["dof"] . "oc3start"} = substr($row["oc3start"],0,-3);
      ${"day" . $row["dof"] . "oc3stop"} = substr($row["oc3stop"],0,-3);
      ${"day" . $row["dof"] . "ot1start"} = substr($row["ot1start"],0,-3);
      ${"day" . $row["dof"] . "ot1stop"} = substr($row["ot1stop"],0,-3);
      ${"day" . $row["dof"] . "ot2start"} = substr($row["ot2start"],0,-3);
      ${"day" . $row["dof"] . "ot2stop"} = substr($row["ot2stop"],0,-3);
      ${"day" . $row["dof"] . "ot3start"} = substr($row["ot3start"],0,-3);
      ${"day" . $row["dof"] . "ot3stop"} = substr($row["ot3stop"],0,-3);
      ${"day" . $row["dof"] . "toil1start"} = substr($row["toil1start"],0,-3);
      ${"day" . $row["dof"] . "toil1stop"} = substr($row["toil1stop"],0,-3);
      ${"day" . $row["dof"] . "toil2start"} = substr($row["toil2start"],0,-3);
      ${"day" . $row["dof"] . "toil2stop"} = substr($row["toil2stop"],0,-3);
      ${"day" . $row["dof"] . "toil3start"} = substr($row["toil3start"],0,-3);
      ${"day" . $row["dof"] . "toil3stop"} = substr($row["toil3stop"],0,-3);
  }

$html1 = '<link rel="stylesheet" href="timesheet.css">';
$html1 .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
$html1 .= '<input type="submit" value="Home" id="home" name="home" class="menubar">';
if ($loggedinuser != $novellname) {
  $html1 .= "<input type=\"submit\" value=\"Approve\" id=\"approve\" name=\"approve\" class=\"menubar\" onClick=\"return confirm('Approve this timesheet?')\">";
}
$html1 .= '<input type="submit" value="Logout" id="logout" name="logout" class="menubar">';
$html1 .= '<script src="./cleave.min.js"></script>';
$html2 = '<h1>'.$first.' '.$last.'</h1>';
$html2 .= '   <div style="position: absolute; top: 8px; left: 250px;" class="bordered">';
            if ($submitted != NULL) {
            $html2 .= "Submitted " . $submitted . "<br>";
            }
            // if ($loggedinuser != $novellname) {
            //   $html2 .= "<input type=\"text\" id=\"flexcb\" name=\"flexcb\">";
            //  $html2 .= "<input type=\"text\" id=\"toilcb\" name=\"toilcb\">";
            // }
            if (isset($approved)) {
              $html2 .= "Approved at " . $approvedtime . "<br>";
              $html2 .= "Approved by " . $approvedby . "<br>";
            }
$html2 .= '    </div>';

if ($_SERVER['PHP_SELF'] == "/timesheet-edit.php") {
    $mode = "edit";
} elseif ($_SERVER['PHP_SELF'] == "/timesheet-view.php") {
    $mode = "view";
}
// (expr1) ? (value if true) : (value if false)
$html2 .= "<table class=\"tg\">";
$html2 .= "<thead>";
$html2 .= "  <tr>";
$html2 .= "    <th class=\"tg-0lax\">Flex C/F: ".$flexcf."<br>TOIL C/F: ".$toilcf."</th>";
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(1);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -13 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(2);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -12 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(3);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -11 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(4);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -10 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(5);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -9 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(6);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c66k\">".date('D<\b\r>d-m', strtotime($fne . ' -8 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(7);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c66k\">".date('D<\b\r>d-m', strtotime($fne . ' -7 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(8);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -6 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(9);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -5 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(10);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -4 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(11);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -3 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(12);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c3ow\">".date('D<\b\r>d-m', strtotime($fne . ' -2 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(13);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c66k\">".date('D<\b\r>d-m', strtotime($fne . ' -1 day')) . $line;
$line = ($mode == "edit") ? "<br><input type=\"button\" value=\"[X]\" class=\"little\" onclick=\"clearDay(14);\"></th>" : "</th>";
$html2 .= "    <th class=\"tg-c66k\">".date('D<\b\r>d-m', strtotime($fne . ' -0 day')) . $line;
$html2 .= "  </tr>";
$html2 .= "</thead>";
$html2 .= "<tbody>";

// this function writes a table row. If it's a regular time, like day1start, day2breakstart etc, or if it
// has a value, it'll display, otherwise it's hidden by default. 
  function writeRow2($bg,$linename,$shortdesc,$a,$b,$initialvalue = NULL) {
    $x = 0;
    global $day1start,$day2start,$day3start,$day4start,$day5start,$day6start,$day7start,$day8start,$day9start,$day10start,$day11start,$day12start,$day13start,$day14start;
    global $day1stop,$day2stop,$day3stop,$day4stop,$day5stop,$day6stop,$day7stop,$day8stop,$day9stop,$day10stop,$day11stop,$day12stop,$day13stop,$day14stop;
    global $day1break1start,$day2break1start,$day3break1start,$day4break1start,$day5break1start,$day6break1start,$day7break1start,$day8break1start,$day9break1start,$day10break1start,$day11break1start,$day12break1start,$day13break1start,$day14break1start;
    global $day1break1stop,$day2break1stop,$day3break1stop,$day4break1stop,$day5break1stop,$day6break1stop,$day7break1stop,$day8break1stop,$day9break1stop,$day10break1stop,$day11break1stop,$day12break1stop,$day13break1stop,$day14break1stop;
    global $day1break2start,$day2break2start,$day3break2start,$day4break2start,$day5break2start,$day6break2start,$day7break2start,$day8break2start,$day9break2start,$day10break2start,$day11break2start,$day12break2start,$day13break2start,$day14break2start;
    global $day1break2stop,$day2break2stop,$day3break2stop,$day4break2stop,$day5break2stop,$day6break2stop,$day7break2stop,$day8break2stop,$day9break2stop,$day10break2stop,$day11break2stop,$day12break2stop,$day13break2stop,$day14break2stop;
    global $day1break3start,$day2break3start,$day3break3start,$day4break3start,$day5break3start,$day6break3start,$day7break3start,$day8break3start,$day9break3start,$day10break3start,$day11break3start,$day12break3start,$day13break3start,$day14break3start;
    global $day1break3stop,$day2break3stop,$day3break3stop,$day4break3stop,$day5break3stop,$day6break3stop,$day7break3stop,$day8break3stop,$day9break3stop,$day10break3stop,$day11break3stop,$day12break3stop,$day13break3stop,$day14break3stop;

    $alwaysshow = array("day1start","day2start","day3start","day4start","day5start","day6start","day7start","day8start","day9start","day10start","day11start","day12start","day13start","day14start");
    array_push($alwaysshow,"day1stop","day2stop","day3stop","day4stop","day5stop","day6stop","day7stop","day8stop","day9stop","day10stop","day11stop","day12stop","day13stop","day14stop");
    array_push($alwaysshow,"day1break1start","day2break1start","day3break1start","day4break1start","day5break1start","day6break1start","day7break1start","day8break1start","day9break1start","day10break1start","day11break1start","day12break1start","day13break1start","day14break1start");
    array_push($alwaysshow,"day1break1stop","day2break1stop","day3break1stop","day4break1stop","day5break1stop","day6break1stop","day7break1stop","day8break1stop","day9break1stop","day10break1stop","day11break1stop","day12break1stop","day13break1stop","day14break1stop");
    array_push($alwaysshow,"day1break2start","day2break2start","day3break2start","day4break2start","day5break2start","day6break2start","day7break2start","day8break2start","day9break2start","day10break2start","day11break2start","day12break2start","day13break2start","day14break2start");
    array_push($alwaysshow,"day1break2stop","day2break2stop","day3break2stop","day4break2stop","day5break2stop","day6break2stop","day7break2stop","day8break2stop","day9break2stop","day10break2stop","day11break2stop","day12break2stop","day13break2stop","day14break2stop");
    array_push($alwaysshow,"day1break3start","day2break3start","day3break3start","day4break3start","day5break3start","day6break3start","day7break3start","day8break3start","day9break3start","day10break3start","day11break3start","day12break3start","day13break3start","day14break3start");
    array_push($alwaysshow,"day1break3stop","day2break3stop","day3break3stop","day4break3stop","day5break3stop","day6break3stop","day7break3stop","day8break3stop","day9break3stop","day10break3stop","day11break3stop","day12break3stop","day13break3stop","day14break3stop");
    array_push($alwaysshow,"flexobal","tota","fulltime","flextaken","flexearned","flexcbal");

    global $day1lvannual,$day2lvannual,$day3lvannual,$day4lvannual,$day5lvannual,$day6lvannual,$day7lvannual,$day8lvannual,$day9lvannual,$day10lvannual,$day11lvannual,$day12lvannual,$day13lvannual,$day14lvannual;
    global $day1lvsick,$day2lvsick,$day3lvsick,$day4lvsick,$day5lvsick,$day6lvsick,$day7lvsick,$day8lvsick,$day9lvsick,$day10lvsick,$day11lvsick,$day12lvsick,$day13lvsick,$day14lvsick;
    global $day1lvtoil,$day2lvtoil,$day3lvtoil,$day4lvtoil,$day5lvtoil,$day6lvtoil,$day7lvtoil,$day8lvtoil,$day9lvtoil,$day10lvtoil,$day11lvtoil,$day12lvtoil,$day13lvtoil,$day14lvtoil;
    global $day1lvphcon,$day2lvphcon,$day3lvphcon,$day4lvphcon,$day5lvphcon,$day6lvphcon,$day7lvphcon,$day8lvphcon,$day9lvphcon,$day10lvphcon,$day11lvphcon,$day12lvphcon,$day13lvphcon,$day14lvphcon;
    global $day1lvflex,$day2lvflex,$day3lvflex,$day4lvflex,$day5lvflex,$day6lvflex,$day7lvflex,$day8lvflex,$day9lvflex,$day10lvflex,$day11lvflex,$day12lvflex,$day13lvflex,$day14lvflex;
    global $day1oc1start,$day2oc1start,$day3oc1start,$day4oc1start,$day5oc1start,$day6oc1start,$day7oc1start,$day8oc1start,$day9oc1start,$day10oc1start,$day11oc1start,$day12oc1start,$day13oc1start,$day14oc1start;
    global $day1oc1stop,$day2oc1stop,$day3oc1stop,$day4oc1stop,$day5oc1stop,$day6oc1stop,$day7oc1stop,$day8oc1stop,$day9oc1stop,$day10oc1stop,$day11oc1stop,$day12oc1stop,$day13oc1stop,$day14oc1stop;
    global $day1oc2start,$day2oc2start,$day3oc2start,$day4oc2start,$day5oc2start,$day6oc2start,$day7oc2start,$day8oc2start,$day9oc2start,$day10oc2start,$day11oc2start,$day12oc2start,$day13oc2start,$day14oc2start;
    global $day1oc2stop,$day2oc2stop,$day3oc2stop,$day4oc2stop,$day5oc2stop,$day6oc2stop,$day7oc2stop,$day8oc2stop,$day9oc2stop,$day10oc2stop,$day11oc2stop,$day12oc2stop,$day13oc2stop,$day14oc2stop;
    global $day1oc3start,$day2oc3start,$day3oc3start,$day4oc3start,$day5oc3start,$day6oc3start,$day7oc3start,$day8oc3start,$day9oc3start,$day10oc3start,$day11oc3start,$day12oc3start,$day13oc3start,$day14oc3start;
    global $day1oc3stop,$day2oc3stop,$day3oc3stop,$day4oc3stop,$day5oc3stop,$day6oc3stop,$day7oc3stop,$day8oc3stop,$day9oc3stop,$day10oc3stop,$day11oc3stop,$day12oc3stop,$day13oc3stop,$day14oc3stop;
    global $day1ot1start,$day2ot1start,$day3ot1start,$day4ot1start,$day5ot1start,$day6ot1start,$day7ot1start,$day8ot1start,$day9ot1start,$day10ot1start,$day11ot1start,$day12ot1start,$day13ot1start,$day14ot1start;
    global $day1ot1stop,$day2ot1stop,$day3ot1stop,$day4ot1stop,$day5ot1stop,$day6ot1stop,$day7ot1stop,$day8ot1stop,$day9ot1stop,$day10ot1stop,$day11ot1stop,$day12ot1stop,$day13ot1stop,$day14ot1stop;
    global $day1ot2start,$day2ot2start,$day3ot2start,$day4ot2start,$day5ot2start,$day6ot2start,$day7ot2start,$day8ot2start,$day9ot2start,$day10ot2start,$day11ot2start,$day12ot2start,$day13ot2start,$day14ot2start;
    global $day1ot2stop,$day2ot2stop,$day3ot2stop,$day4ot2stop,$day5ot2stop,$day6ot2stop,$day7ot2stop,$day8ot2stop,$day9ot2stop,$day10ot2stop,$day11ot2stop,$day12ot2stop,$day13ot2stop,$day14ot2stop;
    global $day1ot3start,$day2ot3start,$day3ot3start,$day4ot3start,$day5ot3start,$day6ot3start,$day7ot3start,$day8ot3start,$day9ot3start,$day10ot3start,$day11ot3start,$day12ot3start,$day13ot3start,$day14ot3start;
    global $day1ot3stop,$day2ot3stop,$day3ot3stop,$day4ot3stop,$day5ot3stop,$day6ot3stop,$day7ot3stop,$day8ot3stop,$day9ot3stop,$day10ot3stop,$day11ot3stop,$day12ot3stop,$day13ot3stop,$day14ot3stop;
    global $day1toil1start,$day2toil1start,$day3toil1start,$day4toil1start,$day5toil1start,$day6toil1start,$day7toil1start,$day8toil1start,$day9toil1start,$day10toil1start,$day11toil1start,$day12toil1start,$day13toil1start,$day14toil1start;
    global $day1toil1stop,$day2toil1stop,$day3toil1stop,$day4toil1stop,$day5toil1stop,$day6toil1stop,$day7toil1stop,$day8toil1stop,$day9toil1stop,$day10toil1stop,$day11toil1stop,$day12toil1stop,$day13toil1stop,$day14toil1stop;
    global $day1toil2start,$day2toil2start,$day3toil2start,$day4toil2start,$day5toil2start,$day6toil2start,$day7toil2start,$day8toil2start,$day9toil2start,$day10toil2start,$day11toil2start,$day12toil2start,$day13toil2start,$day14toil2start;
    global $day1toil2stop,$day2toil2stop,$day3toil2stop,$day4toil2stop,$day5toil2stop,$day6toil2stop,$day7toil2stop,$day8toil2stop,$day9toil2stop,$day10toil2stop,$day11toil2stop,$day12toil2stop,$day13toil2stop,$day14toil2stop;
    global $day1toil3start,$day2toil3start,$day3toil3start,$day4toil3start,$day5toil3start,$day6toil3start,$day7toil3start,$day8toil3start,$day9toil3start,$day10toil3start,$day11toil3start,$day12toil3start,$day13toil3start,$day14toil3start;
    global $day1toil3stop,$day2toil3stop,$day3toil3stop,$day4toil3stop,$day5toil3stop,$day6toil3stop,$day7toil3stop,$day8toil3stop,$day9toil3stop,$day10toil3stop,$day11toil3stop,$day12toil3stop,$day13toil3stop,$day14toil3stop;

    $day1flexobal = $day2flexobal = $day3flexobal = $day4flexobal = $day5flexobal = $day6flexobal = $day7flexobal = $day8flexobal = $day9flexobal = $day10flexobal = $day11flexobal = $day12flexobal = $day13flexobal = $day14flexobal = NULL;
    $day1tota = $day2tota = $day3tota = $day4tota = $day5tota = $day6tota = $day7tota = $day8tota = $day9tota = $day10tota = $day11tota = $day12tota = $day13tota = $day14tota = NULL;
    $day1fulltime = $day2fulltime = $day3fulltime = $day4fulltime = $day5fulltime = $day6fulltime = $day7fulltime = $day8fulltime = $day9fulltime = $day10fulltime = $day11fulltime = $day12fulltime = $day13fulltime = $day14fulltime = NULL;
    $day1flextaken = $day2flextaken = $day3flextaken = $day4flextaken = $day5flextaken = $day6flextaken = $day7flextaken = $day8flextaken = $day9flextaken = $day10flextaken = $day11flextaken = $day12flextaken = $day13flextaken = $day14flextaken = NULL;
    $day1flexearned = $day2flexearned = $day3flexearned = $day4flexearned = $day5flexearned = $day6flexearned = $day7flexearned = $day8flexearned = $day9flexearned = $day10flexearned = $day11flexearned = $day12flexearned = $day13flexearned = $day14flexearned = NULL;
    $day1flexcbal = $day2flexcbal = $day3flexcbal = $day4flexcbal = $day5flexcbal = $day6flexcbal = $day7flexcbal = $day8flexcbal = $day9flexcbal = $day10flexcbal = $day11flexcbal = $day12flexcbal = $day13flexcbal = $day14flexcbal = NULL;
    
    $day1lvtot = $day2lvtot = $day3lvtot = $day4lvtot = $day5lvtot = $day6lvtot = $day7lvtot = $day8lvtot = $day9lvtot = $day10lvtot = $day11lvtot = $day12lvtot = $day13lvtot = $day14lvtot = NULL;
    $day1octot = $day2octot = $day3octot = $day4octot = $day5octot = $day6octot = $day7octot = $day8octot = $day9octot = $day10octot = $day11octot = $day12octot = $day13octot = $day14octot = NULL;
    $day1ottot = $day2ottot = $day3ottot = $day4ottot = $day5ottot = $day6ottot = $day7ottot = $day8ottot = $day9ottot = $day10ottot = $day11ottot = $day12ottot = $day13ottot = $day14ottot = NULL;
    $day1toiltot = $day2toiltot = $day3toiltot = $day4toiltot = $day5toiltot = $day6toiltot = $day7toiltot = $day8toiltot = $day9toiltot = $day10toiltot = $day11toiltot = $day12toiltot = $day13toiltot = $day14toiltot = NULL;
    $day1toilobal = $day2toilobal = $day3toilobal = $day4toilobal = $day5toilobal = $day6toilobal = $day7toilobal = $day8toilobal = $day9toilobal = $day10toilobal = $day11toilobal = $day12toilobal = $day13toilobal = $day14toilobal = NULL;
    $day1toiltkn = $day2toiltkn = $day3toiltkn = $day4toiltkn = $day5toiltkn = $day6toiltkn = $day7toiltkn = $day8toiltkn = $day9toiltkn = $day10toiltkn = $day11toiltkn = $day12toiltkn = $day13toiltkn = $day14toiltkn = NULL;
    $day1toilcbal = $day2toilcbal = $day3toilcbal = $day4toilcbal = $day5toilcbal = $day6toilcbal = $day7toilcbal = $day8toilcbal = $day9toilcbal = $day10toilcbal = $day11toilcbal = $day12toilcbal = $day13toilcbal = $day14toilcbal = NULL;


    global $mode, $html2;
// lvday1phcon
    $status = ($mode == "view") ? "readonly" : "";
    for ($x = 1; $x <= 14; $x++) {
      if ((${"day".$x.$shortdesc} != NULL) || (in_array("day".$x.$shortdesc,$alwaysshow)) || isset($initialvalue)) {
        $style = "show";
      }
    }
    if ($a == 1 && isset($style)) {
      $html2 .= "  <tr id=\"".$shortdesc."\" style=\"display: table-row;\">\n";
      $html2 .= "    <td class=\"$bg\">$linename</td>\n";
    } elseif ($a == 1 && !isset($style)) {
      $html2 .= "  <tr id=\"".$shortdesc."\" style=\"display: none;\">\n";
      $html2 .= "    <td class=\"$bg\">$linename</td>\n";
    }
    for ($x = $a; $x <= $b; $x++) {
      if (isset($initialvalue)) {
        $html2 .= "    <td class=\"$bg\"><p id=\"day".$x.$shortdesc."\" name=\"day".$x.$shortdesc."\">$initialvalue</p></td>\n";
      } else {
        $html2 .= "    <td class=\"$bg\"><input type=\"text\" class=\"input-time\" id=\"day".$x.$shortdesc."\" name=\"day".$x.$shortdesc."\" value=\"".${"day".$x.$shortdesc}."\" onchange=\"myInit()\" ".$status."></td>\n";
      }
    }
    if ($b == 14) {
      $html2 .= "  </tr>\n";
    }
  }

  // calculate the carry-forward balances of flex and toil
  // (expr1) ? (value if true) : (value if false)
  $flexm = preg_match('/^[\d:\+]+$/',$flexcf) ? 1 : -1;
  $time   = explode(":", $flexcf);
  $hour   = abs($time[0]) * 60 * 60 * 1000;
  $minute = abs($time[1]) * 60 * 1000;
  $flexcf = ($hour + $minute) * $flexm;

  $toilm = preg_match('/^[\d:\+]+$/',$toilcf) ? 1 : -1;
  $time   = explode(":", $toilcf);  
  $hour   = abs($time[0]) * 60 * 60 * 1000;
  $minute = abs($time[1]) * 60 * 1000;
  $toilcf = ($hour + $minute) * $toilm;

  writeRow2("tg-0lax","Day Start","start",1,5);
  writeRow2("tg-266k","Day Start","start",6,7);
  writeRow2("tg-0lax","Day Start","start",8,12);
  writeRow2("tg-266k","Day Start","start",13,14);
  writeRow2("tg-7od5","Break1 Start","break1start",1,5);
  writeRow2("tg-266k","Break1 Start","break1start",6,7);
  writeRow2("tg-7od5","Break1 Start","break1start",8,12);
  writeRow2("tg-266k","Break1 Start","break1start",13,14);
  writeRow2("tg-7od5","Break1 Stop","break1stop",1,5);
  writeRow2("tg-266k","Break1 Stop","break1stop",6,7);
  writeRow2("tg-7od5","Break1 Stop","break1stop",8,12);
  writeRow2("tg-266k","Break1 Stop","break1stop",13,14);
  writeRow2("tg-ncd7","Break2 Start","break2start",1,5);
  writeRow2("tg-266k","Break2 Start","break2start",6,7);
  writeRow2("tg-ncd7","Break2 Start","break2start",8,12);
  writeRow2("tg-266k","Break2 Start","break2start",13,14);
  writeRow2("tg-ncd7","Break2 Stop","break2stop",1,5);
  writeRow2("tg-266k","Break2 Stop","break2stop",6,7);
  writeRow2("tg-ncd7","Break2 Stop","break2stop",8,12);
  writeRow2("tg-266k","Break2 Stop","break2stop",13,14);
  writeRow2("tg-7od5","Break3 Start","break3start",1,5);
  writeRow2("tg-266k","Break3 Start","break3start",6,7);
  writeRow2("tg-7od5","Break3 Start","break3start",8,12);
  writeRow2("tg-266k","Break3 Start","break3start",13,14);
  writeRow2("tg-7od5","Break3 Stop","break3stop",1,5);
  writeRow2("tg-266k","Break3 Stop","break3stop",6,7);
  writeRow2("tg-7od5","Break3 Stop","break3stop",8,12);
  writeRow2("tg-266k","Break3 Stop","break3stop",13,14);
  writeRow2("tg-0pky","Day Stop","stop",1,5);
  writeRow2("tg-266k","Day Stop","stop",6,7);
  writeRow2("tg-0pky","Day Stop","stop",8,12);
  writeRow2("tg-266k","Day Stop","stop",13,14);
  $html2 .= "<tr>";
  $html2 .= "  <td class=\"tg-7od5\">Total</td>";
  for ($x = 1; $x <= 14; $x++) {
    $html2 .= "<td class=\"tg-7od5\"><p id=\"day".$x."tot\">00:00</p></td>";
  }
  $html2 .= "</tr>";
  $html2 .= "<tr>";
  $html2 .= "<td class=\"tg-0pky\">Oncall</td>";
  if ($mode == "edit") {
  for ($x = 1; $x <= 14; $x++) {
    $html2 .= "<td class=\"tg-0lax\">";
    $html2 .= "<input type=\"radio\" id=\"day".$x."oc_no\" name=\"day".$x."oc\" value=\"No\" ";
      if (${"day".$x."oncall"} == NULL) {
        $html2 .= "checked";
      }
    $html2 .= ">No<br>";
    
    $html2 .= "<input type=\"radio\" id=\"day".$x."oc_onight\" name=\"day".$x."oc\" value=\"On\" ";
    if (${"day".$x."oncall"} == 1) {
      $html2 .= "checked";
    }    
    $html2 .= ">O/N<br>";
    
    $html2 .= "<input type=\"radio\" id=\"day".$x."oc_247\" name=\"day".$x."oc\" value=\"24\" ";
    if (${"day".$x."oncall"} == 2) {
      $html2 .= "checked";
    }   
    $html2 .= ">24/7<br>";
  }
 } elseif ($mode == "view") {
    for ($x = 1; $x <= 14; $x++) {
      $html2 .= "<td class=\"tg-0lax\">";
        if (${"day".$x."oncall"} == NULL) {
          $html2 .= "No";
        }
      if (${"day".$x."oncall"} == 1) {
        $html2 .= "O/N";
      }    
      if (${"day".$x."oncall"} == 2) {
        $html2 .= "24/7";
      }   
      $html2 .= "</td>";
    }
  }
  $html2 .= "</tr>";

  $html2 .= "<tr><td colspan=\"15\">";
  $leavetoggle = ($mode == "edit") ? "<input type=\"button\" value=\"Toggle Leave\" class=\"little\" onclick=\"togglevis('lv')\">" : "";
  $html2 .= $leavetoggle;
  $html2 .= "</td></tr>";
  writeRow2("tg-ncd7","Annual Leave","lvannual",1,5);
  writeRow2("tg-266k","Annual Leave","lvannual",6,7);
  writeRow2("tg-ncd7","Annual Leave","lvannual",8,12);
  writeRow2("tg-266k","Annual Leave","lvannual",13,14);
  writeRow2("tg-ncd7","Sick Leave","lvsick",1,5);
  writeRow2("tg-266k","Sick Leave","lvsick",6,7);
  writeRow2("tg-ncd7","Sick Leave","lvsick",8,12);
  writeRow2("tg-266k","Sick Leave","lvsick",13,14);
  writeRow2("tg-ncd7","TOIL Taken","lvtoil",1,5);
  writeRow2("tg-266k","TOIL Taken","lvtoil",6,7);
  writeRow2("tg-ncd7","TOIL Taken","lvtoil",8,12);
  writeRow2("tg-266k","TOIL Taken","lvtoil",13,14);
  writeRow2("tg-ncd7","PH/Concessional","lvphcon",1,5);
  writeRow2("tg-266k","PH/Concessional","lvphcon",6,7);
  writeRow2("tg-ncd7","PH/Concessional","lvphcon",8,12);
  writeRow2("tg-266k","PH/Concessional","lvphcon",13,14);
  writeRow2("tg-ncd7","Flex Taken","lvflex",1,5);
  writeRow2("tg-266k","Flex Taken","lvflex",6,7);
  writeRow2("tg-ncd7","Flex Taken","lvflex",8,12);
  writeRow2("tg-266k","Flex Taken","lvflex",13,14);
  writeRow2("tg-7od5","Leave Total","lvtot",1,14,"00:00");
  $html2 .= "<tr><td colspan=\"15\">";
  $leavetoggle = ($mode == "edit") ? "<input type=\"button\" value=\"Toggle Oncall\" class=\"little\" onclick=\"togglevis('oc')\">" : "";
  $html2 .= $leavetoggle;
  $html2 .= "</td></tr>";
  writeRow2("tg-7od5","OC1 Start","oc1start",1,5);
  writeRow2("tg-266k","OC1 Start","oc1start",6,7);
  writeRow2("tg-7od5","OC1 Start","oc1start",8,12);
  writeRow2("tg-266k","OC1 Start","oc1start",13,14);
  writeRow2("tg-7od5","OC1 Stop","oc1stop",1,5);
  writeRow2("tg-266k","OC1 Stop","oc1stop",6,7);
  writeRow2("tg-7od5","OC1 Stop","oc1stop",8,12);
  writeRow2("tg-266k","OC1 Stop","oc1stop",13,14);
  writeRow2("tg-ncd7","OC2 Start","oc2start",1,5);
  writeRow2("tg-266k","OC2 Start","oc2start",6,7);
  writeRow2("tg-ncd7","OC2 Start","oc2start",8,12);
  writeRow2("tg-266k","OC2 Start","oc2start",13,14);
  writeRow2("tg-ncd7","OC2 Stop","oc2stop",1,5);
  writeRow2("tg-266k","OC2 Stop","oc2stop",6,7);
  writeRow2("tg-ncd7","OC2 Stop","oc2stop",8,12);
  writeRow2("tg-266k","OC2 Stop","oc2stop",13,14);
  writeRow2("tg-7od5","OC3 Start","oc3start",1,5);
  writeRow2("tg-266k","OC3 Start","oc3start",6,7);
  writeRow2("tg-7od5","OC3 Start","oc3start",8,12);
  writeRow2("tg-266k","OC3 Start","oc3start",13,14);
  writeRow2("tg-7od5","OC3 Stop","oc3stop",1,5);
  writeRow2("tg-266k","OC3 Stop","oc3stop",6,7);
  writeRow2("tg-7od5","OC3 Stop","oc3stop",8,12);
  writeRow2("tg-266k","OC3 Stop","oc3stop",13,14);
  writeRow2("tg-7od5","Oncall Total","octot",1,14,"00:00");
  $html2 .= "<tr><td colspan=\"15\">";
  $leavetoggle = ($mode == "edit") ? "<input type=\"button\" value=\"Toggle OT\" class=\"little\" onclick=\"togglevis('ot')\">" : "";
  $html2 .= $leavetoggle;
  $html2 .= "</td></tr>";
  writeRow2("tg-7od5","OT1 Start","ot1start",1,5);
  writeRow2("tg-266k","OT1 Start","ot1start",6,7);
  writeRow2("tg-7od5","OT1 Start","ot1start",8,12);
  writeRow2("tg-266k","OT1 Start","ot1start",13,14);
  writeRow2("tg-7od5","OT1 Stop","ot1stop",1,5);
  writeRow2("tg-266k","OT1 Stop","ot1stop",6,7);
  writeRow2("tg-7od5","OT1 Stop","ot1stop",8,12);
  writeRow2("tg-266k","OT1 Stop","ot1stop",13,14);
  writeRow2("tg-ncd7","OT2 Start","ot2start",1,5);
  writeRow2("tg-266k","OT2 Start","ot2start",6,7);
  writeRow2("tg-ncd7","OT2 Start","ot2start",8,12);
  writeRow2("tg-266k","OT2 Start","ot2start",13,14);
  writeRow2("tg-ncd7","OT2 Stop","ot2stop",1,5);
  writeRow2("tg-266k","OT2 Stop","ot2stop",6,7);
  writeRow2("tg-ncd7","OT2 Stop","ot2stop",8,12);
  writeRow2("tg-266k","OT2 Stop","ot2stop",13,14);
  writeRow2("tg-7od5","OT3 Start","ot3start",1,5);
  writeRow2("tg-266k","OT3 Start","ot3start",6,7);
  writeRow2("tg-7od5","OT3 Start","ot3start",8,12);
  writeRow2("tg-266k","OT3 Start","ot3start",13,14);
  writeRow2("tg-7od5","OT3 Stop","ot3stop",1,5);
  writeRow2("tg-266k","OT3 Stop","ot3stop",6,7);
  writeRow2("tg-7od5","OT3 Stop","ot3stop",8,12);
  writeRow2("tg-266k","OT3 Stop","ot3stop",13,14);
  writeRow2("tg-7od5","OT Total","ottot",1,14,"00:00");
  $html2 .= "<tr><td colspan=\"15\">";
  $leavetoggle = ($mode == "edit") ? "<input type=\"button\" value=\"Toggle TOIL\" class=\"little\" onclick=\"togglevis('toil')\">" : "";
  $html2 .= $leavetoggle;
  $html2 .= "</td></tr>";
  writeRow2("tg-7od5","TOIL1 Start","toil1start",1,5);
  writeRow2("tg-266k","TOIL1 Start","toil1start",6,7);
  writeRow2("tg-7od5","TOIL1 Start","toil1start",8,12);
  writeRow2("tg-266k","TOIL1 Start","toil1start",13,14);
  writeRow2("tg-7od5","TOIL1 Stop","toil1stop",1,5);
  writeRow2("tg-266k","TOIL1 Stop","toil1stop",6,7);
  writeRow2("tg-7od5","TOIL1 Stop","toil1stop",8,12);
  writeRow2("tg-266k","TOIL1 Stop","toil1stop",13,14);
  writeRow2("tg-ncd7","TOIL2 Start","toil2start",1,5);
  writeRow2("tg-266k","TOIL2 Start","toil2start",6,7);
  writeRow2("tg-ncd7","TOIL2 Start","toil2start",8,12);
  writeRow2("tg-266k","TOIL2 Start","toil2start",13,14);
  writeRow2("tg-ncd7","TOIL2 Stop","toil2stop",1,5);
  writeRow2("tg-266k","TOIL2 Stop","toil2stop",6,7);
  writeRow2("tg-ncd7","TOIL2 Stop","toil2stop",8,12);
  writeRow2("tg-266k","TOIL2 Stop","toil2stop",13,14);
  writeRow2("tg-7od5","TOIL3 Start","toil3start",1,5);
  writeRow2("tg-266k","TOIL3 Start","toil3start",6,7);
  writeRow2("tg-7od5","TOIL3 Start","toil3start",8,12);
  writeRow2("tg-266k","TOIL3 Start","toil3start",13,14);
  writeRow2("tg-7od5","TOIL3 Stop","toil3stop",1,5);
  writeRow2("tg-266k","TOIL3 Stop","toil3stop",6,7);
  writeRow2("tg-7od5","TOIL3 Stop","toil3stop",8,12);
  writeRow2("tg-266k","TOIL3 Stop","toil3stop",13,14);
  writeRow2("tg-0lax","TOIL Earned","toiltot",1,14,"00:00");
  writeRow2("tg-ncd7","TOIL Opening Balance","toilobal",1,14,"00:00");
  writeRow2("tg-0lax","TOIL Taken","toiltkn",1,14,"00:00");
  writeRow2("tg-ncd7","TOIL Closing Balance","toilcbal",1,14,"00:00");
  $html2 .= "<tr>";
  $html2 .= "  <td colspan=\"15\">";
  $html2 .= "    <div id=\"day0flexcbal\" style=\"display: none;\">" . $flexcf . "</div>";
  $html2 .= "    <div id=\"day0toilcbal\" style=\"display: none;\">" . $toilcf . "</div>";
  $html2 .= "  </td>";
  $html2 .= "</tr>";
  writeRow2("tg-7od5","Flex Opening Balance","flexobal",1,14,"00:00");
  writeRow2("tg-ncd7","Ordinary Hours","tota",1,14,"00:00");
  writeRow2("tg-ncd7","Full Time Std","fulltime",1,5,"07:36");
  writeRow2("tg-ncd7","Full Time Std","fulltime",6,7,"00:00");
  writeRow2("tg-ncd7","Full Time Std","fulltime",8,12,"07:36");
  writeRow2("tg-ncd7","Full Time Std","fulltime",13,14,"00:00");
  writeRow2("tg-7od5","Flex Hours Taken","flextaken",1,14,"00:00");
  writeRow2("tg-ncd7","Flex +/- Worked","flexearned",1,14,"00:00");
  writeRow2("tg-7od5","Flex Closing Balance","flexcbal",1,14,"00:00");
$html2 .= "</tbody>";
$html2 .= "</table>";

  // to do:  based on a variables passed by form, this is where we'll create the timesheet for the fortnight end date

echo $html1.$html2.'</form>';

$css = '<style>
.bordered {
  border: 3px solid #f1f1f1;
  margin-block-end: 1em;
}

.tg  {
  border-collapse:collapse;
  border-spacing:0;
}
.tg td{
  border-color:black;
  border-style:solid;
  border-width:1px;
  font-size:14px;
  overflow:hidden;
  /* padding:10px 5px; */
  word-break:normal;
}
.tg th{
  border-color:black;
  border-style:solid;
  border-width:1px;
  font-family:Arial, sans-serif;
  font-size:14px;
  font-weight:normal;
  overflow:hidden;
  /* padding:10px 5px; */
  word-break:normal;
}
.tg .tg-266k{
  background-color:#9b9b9b;
  border-color:inherit;
  text-align:left;
  vertical-align:top
}
.tg .tg-7od5{
  background-color:#9aff99;
  border-color:inherit;
  text-align:left;
  vertical-align:top
}
.tg .tg-ncd7{
  background-color:#ffffc7;
  border-color:inherit;
  text-align:left;
  vertical-align:top
}
.tg .tg-0lax{
  text-align:left;
  vertical-align:top
}
.tg .tg-c3ow{
  border-color:inherit;
  text-align:center;
  vertical-align:top
}
.tg .tg-c66k{
  background-color:#9b9b9b;
  border-color:inherit;
  text-align:center;
  vertical-align:top
}
.input-time {
  width: 5em;
  padding: 0px 0px;
  margin: 0px 0px;
}</style>';

if(!empty($_POST["approve"]) && $output == "true") {
  $mail = new PHPMailer(true);
  try {
      //Server settings
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = 'mailhog';                              //Set the SMTP server to send through
      $mail->Port       = 1025;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
      $mail->setFrom($superemail, $superfirst.' '.$superlast);
      $mail->addAddress($employemail, $first.' '.$last);     //Add a recipient

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'Approved Timesheet';
      $mail->Body = $css.$html2;

      $mail->send();
      echo 'Message has been sent';
  } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

?>
<script>
window.addEventListener("load", myInit, true); 
  function myInit(){  // call your functions here.... 
    var values = calcday(1,parseInt(document.getElementById("day0flexcbal").innerHTML),parseInt(document.getElementById("day0toilcbal").innerHTML));
    var values = calcday(2,values[0],values[1]);
    var values = calcday(3,values[0],values[1]);
    var values = calcday(4,values[0],values[1]);
    var values = calcday(5,values[0],values[1]);
    var values = calcday(6,values[0],values[1]);
    var values = calcday(7,values[0],values[1]);
    var values = calcday(8,values[0],values[1]);
    var values = calcday(9,values[0],values[1]);
    var values = calcday(10,values[0],values[1]);
    var values = calcday(11,values[0],values[1]);
    var values = calcday(12,values[0],values[1]);
    var values = calcday(13,values[0],values[1]);
    var values = calcday(14,values[0],values[1]);
    var flexcmult = values[0] >= 0 ? "+" : "-";
    var flexcHrs = Math.floor((Math.abs(values[0]) % 86400000) / 3600000); // hours
    var flexcMins = Math.round(((Math.abs(values[0]) % 86400000) % 3600000) / 60000); // minutes
    document.getElementById("flexcb").value = flexcmult + ('0000'+flexcHrs).slice(-2) + ":" + ('0000'+flexcMins).slice(-2) + ":00";
    var toilcmult = values[1] >= 0 ? "+" : "-";
    var toilcHrs = Math.floor((Math.abs(values[1]) % 86400000) / 3600000); // hours
    var toilcMins = Math.round(((Math.abs(values[1]) % 86400000) % 3600000) / 60000); // minutes
    document.getElementById("toilcb").value = toilcmult + ('0000'+toilcHrs).slice(-2) + ":" + ('0000'+toilcMins).slice(-2) + ":00";
  }; 


function calcday(x,flexopening,toilopening) {
  //  var x = condition ? exprIfTrue : exprIfFalse
  var dayxstart = document.getElementById("day" + x + "start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "start").value + ":00.000Z";
  var dayxstop  = document.getElementById("day" + x + "stop").value  == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "stop").value  + ":00.000Z";
  var dayxbreak1start = document.getElementById("day" + x + "break1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break1start").value + ":00.000Z";
  var dayxbreak1stop  = document.getElementById("day" + x + "break1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break1stop").value + ":00.000Z";
  var dayxbreak2start = document.getElementById("day" + x + "break2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break2start").value + ":00.000Z";
  var dayxbreak2stop  = document.getElementById("day" + x + "break2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break2stop").value + ":00.000Z";
  var dayxbreak3start = document.getElementById("day" + x + "break3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break3start").value + ":00.000Z";
  var dayxbreak3stop  = document.getElementById("day" + x + "break3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break3stop").value + ":00.000Z";
  break1 = (new Date(dayxbreak1stop) - new Date(dayxbreak1start))
  break2 = (new Date(dayxbreak2stop) - new Date(dayxbreak2start))   
  break3 = (new Date(dayxbreak3stop) - new Date(dayxbreak3start))   
  tot = (new Date(dayxstop) - new Date(dayxstart)) - (break1 + break2 + break3);
  maxhrs = Math.abs(((9*60)+00)*60000);
  tot = Math.min(tot, maxhrs);
  var diffHrs = Math.floor((tot % 86400000) / 3600000); // hours
  var diffMins = Math.round(((tot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "tot").innerHTML = ('0000'+diffHrs).slice(-2) + ":" + ('0000'+diffMins).slice(-2);
  document.getElementById("day" + x + "tota").innerHTML = ('0000'+diffHrs).slice(-2) + ":" + ('0000'+diffMins).slice(-2);



  // leave totals
  var lvtot = 0;
  var fltot = 0;
  var lvannual = document.getElementById("day" + x + "lvannual").value;
  var lvsick = document.getElementById("day" + x + "lvsick").value;
  var lvtoil = document.getElementById("day" + x + "lvtoil").value;
  var lvphcon = document.getElementById("day" + x + "lvphcon").value;
  var lvflex = document.getElementById("day" + x + "lvflex").value;

  fltothrs = Number(lvflex.slice(0,2));
  fltotmins = Number(lvflex.slice(-2));
  fltot = Math.abs(((fltothrs*60)+fltotmins)*60000); 
  var fltotalhrs = Math.floor((fltot % 86400000) / 3600000); // hours
  var fltotalmins = Math.round(((fltot % 86400000) % 3600000) / 60000); // minutes
  if (fltot > 0) {
    document.getElementById("day" + x + "flextaken").innerHTML = "-" + ('0000'+fltotalhrs).slice(-2) + ":" + ('0000'+fltotalmins).slice(-2);
  } else {
    document.getElementById("day" + x + "flextaken").innerHTML = ('0000'+fltotalhrs).slice(-2) + ":" + ('0000'+fltotalmins).slice(-2);
  }

  toiltakentothrs = Number(lvtoil.slice(0,2));
  toiltakentotmins = Number(lvtoil.slice(-2));
  toiltakentot = Math.abs(((toiltakentothrs*60)+toiltakentotmins)*60000); 
  var toiltakentotalhrs = Math.floor((toiltakentot % 86400000) / 3600000); // hours
  var toiltakentotalmins = Math.round(((toiltakentot % 86400000) % 3600000) / 60000); // minutes
  if (toiltakentot > 0) {
    document.getElementById("day" + x + "toiltkn").innerHTML = "-" + ('0000'+toiltakentotalhrs).slice(-2) + ":" + ('0000'+toiltakentotalmins).slice(-2);
  } else { 
    document.getElementById("day" + x + "toiltkn").innerHTML = ('0000'+toiltakentotalhrs).slice(-2) + ":" + ('0000'+toiltakentotalmins).slice(-2);
  }
  lvtothrs = Number(lvannual.slice(0,2)) + Number(lvsick.slice(0,2)) + Number(lvtoil.slice(0,2)) + Number(lvphcon.slice(0,2)) + Number(lvflex.slice(0,2));
  lvtotmins = Number(lvannual.slice(-2)) + Number(lvsick.slice(-2)) + Number(lvtoil.slice(-2)) + Number(lvphcon.slice(-2)) + Number(lvflex.slice(-2));
  lvtot = Math.abs(((lvtothrs*60)+lvtotmins)*60000); 
  var lvtotalhrs = Math.floor((lvtot % 86400000) / 3600000); // hours
  var lvtotalmins = Math.round(((lvtot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "lvtot").innerHTML = ('0000'+lvtotalhrs).slice(-2) + ":" + ('0000'+lvtotalmins).slice(-2);



  // oncall totals
  var dayxoc1start = document.getElementById("day" + x + "oc1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc1start").value + ":00.000Z";
  var dayxoc1stop  = document.getElementById("day" + x + "oc1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc1stop").value + ":00.000Z";
  var dayxoc2start = document.getElementById("day" + x + "oc2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc2start").value + ":00.000Z";
  var dayxoc2stop  = document.getElementById("day" + x + "oc2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc2stop").value + ":00.000Z";
  var dayxoc3start = document.getElementById("day" + x + "oc3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc3start").value + ":00.000Z";
  var dayxoc3stop  = document.getElementById("day" + x + "oc3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc3stop").value + ":00.000Z";
  oc1 = (new Date(dayxoc1stop) - new Date(dayxoc1start))
  oc2 = (new Date(dayxoc2stop) - new Date(dayxoc2start))   
  oc3 = (new Date(dayxoc3stop) - new Date(dayxoc3start))   
  octot = oc1 + oc2 + oc3;
  var ocdiffHrs = Math.floor((octot % 86400000) / 3600000); // hours
  var ocdiffMins = Math.round(((octot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "octot").innerHTML = ('0000'+ocdiffHrs).slice(-2) + ":" + ('0000'+ocdiffMins).slice(-2);



  // OT totals
  var dayxot1start = document.getElementById("day" + x + "ot1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot1start").value + ":00.000Z";
  var dayxot1stop  = document.getElementById("day" + x + "ot1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot1stop").value + ":00.000Z";
  var dayxot2start = document.getElementById("day" + x + "ot2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot2start").value + ":00.000Z";
  var dayxot2stop  = document.getElementById("day" + x + "ot2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot2stop").value + ":00.000Z";
  var dayxot3start = document.getElementById("day" + x + "ot3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot3start").value + ":00.000Z";
  var dayxot3stop  = document.getElementById("day" + x + "ot3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot3stop").value + ":00.000Z";
  ot1 = (new Date(dayxot1stop) - new Date(dayxot1start))
  ot2 = (new Date(dayxot2stop) - new Date(dayxot2start))   
  ot3 = (new Date(dayxot3stop) - new Date(dayxot3start))   
  ottot = ot1 + ot2 + ot3;
  var otdiffHrs = Math.floor((ottot % 86400000) / 3600000); // hours
  var otdiffMins = Math.round(((ottot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "ottot").innerHTML = ('0000'+otdiffHrs).slice(-2) + ":" + ('0000'+otdiffMins).slice(-2);



  // TOIL totals
  var toilobal = toilopening;
  var toilodiffHrs = Math.floor((Math.abs(toilobal) % 86400000) / 3600000); // hours
  var toilodiffMins = Math.round(((Math.abs(toilobal) % 86400000) % 3600000) / 60000); // minutes
  if (toilobal >= 0) {
    document.getElementById("day" + x + "toilobal").innerHTML = ('0000'+toilodiffHrs).slice(-2) + ":" + ('0000'+toilodiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "toilobal").innerHTML = "-" + ('0000'+toilodiffHrs).slice(-2) + ":" + ('0000'+toilodiffMins).slice(-2);
  }
  var dayxtoil1start = document.getElementById("day" + x + "toil1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil1start").value + ":00.000Z";
  var dayxtoil1stop  = document.getElementById("day" + x + "toil1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil1stop").value + ":00.000Z";
  var dayxtoil2start = document.getElementById("day" + x + "toil2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil2start").value + ":00.000Z";
  var dayxtoil2stop  = document.getElementById("day" + x + "toil2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil2stop").value + ":00.000Z";
  var dayxtoil3start = document.getElementById("day" + x + "toil3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil3start").value + ":00.000Z";
  var dayxtoil3stop  = document.getElementById("day" + x + "toil3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil3stop").value + ":00.000Z";
  toil1 = (new Date(dayxtoil1stop) - new Date(dayxtoil1start))
  toil2 = (new Date(dayxtoil2stop) - new Date(dayxtoil2start))   
  toil3 = (new Date(dayxtoil3stop) - new Date(dayxtoil3start))   
  toiltot = toil1 + toil2 + toil3;
  var toildiffHrs = Math.floor((toiltot % 86400000) / 3600000); // hours
  var toildiffMins = Math.round(((toiltot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "toiltot").innerHTML = ('0000'+toildiffHrs).slice(-2) + ":" + ('0000'+toildiffMins).slice(-2);
  var toilcbal = toilobal + toiltot - toiltakentot; // hh:mm
  var ttoildiffHrs = Math.floor((Math.abs(toilcbal) % 86400000) / 3600000); // hours
  var ttoildiffMins = Math.round((((Math.abs(toilcbal)) % 86400000) % 3600000) / 60000); // minutes
  if (toilcbal >= 0) {
    document.getElementById("day" + x + "toilcbal").innerHTML = ('0000'+ttoildiffHrs).slice(-2) + ":" + ('0000'+ttoildiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "toilcbal").innerHTML = "-" + ('0000'+ttoildiffHrs).slice(-2) + ":" + ('0000'+ttoildiffMins).slice(-2);
  }



  // Flex calculations
  var flexobal = flexopening;
  var flexodiffHrs = Math.floor((Math.abs(flexobal) % 86400000) / 3600000); // hours
  var flexodiffMins = Math.round(((Math.abs(flexobal) % 86400000) % 3600000) / 60000); // minutes
  if (flexobal >= 0) {
    document.getElementById("day" + x + "flexobal").innerHTML = ('0000'+flexodiffHrs).slice(-2) + ":" + ('0000'+flexodiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "flexobal").innerHTML = "-" + ('0000'+flexodiffHrs).slice(-2) + ":" + ('0000'+flexodiffMins).slice(-2);
  }
  standardhrs = [6,7,13,14].includes(x) ? 0 : Math.abs(((7*60)+36)*60000); // 27,360,000 
  flextot =  tot - standardhrs + lvtot;
  var flexdiffHrs = Math.floor((Math.abs(flextot) % 86400000) / 3600000); // hours
  var flexdiffMins = Math.round(((Math.abs(flextot) % 86400000) % 3600000) / 60000); // minutes
  //  var x = condition ? exprIfTrue : exprIfFalse
  if (flextot >= 0) {
    document.getElementById("day" + x + "flexearned").innerHTML = ('0000'+flexdiffHrs).slice(-2) + ":" + ('0000'+flexdiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "flexearned").innerHTML = "-" + ('0000'+flexdiffHrs).slice(-2) + ":" + ('0000'+flexdiffMins).slice(-2);
  }
  var flexcbal = flexobal + flextot - fltot; // hh:mm
  maxflex = Math.abs(((7*60)+36)*3*60000);
  flexcbal2 = Math.min(Math.abs(flexcbal), maxflex);
  var tflexdiffHrs = Math.floor((Math.abs(flexcbal2) % 86400000) / 3600000); // hours
  var tflexdiffMins = Math.round((((Math.abs(flexcbal2)) % 86400000) % 3600000) / 60000); // minutes
  if (flexcbal >= 0) {
    document.getElementById("day" + x + "flexcbal").innerHTML = ('0000'+tflexdiffHrs).slice(-2) + ":" + ('0000'+tflexdiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "flexcbal").innerHTML = "-" + ('0000'+tflexdiffHrs).slice(-2) + ":" + ('0000'+tflexdiffMins).slice(-2);
  }
  // return flexcbal;
  return [flexcbal, toilcbal];
}
</script>