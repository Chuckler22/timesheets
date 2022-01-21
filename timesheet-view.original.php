<?php
   session_start();
   if(!isset($_SESSION["username"])) {
    header("location:./login.php");
  }
  
  $loggedinuser = $_SESSION["username"];
  require("config.inc.php");


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
    $sql = "SELECT timesheets.id as timesheet_id,timesheets.employee,`fne_date`,`submitted`,`approvedby`,`approvedtime`, employ.id, employ.first, employ.last, employ.novellname, employ.supervisor, super.id as superid, super.novellname as supernovell FROM `timesheets` LEFT JOIN `employees` employ on timesheets.employee = employ.id LEFT JOIN `employees` super ON employ.supervisor = super.id WHERE submitted IS NOT NULL AND timesheets.id = ? AND super.novellname = ?";
    $userStatement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($userStatement,'is',$_GET["timesheet"],$loggedinuser);
    mysqli_stmt_execute($userStatement);
    $result = mysqli_stmt_get_result($userStatement);
    if (mysqli_num_rows($result) == 1) {
      $getData = mysqli_fetch_assoc($result);
      $fne = $getData["fne_date"];
      $novellname = $getData["novellname"];
      $superid = $getData["superid"];
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
    $approved = file_get_contents('https://localhost/timesheet-view.php?timesheet='.$_GET["timesheet"]);
    error_log($approved, 3, "/var/www/html/my-errors.log");
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
  // echo $sql;
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
?>
<link rel="stylesheet" href="timesheet.css">
<form action="<?=$_SERVER['REQUEST_URI']; ?>" method="post">
<input type="submit" value="Home" id="home" name="home" class="menubar">
<?php if ($loggedinuser != $novellname) {
  echo "<input type=\"submit\" value=\"Approve\" id=\"approve\" name=\"approve\" class=\"menubar\" onClick=\"return confirm('Approve this timesheet?')\">";
}
?> 
<input type="submit" value="Logout" id="logout" name="logout" class="menubar">
<script src="./cleave.min.js"></script>
<h1>
    <?=$first; ?>
    <?=$last; ?>
</h1>
    <div style="position: absolute; top: 8px; left: 250px;" class="bordered">
        <?php 
            if ($submitted != NULL) {
            echo "Submitted " . $submitted . "<br>";
            }
            // if ($loggedinuser != $novellname) {
            //   echo "<input type=\"text\" id=\"flexcb\" name=\"flexcb\">";
            //  echo "<input type=\"text\" id=\"toilcb\" name=\"toilcb\">";
            // }
            if (isset($approved)) {
              echo "Approved at " . $approvedtime . "<br>";
              echo "Approved by " . $approvedby . "<br>";
            }
        ?>
    </div>
<?php
include 'timesheet-body.php';
  // to do:  based on a variables passed by form, this is where we'll create the timesheet for the fortnight end date
?>
</form>
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