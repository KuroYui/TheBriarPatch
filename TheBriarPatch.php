<?php
session_start();

if (!isset($_SESSION["authuser"]) && !isset($_SESSION["authpass"]))
{
echo "Please login first...thanks!<br>Redirecting to login page in 3 seconds...<br>";
header("refresh:3;url=Login.php");
exit (0);
}
?>
<head>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function(){
    $('#example').DataTable();
});
	</script>
</head>
<?php

//**********************************************************************************************
//TheBriarPatch
//Locates and classifies traffic captured from Suricata and compares with intel logs from Bro
//**********************************************************************************************

//main class to be used throughout code

   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('BriarPatch.db');
      }
   }

//************************************************

$tracker="";
$malicious="";
$currentdate=shell_exec('date +"%m/%d/%Y"');
$bropid=0;
$suripid=0;
$maliciousscanner="";
echo '<form id="getdevice" action="" method="POST">';
$proclist=shell_exec("ps aux");

$locatebrosuri=explode(PHP_EOL,$proclist);
$proccount=count($locatebrosuri);
for ($i=0;$i<$proccount-1;$i++)
{
$surirunning=preg_match("/\/bin\/suricata/",$locatebrosuri[$i]);
//$brorunning=preg_match("/\/bin\/bro/",$locatebrosuri[$i]);
//echo $surirunning."<br>";
if ($surirunning==1)
{
//echo "found suricata process!";
$suripid=1;
}
//deprecated
//if ($brorunning==1)
//{
//echo "found bro process!";
//$bropid=1;
//}
}

//DEPRECATED
//if (shell_exec("cat maliciousscanner")==1)
//{
//$maliciousscanner=1;
//}


echo "<b><i style='font-size:14px;background:white'>Logged in as: </b><b style='background:yellow'>".$_SESSION['authuser']."</i></b><br>";
echo "<input type='button' onClick='logoutsession()' name='loggy' id='loggy' style='font-size:14px' value='logout'><br>";
echo "<br><b style='font-size:14px'>&nbsp<u style='background:white'>TheBriarPatch Status Menu</u><br>";
echo "<fieldset style='width:200px'>";

//DEPRECATED
//if ($maliciousscanner==1)
//echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b>Malicious scanner is enabled!</p>";
//else
//echo "<p align='left'><img src='images/redx.png' width=15 height=15><b>Malicious scanner is not enabled</p>";

//if ($bropid!=1)
//{
//echo "<p align='left'><img src='images/redx.png' width=15 height=15><b>bro is not running</p>";
//}
//else
//{
//echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b>bro is running!</p>";
//}
if ($suripid!=1)
{
echo "<p align='left'><img src='images/redx.png' width=15 height=15><b style='background:yellow'>suricata is not running</p></b>";
}
else
{
echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b style='background:yellow'>suricata is running!</b></p>";
}

if (shell_exec("cat refreshornot")==1)
{
	echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b style='background:yellow'>Auto Refresh every 60 seconds</p></b>";
}
//show file sizes of logs
$loglisting=shell_exec("ls -Sahg /var/log/suricata/ | awk '{print $4,$8}' | grep -m 3 -e 'fast.log' -e 'http.log'");
$loglisting=explode(PHP_EOL,$loglisting);
echo "<b style='background:white'><u>Top 3 Largest Logs:</b></u>"."<br>";
foreach ($loglisting as &$value)
{
	echo "<b style='background:yellow'>".$value."</b><br>";
}

echo "<b style='background:yellow'><u>Log integrity check results:</b></u><br>";
exec("file --mime-encoding /var/log/suricata/*.log | grep binary",$theoutput,$integrity);
if ($integrity==1)
{
echo "<b style='background:white'>"."Logs look good.  no corruption detected</b>";
}
else
{
echo "<b style='background:white'>"."Logs appear to be corrupted.  This is often due to unexpected, forced closing of Suricata<br>";
echo "Recommended Actions: remove all files in /var/log/suricata so Suricata can re-create new copies</b>";
}
//DEPRECATED
//echo "<input type='button' onClick='surisubmit()' style='font-size:14px' title='clear exploit logs' value='clear old exploit logs' name='suricatalogs' id='suricatalogs'>";

