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

//SQLite3 stuff
//************************************
//check for SQLite3 BriarPatch DATABASE and TABLE existence
//create if not already existing
//************************************

$DBexists=shell_exec("ls BriarPatch.db 2>&1");
//echo $DBexists;
if (strpos($DBexists, 'cannot') !== false)
{
    echo "database not created yet...creating now";

//create DB
$myfile = fopen("BriarPatch.db", "w");
fwrite($myfile);
fclose($myfile);
}

//main class to be used throughout code

   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('BriarPatch.db');
      }
   }
   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
  //    echo "Opened database successfully\n";
   }

//perform select query to determine if TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='EXPLOITS';");

$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "TABLE EXPLOITS does NOT exist...creating now";

   $sql =<<<EOF
      CREATE TABLE EXPLOITS
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      EXPLOIT         TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "Table created successfully\n";
   }

}
   $db->close();

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
$brorunning=preg_match("/\/bin\/bro/",$locatebrosuri[$i]);
//echo $surirunning."<br>";
if ($surirunning==1)
{
//echo "found suricata process!";
$suripid=1;
}
if ($brorunning==1)
{
//echo "found bro process!";
$bropid=1;
}
}

if (shell_exec("cat maliciousscanner")==1)
{
$maliciousscanner=1;
}



echo "<i style='font-size:12px'>Logged in as: <b style='background:yellow'>".$_SESSION['authuser']."</i></b><br>";
echo "<input type='button' onClick='logoutsession()' name='loggy' id='loggy' style='font-size:10px' value='logout'><br>";
echo "<b style='font-size:11px'><u>TheBriarPatch Status Menu</u><br>";

if ($maliciousscanner==1)
echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b>Malicious scanner is enabled!</p>";
else
echo "<p align='left'><img src='images/redx.png' width=15 height=15><b>Malicious scanner is not enabled</p>";

if ($bropid!=1)
{
echo "<p align='left'><img src='images/redx.png' width=15 height=15><b>bro is not running</p>";
}
else
{
echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b>bro is running!</p>";
}
if ($suripid!=1)
{
echo "<p align='left'><img src='images/redx.png' width=15 height=15><b>suricata is not running</p>";
}
else
{
echo "<p align='left'><img src='images/greencheck.png' width=15 height=15><b>suricata is running!</b></p><br>";
}
echo "<input type='button' onClick='surisubmit()' style='font-size:10px' title='clear exploit logs' value='clear old exploit logs' name='suricatalogs' id='suricatalogs'>";
echo "<input type='button' onClick='brosubmit()' style='font-size:10px' title='clear bro logs' value='clear old bro logs' name='broslogs' id='broslogs'>";

echo "<center><img src='images/briarpatch.png'><br><i style='font-size:14px'>An extremely crude, lightweight Web Frontend for Suricata/Bro to be used with BriarIDS<br><a href='https://www.github.com/musicmancorley/BriarIDS'><b>https://www.github.com/musicmancorley/BriarIDS</a></b></i></center>";

echo "<input type='hidden' id='grabiphone' name='grabiphone'>";
echo "<input type='hidden' id='lennythepenguin' name='lennythepenguin'>";
echo "<input type='hidden' id='exploity' name='exploity'>";
echo "<input type='hidden' id='windowsmachine' name='windowsmachine'>";
echo "<input type='hidden' id='clearsuricata' name='clearsuricata'>";
echo "<input type='hidden' id='clearbro' name='clearbro'>";
echo "<input type='hidden' id='logout' name='logout'>";
echo "<center><b>Discovered Devices will be displayed automatically below</b><br>";