echo "<input type='button' onClick='logrotatesubmit()' style='font-size:14px' title='Rotate Logs (runs daily otherwise)' value='Rotate Logs (runs daily otherwise)' name='logginhelp' id='logginhelp'>";
echo "</fieldset></b>";
echo "<center><img src='images/briarpatch.png'><br><i style='font-size:14px'>An extremely crude, lightweight Web Frontend for Suricata/Bro to be used with BriarIDS<br><a href='https://www.github.com/musicmancorley/BriarIDS'><b>https://www.github.com/musicmancorley/BriarIDS</a></b></i></center>";

echo "<input type='hidden' id='grabiphone' name='grabiphone'>";
echo "<input type='hidden' id='lennythepenguin' name='lennythepenguin'>";
echo "<input type='hidden' id='exploity' name='exploity'>";
echo "<input type='hidden' id='windowsmachine' name='windowsmachine'>";
echo "<input type='hidden' id='clearsuricata' name='clearsuricata'>";
echo "<input type='hidden' id='rotateit' name='rotateit'>";
echo "<input type='hidden' id='logout' name='logout'>";
echo "<center><b>Discovered Devices will be displayed automatically below</b><br>";

echo "<table cellpadding='10'><tr>";
//Windows OS
$WindowsOS = shell_exec("grep -m 1 'Windows NT' /var/log/suricata/http.log | awk '{print $2}'");
//Windows OS Archives
$WindowsOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'Windows NT' | awk '{print $2}'");
//Linux OS
$LinuxOS = shell_exec("grep -m 1 'X11' /var/log/suricata/http.log | awk '{print $2}'");
//Linux OS Archives
$LinuxOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'X11' | awk '{print $2}'");
//Chromebook (chrome os)
$ChromeOS = shell_exec("grep -m 1 'CrOS' /var/log/suricata/http.log | awk '{print $2}'");
//Chrome OS Archives
$ChromeOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'CrOS' | awk '{print $2}'");
//SmartTV (Smart TV)
$SmartTV = shell_exec("grep -m 1 'Tizen' /var/log/suricata/http.log | awk '{print $2}'");
//SmartTV Archives
$SmartTVArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'Tizen' | awk '{print $2}'");
//Android OS (Android)
$AndroidOS = shell_exec("grep -m 1 'Android' /var/log/suricata/http.log | awk '{print $2}'");
//Android OS Archives
$AndroidOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'Android' | awk '{print $2}'");
//Armv7l (Raspberry PI OS)
$RaspberryPIOS = shell_exec("grep -m 1 'armv7l' /var/log/suricata/http.log | awk '{print $2}'");
//RaspberryPi OS Archives
$RaspberryPIOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'armv7l' | awk '{print $2}'");
//Mac OS
$MacOS = shell_exec("grep -m 1 'Macintosh' /var/log/suricata/http.log | awk '{print $2}'");
//Mac OS Archives
$MacOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'Macintosh' | awk '{print $2}'");
//iPhone
$iPhone = shell_exec("grep -m 1 'iPhone' /var/log/suricata/http.log | awk '{print $2}'");
//RaspberryPi OS Archives
$iPhoneOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'iPhone' | awk '{print $2}'");
//iPad
$iPad = shell_exec("grep -m 1 'iPad' /var/log/suricata/http.log | awk '{print $2}'");
//iPad OS Archives
$iPadOSArchives=shell_exec("zcat /var/log/suricata/http.log-* 2> /dev/null | grep -m 1 'iPad' | awk '{print $2}'");
//Exploit Attempts
$ExploitAttempts = shell_exec("grep -m 1 -E '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' /var/log/suricata/fast.log");
//Exploit Attempts Archives
$ExploitAttemptsArchives = shell_exec("zcat /var/log/suricata/fast.log-* 2> /dev/null | grep -m 1 -E '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}'");

$todaysdate=date("m/d/Y");

/*
//Deprecated
//echo $todaysdate;
//$todayspackets = file_get_contents("/var/log/suricata/http.log");
$todayspackets="";
//**********************************
//use file stream instead ;)
//**********************************
$todayshttplog = fopen("/var/log/suricata/http.log",'r');

while (true) {
$buffer=fgets($todayshttplog);
if (!$buffer) {
break;
}

preg_match('/[$todaysdate]/',$buffer,$matches);
if ($matches)
{
//var_dump($buffer);
$todayspackets=1;
}
else
{
$todayspackets="";
}
//echo "todays packets: ".$todayspackets;
}
//*****************************************
fclose($todayshttplog);

if ($todayspackets != "")
{
*/

if ($WindowsOS != "" || $WindowsOSArchives != "")
{
echo "<td><img src='images/windowscomputer' onClick='windowssubmitter()' class='img1' width=75 height=75>"."<br>Windows-based OS</td>";
}
if ($LinuxOS != "" || $ChromeOS != "" || $SmartTV != "" || $AndroidOS != "" || $RaspberryPIOS != "" || $LinuxOSArchives != "" || $ChromeOSArchives != "" || $SmartTVArchives != "" || $AndroidOSArchives != "" || $RaspberryPIOSArchives != "")
{
echo "<td><img src='images/linuxcomputer' onClick='penguinsubmitter()' class='img1' width=75 height=75>"."<br>Linux-based OS</td>";
}
if ($MacOS != "" || $iPhone != "" || $iPad	!= "" || $MacOSArchives != "" || $iPhoneOSArchives != "" || $iPadOSArchives != "")
{
echo "<td><img src='images/MacOSX.png' onClick='iphonesubmitter()' class='img1' width=100 height=75>"."<br><center>Apple-based OS</center></td>";
}
if ($ExploitAttempts != "" || $ExploitAttemptsArchives != "")
{
echo "<td><img src='images/bug.png' onClick='exploitsubmitter()' class='img1' width=75 height=75>"."<br><center>Exploit Attempts</center></td>";
}
if ($ExploitAttempts == "" && $iPhone == "" && $iPad == "" && $MacOS == "" && $LinuxOS == "" && $WindowsOS == "" && $ChromeOS == "" && $SmartTV == "" && $AndroidOS == "" && $RaspberryPIOS == ""
 && $WindowsOSArchives == "" && $LinuxOSArchives == "" && $ChromeOSArchives == "" && $SmartTVArchives == "" && $AndroidOSArchives == "" && $RaspberryPIOSArchives == "" && 
 $MacOSArchives == "" && $iPhoneOSArchives == "" && $iPadOSArchives == "" && $ExploitAttemptsArchives == "")
{
echo "<b style='background:orange'>Doesn't look like you have any packets collected from Suricata, old or new, for analysis yet...</b>";
}
//else
//{
//echo "<b style='background:orange'>Doesn't look like you have any LIVE packets for today collected from Suricata for analysis yet...<br>";
//echo "feel free to browse the archived data until LIVE data arrives</b>";
//}
echo "</tr></table>";

//******************************************************
//This section checks for archived logs via logrotate.d
//******************************************************

$outs=shell_exec("./archives.sh");
$outs=explode(PHP_EOL,$outs);
echo "<b style='background:yellow'>Optional: Browse Archived Logs </b>";
//echo "<form name='grabber' id='grabber' method='post' action=''>";
echo "<input type='hidden' value='blank' name='selecter' id='selecter'>";
echo '<select id="MySelect" name="MySelect" onChange="collectit()">';
echo '<option selected disabled>TODAY\'S LOGS [DEFAULT]</option>';
for ($v=0;$v<count($outs)-1;$v++)
{
echo '<option>'.$outs[$v].'</option>';
}
echo '</select>';
//echo '<input type="submit" value="search!">';
//echo "</form>";

echo "<br>";
//echo '<form name="gatherer" id="gatherer" method="POST" action="">';
echo "<input type='hidden' id='obligatory' value='blank' name='obligatory'>";
echo "<input type='hidden' id='osclicked' value='blank' name='osclicked'>";
//echo "</form>";

//*******************************************************************
//This generates more verbose information after clicking on log entry
//*******************************************************************
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['obligatory']) && $_POST['obligatory'] != "blank" && isset($_POST['osclicked']) && $_POST['osclicked'] != "blank")
{
echo "<h2>";
echo "<p align='left' style='position:absolute;width:1000px'><u style='background:green'>Here's some extra info on the entry you just clicked on (actual resources loaded, etc):</u><br>";
$searchstring=escapeshellarg($_POST['obligatory']);
$osvalue=escapeshellarg($_POST['osclicked']);
$moredetails=shell_exec("grep $searchstring $osvalue");
echo $moredetails."</p><br>";
echo "</h2>";
}