echo "<table cellpadding='10'><tr>";
//Windows OS
$WindowsOS = shell_exec("grep 'Windows NT' /var/log/suricata/http.log | awk '{print $2}'");
//Linux OS
$LinuxOS = shell_exec("grep 'Linux' /var/log/suricata/http.log | awk '{print $2}'");
//Chromebook (chrome os)
$ChromeOS = shell_exec("grep 'CrOS' /var/log/suricata/http.log | awk '{print $2}'");
//SmartTV (Smart TV)
$SmartTV = shell_exec("grep 'Tizen' /var/log/suricata/http.log | awk '{print $2}'");
//Android OS (Android)
$AndroidOS = shell_exec("grep 'Android' /var/log/suricata/http.log | awk '{print $2}'");
//Armv7l (Raspberry PI OS)
$RaspberryPIOS = shell_exec("grep 'armv7l' /var/log/suricata/http.log | awk '{print $2}'");
//Mac OS
$MacOS = shell_exec("grep 'Macintosh' /var/log/suricata/http.log | awk '{print $2}'");
//iPhone
$iPhone = shell_exec("grep 'iPhone' /var/log/suricata/http.log | awk '{print $2}'");
//Exploit Attempts
$ExploitAttempts = shell_exec("grep -E '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' /var/log/suricata/fast.log");

$todaysdate=date("m/d/Y");

//**********************************
//using file stream method instead ;)
//should correct issue:
//Allowed memory size of 134217728 bytes exhausted
//php memory allocation issues fix
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

if ($WindowsOS != "")
{
echo "<td><img src='images/windowscomputer' onClick='windowssubmitter()' class='img1' width=75 height=75>"."<br>Windows-based OS</td>";
}
if ($LinuxOS != "" || $ChromeOS != "" || $SmartTV != "" || $AndroidOS != "" || $RaspberryPIOS != "")
{
echo "<td><img src='images/linuxcomputer' onClick='penguinsubmitter()' class='img1' width=75 height=75>"."<br>Linux-based OS</td>";
}
if ($MacOS != "" || $iPhone != "")
{
echo "<td><img src='images/MacOSX.png' onClick='iphonesubmitter()' class='img1' width=100 height=75>"."<br><center>Apple-based OS</center></td>";
}
if ($ExploitAttempts != "")
{
echo "<td><img src='images/bug.png' onClick='exploitsubmitter()' class='img1' width=75 height=75>"."<br><center>Exploit Attempts</center></td>";
}
if ($ExploitAttempts == "" && $iPhone == "" && $MacOS == "" && $LinuxOS == "" && $WindowsOS == "" && $ChromeOS == "" && $SmartTV == "" && $AndroidOS == "" && $RaspberryPIOS == "")
{
echo "<b style='background:orange'>Doesn't look like you have any packets collected from Suricata, old or new, for analysis yet...</b>";
}

}
else
{
echo "<b style='background:orange'>Doesn't look like you have any LIVE packets for today collected from Suricata for analysis yet...<br>";
echo "feel free to browse the archived data until LIVE data arrives</b>";
}
echo "</tr></table>";








$outs=shell_exec("./archives.sh");
$outs=explode(PHP_EOL,$outs);
echo "Optional: Browse Archived Logs ";
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