if (shell_exec("cat refreshornot")==1)
{
header("Refresh:60");
}

//logging out???
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout']) && $_POST['logout']=="loggingout")
{
//session_start();
setcookie(session_name(), '', 100);
session_unset();
session_destroy();
$_SESSION = array();
header("refresh:1;url=Login.php");
}


//DEPRECATED
//if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clearsuricata']) && $_POST['clearsuricata']=="clicked")
//{
//shell_exec("sudo ./suriretention.sh");
//echo "<script>alert('suricata exploit logs have been cleared!');</script>";
//}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rotateit']) && $_POST['rotateit']=="clicked")
{
shell_exec("sudo ./rotatelogsnew.sh > /dev/null 2>/dev/null &");
echo "<script>alert('logs rotation script has been run!');</script>";
}



//***********************************************
//Apple device section
//***********************************************

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grabiphone']) && $_POST['grabiphone']=="clicked")
{
$archivebit=False;
$ourdate="";
//iPhone devices
shell_exec("> iPhoneTraffic.txt");
//$output = shell_exec("grep iPhone /var/log/suricata/http.log >> iPhoneTraffic.txt");
//echo $_POST['selecter'];

//*******************************************************************
//This section determines if archive data will be pulled or live data
//*******************************************************************
if ($_POST['selecter'] == "blank")
{
shell_exec("./today.sh Apple iPhoneTraffic");
}
else
{
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." Apple iPhoneTraffic");
$ourdate=shell_exec("head -n 1 iPhoneTraffic.txt | awk -F'-' '{print $1}'");
//echo $ourdate;
$archivebit=True;
}
$devicefinder=shell_exec("grep -e 'iPhone' -e 'Macintosh' -e 'iPad' iPhoneTraffic.txt");
$thedate = shell_exec("awk '{print $1}' iPhoneTraffic.txt");
$theurl = shell_exec("awk '{print $2}' iPhoneTraffic.txt");
//$devicetype = shell_exec("awk '/iPhone/{print $9}' iPhoneTraffic.txt");
//$deviceos = shell_exec("awk '/iPhone/{print $11}' iPhoneTraffic.txt");
$sourceip = shell_exec("awk '{print $(NF-2)}' iPhoneTraffic.txt");
$remoteip = shell_exec("awk '{print $(NF)}' iPhoneTraffic.txt");

$devicefinder=explode(PHP_EOL,$devicefinder);
$single_urls=explode(PHP_EOL,$theurl);
//$single_urls=array_unique($single_urls, SORT_REGULAR);
//$device=explode(PHP_EOL,$devicetype);
$thedate=explode(PHP_EOL,$thedate);
//$theos=explode(PHP_EOL,$deviceos);
$eachip=explode(PHP_EOL,$sourceip);
$eachip2=explode(PHP_EOL,$remoteip);
//$urlscount=count($single_urls);
//$devicecount=count($device);
$counturls = count($single_urls);

   
//********
//header
//********
echo "<table id='example' style='background:none' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";