//check details information grabber
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




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clearsuricata']) && $_POST['clearsuricata']=="clicked")
{
shell_exec("sudo ./suriretention.sh");
echo "<script>alert('suricata exploit logs have been cleared!');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clearbro']) && $_POST['clearbro']=="clicked")
{
shell_exec("sudo ./broretention.sh");
echo "<script>alert('bro logs have been cleared!');</script>";
}


//Apple device section

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grabiphone']) && $_POST['grabiphone']=="clicked")
{
//iPhone devices
shell_exec("> iPhoneTraffic.txt");
//$output = shell_exec("grep iPhone /var/log/suricata/http.log >> iPhoneTraffic.txt");
//echo $_POST['selecter'];

if ($_POST['selecter'] == "blank")
shell_exec("./today.sh Apple iPhoneTraffic");
else
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." Apple iPhoneTraffic");

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

if ($maliciousscanner==1)
{
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr></tfoot>";
echo "<tbody>";
}
else
{
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";
}
$brodirs=shell_exec("ls -d /opt/nsm/bro/logs/*");
$brodirs=trim($brodirs);
$brodirs=explode(PHP_EOL,$brodirs);
$brodirscount=count($brodirs);
//echo $brodirscount;
//echo $brodirs[0], $brodirs[1];

//$inteldirs=shell_exec("ls $brodirs[0] | grep intel*");
//$inteldirs=explode(PHP_EOL,$inteldirs);
//$inteldirscount=count($inteldirs);
//$malicious=shell_exec("zgrep airtyrant.com $brodirs[0]/$inteldirs[0]");
//echo $malicious;
for ($a=0; $a<$counturls-1; $a++)
{
//if (!preg_match('/192\.168\.1\.\d{1,3}/', $single_urls[$a]))

if (preg_match('/Macintosh/',$devicefinder[$a])) //raspberry pi image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/MacOSX.png' width=30 height=30>Mac OSX Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/iPhone/',$devicefinder[$a])) //raspberry pi image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src=images/iphone width=30 height=30>iPhone Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/iPad/',$devicefinder[$a])) //smart tv
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/ipad.png' width=30 height=30>iPad Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else
{
echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"iPhoneTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/MacOSX.png' width=30  height=30>Unknown Apple Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}







if ($maliciousscanner==1)
{

for ($d=0;$d<$brodirscount;$d++)
{

$inteldirs=shell_exec("ls $brodirs[$d] | grep intel*");
$inteldirs=trim($inteldirs);
$inteldirs=explode(PHP_EOL,$inteldirs);
$inteldirscount=count($inteldirs);

for ($f=0;$f<$inteldirscount;$f++)
{
//for debugging purposes only
//****************************************
//echo "<br>";
//echo "searching: ".$brodirs[$d]."<br>";
//echo "scanning: ".$inteldirs[$f]."<br>";
//****************************************

//make sure we aren't scanning our own local ip!
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
$malicious=shell_exec("zgrep $single_urls[$a] $brodirs[$d]/$inteldirs[$f]");

if ($malicious != "")
$tracker=1;

}
}

//if ($tracker==1 && !preg_match('/192.168.1.128/', $single_urls[$a]))
if ($tracker==1)
{
echo "<td style='background:red'>Potentially malicious!</td>";
$tracker=0;
}
else
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<td style='background:green'>Not likely Malicious</td>";
$tracker=0;
}

}

}

echo "</tr></tbody></table>";
//echo '<div style="clear: both; margin-bottom: 2000px;">';
	
//echo "</div>";
}








if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['windowsmachine']) && $_POST['windowsmachine']=="clicked")
{
//Windows devices
shell_exec("> WindowsTraffic.txt");
//$output = shell_exec("grep 'Windows NT' /var/log/suricata/http.log >> WindowsTraffic.txt");

if ($_POST['selecter'] == "blank")
shell_exec("./today.sh 'Windows NT' 'WindowsTraffic'");
else
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." 'Windows NT' 'WindowsTraffic'");

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

if ($maliciousscanner==1)
{
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr></tfoot>";
echo "<tbody>";
}
else
{
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";
}

$brodirs=shell_exec("ls -d /opt/nsm/bro/logs/*");
$brodirs=trim($brodirs);
$brodirs=explode(PHP_EOL,$brodirs);
$brodirscount=count($brodirs);
//echo $brodirscount;

//$inteldirs=shell_exec("ls $brodirs[0] | grep intel*");
//$inteldirs=explode(PHP_EOL,$inteldirs);
//$inteldirscount=count($inteldirs);
//$malicious=shell_exec("zgrep airtyrant.com $brodirs[0]/$inteldirs[0]");
//echo $malicious;

for ($a=0; $a<$datecount-1; $a++)
{
//if (!preg_match('/192\.168\.1\.\d{1,3}/', $single_urls[$a]))

echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"WindowsTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/windowscomputer' width=30 height=30>Windows OS Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";

if ($maliciousscanner==1)
{

for ($d=0;$d<$brodirscount;$d++)
{

$inteldirs=shell_exec("ls $brodirs[$d] | grep intel*");
$inteldirs=trim($inteldirs);
$inteldirs=explode(PHP_EOL,$inteldirs);
$inteldirscount=count($inteldirs);

for ($f=0;$f<$inteldirscount;$f++)
{
//for debugging purposes only
//****************************************
//echo "<br>";
//echo "searching: ".$brodirs[$d]."<br>";
//echo "scanning: ".$inteldirs[$f]."<br>";
//****************************************

//make sure we aren't scanning our own local ip!
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
$malicious=shell_exec("zgrep $single_urls[$a] $brodirs[$d]/$inteldirs[$f]");

if ($malicious != "")
$tracker=1;

}
}

if ($tracker==1)
{
echo "<td style='background:red'>Potentially malicious!</td>";
$tracker=0;
}
else
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<td style='background:green'>Not likely Malicious</td>";
$tracker=0;
}

}

}

echo "</tr></table>";
}







//****************************************
//EXPLOITS Section
//****************************************

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exploity']) && $_POST['exploity']=="clicked")
{

//**********************
//Insert current data
//**********************

   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      //echo "Opened database successfully\n";
   }


$UDPdate = shell_exec("grep '{UDP}' /var/log/suricata/fast.log  | awk -F']' '{print $1}' | awk -F'[' '{print $1}'");
$UDPtitle = shell_exec("grep '{UDP}' /var/log/suricata/fast.log  | awk -F']' '{print $3}' | awk -F'[' '{print $1}'");
$udp_source = shell_exec("grep '{UDP}' /var/log/suricata/fast.log | awk '{print $(NF-2)}'");
$udp_dest = shell_exec("grep '{UDP}' /var/log/suricata/fast.log | awk '{print $(NF)}'");


$TCPdate = shell_exec("grep '{TCP}' /var/log/suricata/fast.log  | awk -F']' '{print $1}' | awk -F'[' '{print $1}'");
$TCPtitle = shell_exec("grep '{TCP}' /var/log/suricata/fast.log  | awk -F']' '{print $3}' | awk -F'[' '{print $1}'");
$tcp_source = shell_exec("grep '{TCP}' /var/log/suricata/fast.log | awk '{print $(NF-2)}'");
$tcp_dest = shell_exec("grep '{TCP}' /var/log/suricata/fast.log | awk '{print $(NF)}'");


$UDPdateXploded=explode(PHP_EOL,$UDPdate);
$UDPsploitXploded=explode(PHP_EOL,$UDPtitle);
$udpsXploded=explode(PHP_EOL,$udp_source);
$udpdXploded=explode(PHP_EOL,$udp_dest);

$TCPdateXploded=explode(PHP_EOL,$TCPdate);
$TCPsploitXploded=explode(PHP_EOL,$TCPtitle);
$tcpsXploded=explode(PHP_EOL,$tcp_source);
$tcpdXploded=explode(PHP_EOL,$tcp_dest);


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


   $db->close();


echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>EXPLOIT ATTEMPT</th><th>Source IP / Port</th><th>Destination IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>EXPLOIT ATTEMPT</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";
$db = new SQLite3('BriarPatch.db');

//show exploit attempts
//echo "enter filter string here, or multiple strings separated by a comma (up to 7 strings accepted currently)<br>";
//echo "<input type='text' size=150 name='filterstrings' id='filterstrings' value=''><input type='submit' value='add filter!'>";


//$theurl = shell_exec("cat /var/log/suricata/fast.log");
//$single_urls=explode(PHP_EOL,$theurl);
//$urlscount=count($single_urls);


//echo "<table cellpadding=10><tr align=left><th></th><th><center>Exploit Attempts</center></th></tr>";

$results = $db->query('SELECT * FROM EXPLOITS');
while ($row = $results->fetchArray()) {
    echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50>$row[DATE]</td><td>$row[EXPLOIT]</td><td>$row[SOURCE]</td><td>$row[DEST]</td>";
}
//for ($a=0; $a<$urlscount-1; $a++)
//{
//if (!preg_match('/FILE store all/',$single_urls[$a]))
//echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
//}