for ($a=0; $a<$counturls-1; $a++)
{
//if (!preg_match('/192\.168\.1\.\d{1,3}/', $single_urls[$a]))

if (preg_match('/Macintosh/',$devicefinder[$a])) //Mac OSX
{
	if ($archivebit!=True)
	{	
	$sql ="INSERT INTO MACINTOSHOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Mac OSX Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   
   }
   else
   {
	   $sql ="INSERT INTO MACINTOSHOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Mac OSX Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
	
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/MacOSX.png' width=30 height=30>Mac OSX Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/iPhone/',$devicefinder[$a])) //iPhone Device
{
	if ($archivebit!=True)
	{	
	
	$sql ="INSERT INTO MACINTOSHOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'iPhone Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

$db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   
   }
   else
   {
	   
	$sql ="INSERT INTO MACINTOSHOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'iPhone Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

$db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
   
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src=images/iphone width=30 height=30>iPhone Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/iPad/',$devicefinder[$a])) //iPad Device
{
	if ($archivebit!=True)
	{	
	$sql ="INSERT INTO MACINTOSHOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'iPad Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   
   }
   else
   {
	   $sql ="INSERT INTO MACINTOSHOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'iPad Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
   
   
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/ipad.png' width=30 height=30>iPad Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
{
	if ($archivebit!=True)
	{
	$sql ="INSERT INTO MACINTOSHOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Unknown Apple Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

  $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   
   }
   else
   {
	   $sql ="INSERT INTO MACINTOSHOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Unknown Apple Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

  $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
   
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/MacOSX.png' width=30  height=30>Unknown Apple Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}

$db->close();


}

//***************
//SELECT QUERY
//***************

$db = new SQLite3('BriarPatch.db');
$J=0;
if ($archivebit==True)
{
	$ourdate=trim($ourdate);
	//echo "<br>"."SELECT * FROM MACINTOSHOSARCHIVES WHERE DATE LIKE "."'".$ourdate."%'";
	$results = $db->query("SELECT * FROM MACINTOSHOSARCHIVES WHERE DATE LIKE "."'".$ourdate."%'");
}
else
{
	$results = $db->query('SELECT * FROM MACINTOSHOS');
}
while ($row = $results->fetchArray()) 
{

	if (preg_match('/Mac/',$row['DEVICETYPE'])) //Mac OSX
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"iPhoneTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/MacOSX.png' width=50 height=50>Macintosh Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else if (preg_match('/iPhone/',$row['DEVICETYPE'])) //iPhone 
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"iPhoneTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/iphone' width=50 height=50>iPhone Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else if (preg_match('/iPad/',$row['DEVICETYPE'])) //iPad
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"iPhoneTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/ipad.png' width=50 height=50>iPad Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else //unknown Apple device
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"iPhoneTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/MacOSX.png' width=50 height=50>Unknown Apple Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}

$J=$J+1;
}

echo "</tr></tbody></table>";
//echo '<div style="clear: both; margin-bottom: 2000px;">';
$db->close();	
//echo "</div>";
}





//**********************
//Windows Device Section
//**********************

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['windowsmachine']) && $_POST['windowsmachine']=="clicked")
{
	
$archivebit=False;
$ourdate="";
//Windows devices
shell_exec("> WindowsTraffic.txt");
//$output = shell_exec("grep 'Windows NT' /var/log/suricata/http.log >> WindowsTraffic.txt");

if ($_POST['selecter'] == "blank")
{
	
shell_exec("./today.sh 'Windows NT' 'WindowsTraffic'");
}
else
{
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." 'Windows NT' 'WindowsTraffic'");
$ourdate=shell_exec("head -n 1 WindowsTraffic.txt | awk -F'-' '{print $1}'");
$archivebit=True;
}
//shell_exec("./today.sh 'Windows NT' 'WindowsTraffic'");

$thedate = shell_exec("awk '/Windows NT/{print $1}' WindowsTraffic.txt");
$theurl = shell_exec("awk '/Windows NT/{print $2}' WindowsTraffic.txt");
//$devicetype = shell_exec("awk '/Windows NT/{print $7}' WindowsTraffic.txt");
//$deviceos = shell_exec("awk '/Windows NT/{print $8,$9,$10,$11}' WindowsTraffic.txt");
$sourceip = shell_exec("awk '{print $(NF-2)}' WindowsTraffic.txt");
$remoteip = shell_exec("awk '{print $(NF)}' WindowsTraffic.txt");

$thedate=explode(PHP_EOL,$thedate);
$single_urls=explode(PHP_EOL,$theurl);
//$single_urls=array_unique($single_urls, SORT_REGULAR);
//$device=explode(PHP_EOL,$devicetype);
//$theos=explode(PHP_EOL,$deviceos);
$eachip=explode(PHP_EOL,$sourceip);
$eachip2=explode(PHP_EOL,$remoteip);
//$urlscount=count($single_urls);
//$devicecount=count($device);
$datecount=count($thedate);
//$counturls = count($single_urls);

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }

for ($R=0;$R<$datecount-1;$R++)
{
	if ($archivebit == False)
	{
		
  $sql ="INSERT INTO WINDOWSOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$R]', 'Windows OS Device', '$single_urls[$R]', '$eachip[$R]', '$eachip2[$R]')";

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   
   }
   else
   {
	   $sql ="INSERT INTO WINDOWSOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$R]', 'Windows OS Device', '$single_urls[$R]', '$eachip[$R]', '$eachip2[$R]')";

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   
}	
$db->close();

//******
//header
//******
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";

$db = new SQLite3('BriarPatch.db');
$a=0;
if ($archivebit == True)
{
$ourdate=trim($ourdate);
$results = $db->query("SELECT * FROM WINDOWSOSARCHIVES WHERE DATE LIKE "."'".$ourdate."%'");
}
else
{
$results = $db->query('SELECT * FROM WINDOWSOS');
}
while ($row = $results->fetchArray()) {
    echo "<tr align='left' id='counter$a' onClick='grabID(date$a,\"WindowsTraffic.txt\")'><td id='date$a'>$row[DATE]</td><td><img src='images/windowscomputer' width=50 height=50>$row[DEVICETYPE]</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
$a=$a+1;
}

echo "</tr></table>";
$db->close();
}







//****************************************
//EXPLOITS Section
//****************************************

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exploity']) && $_POST['exploity']=="clicked")
{
$archivebit=False;
$ourdate="";
//Exploits cleanup
shell_exec("> ExploitTraffic.txt");
//**********************
//Insert current data
//**********************

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }

if ($_POST['selecter'] != "blank")
{
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." 'Exploits' 'ExploitTraffic'");
$ourdate=shell_exec("head -n 1 ExploitTraffic.txt | awk -F'-' '{print $1}'");
$archivebit=True;
}
else
{	
	shell_exec("./today.sh Exploits ExploitTraffic");
	$ourdate=shell_exec("head -n 1 ExploitTraffic.txt | awk -F'-' '{print $1}'");
	$archivebit=False;
}


$UDPdate = shell_exec("grep '{UDP}' ExploitTraffic.txt  | awk -F']' '{print $1}' | awk -F'[' '{print $1}'");
$UDPtitle = shell_exec("grep '{UDP}' ExploitTraffic.txt  | awk -F']' '{print $3}' | awk -F'[' '{print $1}'");
$udp_source = shell_exec("grep '{UDP}' ExploitTraffic.txt | awk '{print $(NF-2)}'");
$udp_dest = shell_exec("grep '{UDP}' ExploitTraffic.txt | awk '{print $(NF)}'");


$TCPdate = shell_exec("grep '{TCP}' ExploitTraffic.txt  | awk -F']' '{print $1}' | awk -F'[' '{print $1}'");
$TCPtitle = shell_exec("grep '{TCP}' ExploitTraffic.txt  | awk -F']' '{print $3}' | awk -F'[' '{print $1}'");
$tcp_source = shell_exec("grep '{TCP}' ExploitTraffic.txt | awk '{print $(NF-2)}'");
$tcp_dest = shell_exec("grep '{TCP}' ExploitTraffic.txt | awk '{print $(NF)}'");


$UDPdateXploded=explode(PHP_EOL,$UDPdate);
$UDPsploitXploded=explode(PHP_EOL,$UDPtitle);
$udpsXploded=explode(PHP_EOL,$udp_source);
$udpdXploded=explode(PHP_EOL,$udp_dest);

$TCPdateXploded=explode(PHP_EOL,$TCPdate);
$TCPsploitXploded=explode(PHP_EOL,$TCPtitle);
$tcpsXploded=explode(PHP_EOL,$tcp_source);
$tcpdXploded=explode(PHP_EOL,$tcp_dest);


if ($archivebit==False)
{

for ($a=0;$a<count($UDPdateXploded)-1;$a++)
{
  $sql ="INSERT INTO EXPLOITS (DATE,EXPLOIT,SOURCE,DEST) VALUES ('$UDPdateXploded[$a]', '$UDPsploitXploded[$a]', '$udpsXploded[$a]', '$udpdXploded[$a]')";

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
}

for ($b=0;$b<count($TCPdateXploded)-1;$b++)
{
   $sql ="INSERT INTO EXPLOITS (DATE,EXPLOIT,SOURCE,DEST) VALUES ('$TCPdateXploded[$b]', '$TCPsploitXploded[$b]', '$tcpsXploded[$b]', '$tcpdXploded[$b]')";
   $ret = $db->exec($sql);

   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
}
}
else
{
	
for ($a=0;$a<count($UDPdateXploded)-1;$a++)
{
  $sql ="INSERT INTO EXPLOITARCHIVES (DATE,EXPLOIT,SOURCE,DEST) VALUES ('$UDPdateXploded[$a]', '$UDPsploitXploded[$a]', '$udpsXploded[$a]', '$udpdXploded[$a]')";

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
}

for ($b=0;$b<count($TCPdateXploded)-1;$b++)
{
   $sql ="INSERT INTO EXPLOITARCHIVES (DATE,EXPLOIT,SOURCE,DEST) VALUES ('$TCPdateXploded[$b]', '$TCPsploitXploded[$b]', '$tcpsXploded[$b]', '$tcpdXploded[$b]')";
   $ret = $db->exec($sql);

   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
}

}


   $db->close();


echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>EXPLOIT ATTEMPT</th><th>Source IP / Port</th><th>Destination IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>EXPLOIT ATTEMPT</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";
$db = new SQLite3('BriarPatch.db');
$ourdate=trim($ourdate);

if ($archivebit==True)
{
$results = $db->query("SELECT * FROM EXPLOITARCHIVES WHERE DATE LIKE "."'".$ourdate."%'");
}
else
{
$results = $db->query('SELECT * FROM EXPLOITS');
}

while ($row = $results->fetchArray()) {
    echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50>$row[DATE]</td><td>$row[EXPLOIT]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
}

echo "</tr></table>";
$db->close();
}






//************************
//Linux device section
//************************

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lennythepenguin']) && $_POST['lennythepenguin']=="clicked")
{
$archivebit=False;
$ourdate="";
//Linux devices
shell_exec("> LinuxTraffic.txt");
//$output = shell_exec('grep -e "Linux" -e "CrOS" /var/log/suricata/http.log >> LinuxTraffic.txt');

if ($_POST['selecter'] == "blank")
{
shell_exec("./today.sh Linux LinuxTraffic");
}
else
{
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." Linux LinuxTraffic");
$ourdate=shell_exec("head -n 1 LinuxTraffic.txt | awk -F'-' '{print $1}'");
$archivebit=True;
}
//shell_exec("./today.sh Linux LinuxTraffic");

$devicefinder=shell_exec("grep -e 'armv7l' -e 'Android' -e 'Tizen' -e 'X11;' -e 'CrOS' LinuxTraffic.txt");
$thedate = shell_exec("awk '{print $1}' LinuxTraffic.txt");
$theurl = shell_exec("awk '{print $2}' LinuxTraffic.txt");
//$devicetype = shell_exec("awk '/Linux/{print $8}' LinuxTraffic.txt");
//$deviceos = shell_exec("awk '/Linux/{print $9}' LinuxTraffic.txt");
$sourceip = shell_exec("awk '{print $(NF-2)}' LinuxTraffic.txt");
$remoteip = shell_exec("awk '{print $(NF)}' LinuxTraffic.txt");