echo "</tr></table>";
$db->close();
}







//Linux device section

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lennythepenguin']) && $_POST['lennythepenguin']=="clicked")
{
//Linux devices
shell_exec("> LinuxTraffic.txt");
//$output = shell_exec('grep -e "Linux" -e "CrOS" /var/log/suricata/http.log >> LinuxTraffic.txt');

if ($_POST['selecter'] == "blank")
shell_exec("./today.sh Linux LinuxTraffic");
else
shell_exec("./archiveddata.sh ".escapeshellarg($_POST['selecter'])." Linux LinuxTraffic");

//shell_exec("./today.sh Linux LinuxTraffic");

$devicefinder=shell_exec("grep -e 'X11;' -e 'CrOS' LinuxTraffic.txt");
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


if ($maliciousscanner==1)
{
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr></tfoot>";
echo "<tbody>";
}
else
{
echo "<table id='example' class='display' width='100%' cellspacing='0'><thead><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></thead>";
echo "<tfoot><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr></tfoot>";
echo "<tbody>";
}

$brodirs=shell_exec("ls -d /opt/nsm/bro/logs/*");
$brodirs=trim($brodirs);
$brodirs=explode(PHP_EOL,$brodirs);
$brodirscount=count($brodirs);
//echo $brodirscount;
//echo $brodirs[0], $brodirs[1];

//$inteldirs=shell_exec("ls $brodirs[0] | grep intel*");
//$inteldirs=explode(PHP_EOL,$inteldirs);
//$inteldirscount=count($inteldirs);
//$malicious=shell_exec("zgrep airtyrant.com $brodirs[0]/$inteldirs[0]");
//echo $malicious;

for ($a=0; $a<$countdate-1; $a++)
{
//echo $devicefinder[$a]."<br>";
if (preg_match('/armv7l/',$devicefinder[$a])) //raspberry pi image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))

echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/Android/',$devicefinder[$a])) //android image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))

echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/android.png' width=30 height=30>Android SmartPhone Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/Tizen/',$devicefinder[$a])) //smart tv
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))

echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/smarttv.png' width=30 height=30>Smart TV Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/CrOS/',$devicefinder[$a])) //chromebook
{
//echo "it works!<br>";
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))

echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/chromeos.png' width=30 height=30>Chromebook OS device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else //penguin image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))

echo "<tr align=left id='counter$a' onClick='grabID(date$a,\"LinuxTraffic.txt\")'><td id='date$a'>$thedate[$a]</td><td><img src='images/linuxcomputer' width=30 height=30>Linux Debian/Ubuntu OS Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}

if ($maliciousscanner==1)
{

for ($d=0;$d<$brodirscount;$d++)
{

$inteldirs=shell_exec("ls $brodirs[$d] | grep intel*");
$inteldirs=trim($inteldirs);
$inteldirs=explode(PHP_EOL,$inteldirs);
$inteldirscount=count($inteldirs);

for ($f=0;$f<$inteldirscount;$f++)
{
//for debugging purposes only
//****************************************
//echo "<br>";
//echo "searching: ".$brodirs[$d]."<br>";
//echo "scanning: ".$inteldirs[$f]."<br>";
//****************************************

//make sure we aren't scanning our own local ip!
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
$malicious=shell_exec("zgrep $single_urls[$a] $brodirs[$d]/$inteldirs[$f]");

if ($malicious != "")
$tracker=1;

}
}

if ($tracker==1)
{
echo "<td style='background:red'>Potentially malicious!</td>";
$tracker=0;
}
else
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<td style='background:green'>Not likely Malicious</td>";
$tracker=0;
}

}

}

echo "</tr></table>";
}

?>

<html>
<head></head>
<style>


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
function brosubmit()
{
document.getElementById('clearbro').value="clicked";
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