$devicefinder=explode(PHP_EOL,$devicefinder);
$thedate=explode(PHP_EOL,$thedate);
$single_urls=explode(PHP_EOL,$theurl);
//$single_urls=array_unique($single_urls, SORT_REGULAR);
//$device=explode(PHP_EOL,$devicetype);
//$theos=explode(PHP_EOL,$deviceos);
$eachip=explode(PHP_EOL,$sourceip);
$eachip2=explode(PHP_EOL,$remoteip);
//$urlscount=count($single_urls);
//$devicecount=count($device);
$countdate=count($thedate);

//***********
//header
//***********
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";


//***********
//INSERTS
//***********

for ($a=0; $a<$countdate-1; $a++)
{

if (preg_match('/armv7l/',$devicefinder[$a])) //raspberry pi image
{

if ($archivebit!=True)
{	
	$sql ="INSERT INTO LINUXOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Raspberry Pi Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
   {
	   $sql ="INSERT INTO LINUXOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Raspberry Pi Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
	
}
else if (preg_match('/Android/',$devicefinder[$a])) //Android image
{

if ($archivebit!=True)
{	
	$sql ="INSERT INTO LINUXOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Android Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
   {
	   $sql ="INSERT INTO LINUXOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Android Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
	
}
else if (preg_match('/Tizen/',$devicefinder[$a])) //SmartTV image
{

if ($archivebit!=True)
{	
	$sql ="INSERT INTO LINUXOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'SmartTV Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
   {
	   $sql ="INSERT INTO LINUXOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'SmartTV Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
	
}
else if (preg_match('/CrOS/',$devicefinder[$a])) //Chromebook image
{

if ($archivebit!=True)
{	
	$sql ="INSERT INTO LINUXOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Chromebook Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
   {
	   $sql ="INSERT INTO LINUXOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Chromebook Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
	
}

else
{

if ($archivebit!=True)
{	
	$sql ="INSERT INTO LINUXOS (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Debian/Ubuntu based Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
//echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
   {
	   $sql ="INSERT INTO LINUXOSARCHIVES (DATE,DEVICETYPE,BASEURL,SOURCE,DEST) VALUES ('$thedate[$a]', 'Debian/Ubuntu based Device', '$single_urls[$a]', '$eachip[$a]', '$eachip2[$a]')";

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }
   
   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      //echo "Record inserted successfully\n";
   }
   }
   $db->close();
	
}
$db->close();

}

//***************
//SELECT QUERY
//***************

$db = new SQLite3('BriarPatch.db');
$J=0;
if ($archivebit==True)
{
	$ourdate=trim($ourdate);
	$results = $db->query("SELECT * FROM LINUXOSARCHIVES WHERE DATE LIKE "."'".$ourdate."%'");
}
else
{
	$results = $db->query('SELECT * FROM LINUXOS');
}
while ($row = $results->fetchArray()) 
{

	if (preg_match('/Raspberry/',$row['DEVICETYPE'])) //raspberry pi
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"LinuxTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/raspberrypi.png' width=50 height=50>Raspberry Pi Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else if (preg_match('/Android/',$row['DEVICETYPE'])) //Android
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"LinuxTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/android.png' width=50 height=50>Android Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else if (preg_match('/Smart/',$row['DEVICETYPE'])) //SmartTv
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"LinuxTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/smarttv.png' width=50 height=50>Smart TV Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else if (preg_match('/Chromebook/',$row['DEVICETYPE'])) //Chromebook
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"LinuxTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/chromeos.png' width=50 height=50>Chromebook Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}
	else //Linux Debian/Ubuntu
	{
		echo "<tr align='left' id='counter$J' onClick='grabID(date$J,\"LinuxTraffic.txt\")'><td id='date$J'>$row[DATE]</td><td><img src='images/linuxcomputer' width=50 height=50>Debian/Ubuntu Device</td><td>$row[BASEURL]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
	}

$J=$J+1;
}

echo "</tr></tbody></table>";
//echo '<div style="clear: both; margin-bottom: 2000px;">';
$db->close();	
//echo "</div>";
}

?>

<html>
<head></head>
<style>

html { 
  background: url(images/rabbit2.jpg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}

.img1 {
    border: solid 10px transparent;
}
.img1:hover {
    border-color: green;
}

</style>
<body>
</html>

<script language="javascript">

function iphonesubmitter()
{
document.getElementById('grabiphone').value="clicked";
document.getElementById('getdevice').submit();
}
function penguinsubmitter()
{
document.getElementById('lennythepenguin').value="clicked";
document.getElementById('getdevice').submit();
}
function exploitsubmitter()
{
document.getElementById('exploity').value="clicked";
document.getElementById('getdevice').submit();
}
function windowssubmitter()
{
document.getElementById('windowsmachine').value="clicked";
document.getElementById('getdevice').submit();
}
function surisubmit()
{
document.getElementById('clearsuricata').value="clicked";
document.getElementById('getdevice').submit();
}

function logrotatesubmit()
{
document.getElementById('rotateit').value="clicked";
document.getElementById('getdevice').submit();
}
function logoutsession()
{
document.getElementById('logout').value="loggingout";
document.getElementById('loggy').value="logging out...";
document.getElementById('getdevice').submit();
}
function collectit()
{
var e = document.getElementById("MySelect");
//alert(e.options[e.selectedIndex].text);
document.getElementById("selecter").value=e.options[e.selectedIndex].text;
//alert(document.getElementById("selecter").value);
}
function grabID(myvar,theos) 
{

	mystr=myvar.innerHTML;
	ostype=theos;
	//alert(mystr);
        document.getElementById('obligatory').value=mystr;
        document.getElementById('osclicked').value=ostype;
        //alert(document.getElementById('obligatory').value);
        document.getElementById('getdevice').submit();
}
</script>
</form>
