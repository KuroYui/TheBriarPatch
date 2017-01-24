<?php
session_start();

if (!isset($_SESSION["authuser"]) && !isset($_SESSION["authpass"]))
{
echo "Please login first...thanks!<br>Redirecting to login page in 3 seconds...<br>";
header("refresh:3;url=Login.php");
exit (0);
}
?>

<?php

//TheBriarPatch
//Locates and classifies traffic captured from Suricata and compares with intel logs from Bro
$tracker="";
$malicious="";

$bropid=0;
$suripid=0;
$maliciousscanner="";
echo '<form id="getdevice" action="TheBriarPatch.php" method="POST">';
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
echo "<input type='button' onClick='surisubmit()' style='font-size:10px' title='clear suricata logs' value='clear old suricata logs' name='suricatalogs' id='suricatalogs'>";
echo "<input type='button' onClick='brosubmit()' style='font-size:10px' title='clear bro logs' value='clear old bro logs' name='broslogs' id='broslogs'>";

echo "<center><img src='images/briarpatch.png'><br><i style='font-size:14px'>An extremely crude, lightweight Web Frontend for Suricata/Bro to be used with BriarIDS<br>No database installation required!<br><a href='https://www.github.com/musicmancorley/BriarIDS'><b>https://www.github.com/musicmancorley/BriarIDS</a></b></i></center>";

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


//iPhone
$iPhone = shell_exec("grep 'iPhone' /var/log/suricata/http.log | awk '{print $2}'");
//Exploit Attempts
$ExploitAttempts = shell_exec("grep -E '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' /var/log/suricata/fast.log");


if ($WindowsOS != "")
{
echo "<td><img src='images/windowscomputer' onClick='windowssubmitter()' class='img1' width=75 height=75>"."<br>Windows-based OS</td>";
}
if ($LinuxOS != "" || $ChromeOS != "" || $SmartTV != "" || $AndroidOS != "" || $RaspberryPIOS != "")
{
echo "<td><img src='images/linuxcomputer' onClick='penguinsubmitter()' class='img1' width=75 height=75>"."<br>Linux-based OS</td>";
}
if ($iPhone != "")
{
echo "<td><img src='images/iphone' onClick='iphonesubmitter()' class='img1' width=75 height=75>"."<br><center>Apple-based OS</center></td>";
}
if ($ExploitAttempts != "")
{
echo "<td><img src='images/bug.png' onClick='exploitsubmitter()' class='img1' width=75 height=75>"."<br><center>Exploit Attempts</center></td>";
}
if ($ExploitAttempts == "" && $iPhone == "" && $LinuxOS == "" && $WindowsOS == "" && $ChromeOS == "" && $SmartTV == "" && $AndroidOS == "" && $RaspberryPIOS == "")
{
echo "<b style='background:orange'>Doesn't look like you have any packets collected from Suricata for analysis yet...</b>";
}

echo "</tr></table>";


if (shell_exec("cat refreshornot")==1)
{
header("Refresh:60");
}

//logging out???
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['logout']=="loggingout")
{
//session_start();
setcookie(session_name(), '', 100);
session_unset();
session_destroy();
$_SESSION = array();
header("refresh:1;url=Login.php");
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['clearsuricata']=="clicked")
{
shell_exec("sudo ./suriretention.sh");
echo "<script>alert('suricata logs have been cleared!');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['clearbro']=="clicked")
{
shell_exec("sudo ./broretention.sh");
echo "<script>alert('bro logs have been cleared!');</script>";
}


//Apple device section

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['grabiphone']=="clicked")
{
//iPhone devices
shell_exec("> iPhoneTraffic.txt");
$output = shell_exec('grep iPhone /var/log/suricata/http.log >> iPhoneTraffic.txt');

$thedate = shell_exec("awk '/iPhone/{print $1}' iPhoneTraffic.txt");
$theurl = shell_exec("awk '/iPhone/{print $2}' iPhoneTraffic.txt");
//$devicetype = shell_exec("awk '/iPhone/{print $9}' iPhoneTraffic.txt");
//$deviceos = shell_exec("awk '/iPhone/{print $11}' iPhoneTraffic.txt");
$sourceip = shell_exec("awk '{print $(NF-2)}' iPhoneTraffic.txt");
$remoteip = shell_exec("awk '{print $(NF)}' iPhoneTraffic.txt");

$single_urls=explode(PHP_EOL,$theurl);
//$device=explode(PHP_EOL,$devicetype);
$thedate=explode(PHP_EOL,$thedate);
//$theos=explode(PHP_EOL,$deviceos);
$eachip=explode(PHP_EOL,$sourceip);
$eachip2=explode(PHP_EOL,$remoteip);
//$urlscount=count($single_urls);
//$devicecount=count($device);
$counturls = count($single_urls);

if ($maliciousscanner==1)
echo "<table cellpadding=10><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr>";
else
echo "<table cellpadding=10><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr>";

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
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/iphone' width=30 height=30>iPhone</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";

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

echo "</tr></table>";
}








if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['windowsmachine']=="clicked")
{
//Windows devices
shell_exec("> WindowsTraffic.txt");
$output = shell_exec("grep 'Windows NT' /var/log/suricata/http.log >> WindowsTraffic.txt");

$thedate = shell_exec("awk '/Windows NT/{print $1}' WindowsTraffic.txt");
$theurl = shell_exec("awk '/Windows NT/{print $2}' WindowsTraffic.txt");
//$devicetype = shell_exec("awk '/Windows NT/{print $7}' WindowsTraffic.txt");
//$deviceos = shell_exec("awk '/Windows NT/{print $8,$9,$10,$11}' WindowsTraffic.txt");
$sourceip = shell_exec("awk '{print $(NF-2)}' WindowsTraffic.txt");
$remoteip = shell_exec("awk '{print $(NF)}' WindowsTraffic.txt");

$thedate=explode(PHP_EOL,$thedate);
$single_urls=explode(PHP_EOL,$theurl);
//$device=explode(PHP_EOL,$devicetype);
//$theos=explode(PHP_EOL,$deviceos);
$eachip=explode(PHP_EOL,$sourceip);
$eachip2=explode(PHP_EOL,$remoteip);
//$urlscount=count($single_urls);
//$devicecount=count($device);
$datecount=count($thedate);

if ($maliciousscanner==1)
echo "<table cellpadding=10><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr>";
else
echo "<table cellpadding=10><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr>";



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
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/windowscomputer' width=30 height=30>Windows OS Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";

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
echo "<br>";
echo "searching: ".$brodirs[$d]."<br>";
echo "scanning: ".$inteldirs[$f]."<br>";
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











if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['exploity']=="clicked")
{
//show exploit attempts
echo "enter filter string here, or multiple strings separated by a comma (up to 7 strings accepted currently)<br>";
echo "<input type='text' size=150 name='filterstrings'><input type='submit' value='add filter!'>";

$theurl = shell_exec("cat /var/log/suricata/fast.log");

$single_urls=explode(PHP_EOL,$theurl);
$urlscount=count($single_urls);

echo "<table cellpadding=10><tr align=left><th></th><th><center>Exploit Description</center></th></tr>";

for ($a=0; $a<$urlscount-1; $a++)
{
//if (!preg_match('/FILE store all/',$single_urls[$a]))
echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
}


echo "</tr></table>";
}





if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['filterstrings']))
{
//show filtered exploit attempts

$usersetfilters=$_POST['filterstrings'];
$pieces = explode(",", $usersetfilters);
$piececount=count($pieces);
$theurl = shell_exec("cat /var/log/suricata/fast.log");
//grep -v '{$_POST['filterstrings']}'"

if ($pieces[0]!="")
echo "filters applied: ". $piececount;
else
echo "filters applied: 0";

$single_urls=explode(PHP_EOL,$theurl);
$urlscount=count($single_urls);

echo "<table cellpadding=10><tr align=left><th></th></tr>";


for ($a=0; $a<$urlscount-1; $a++)
{

if ($piececount==1 && $pieces[0]!="")
{
       if (strpos($single_urls[$a], $pieces[0]) === false) //not found
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}
if ($piececount==2)
{
       if (strpos($single_urls[$a], $pieces[0]) === false && strpos($single_urls[$a], $pieces[1]) === false) //not found
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}
if ($piececount==3)
{
       if (strpos($single_urls[$a], $pieces[0]) === false && strpos($single_urls[$a], $pieces[1]) === false && strpos($single_urls[$a], $pieces[2]) === false) //not found
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}
if ($piececount==4)
{
       if (strpos($single_urls[$a], $pieces[0]) === false && strpos($single_urls[$a], $pieces[1]) === false && strpos($single_urls[$a], $pieces[2]) === false  && strpos($single_urls[$a], $pieces[3]) === false) //not found
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}
if ($piececount==5)
{
       if (strpos($single_urls[$a], $pieces[0]) === false && strpos($single_urls[$a], $pieces[1]) === false && strpos($single_urls[$a], $pieces[2]) === false  && strpos($single_urls[$a], $pieces[3]) === false  && strpos($single_urls[$a], $pieces[4]) === false)
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}
if ($piececount==6)
{
       if (strpos($single_urls[$a], $pieces[0]) === false && strpos($single_urls[$a], $pieces[1]) === false && strpos($single_urls[$a], $pieces[2]) === false && strpos($single_urls[$a], $pieces[3]) === false && strpos($single_urls[$a], $pieces[4]) === false && strpos($single_urls[$a], $pieces[5]) === false) //not found
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}
if ($piececount==7)
{
       if (strpos($single_urls[$a], $pieces[0]) === false && strpos($single_urls[$a], $pieces[1]) === false && strpos($single_urls[$a], $pieces[2]) === false && strpos($single_urls[$a], $pieces[3]) === false && strpos($single_urls[$a], $pieces[4]) === false && strpos($single_urls[$a], $pieces[5]) === false && strpos($single_urls[$a], $pieces[6]) === false) //not found
       {
       echo "<tr align='left'><td><img src='images/bug.png' width=50 height=50></td><td>$single_urls[$a]</td>";
       }
}





}
echo "</tr></table>";

}









//Linux device section

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['lennythepenguin']=="clicked")
{
//Linux devices
shell_exec("> LinuxTraffic.txt");
$output = shell_exec('grep -e "Linux" -e "CrOS" /var/log/suricata/http.log >> LinuxTraffic.txt');

$devicefinder=shell_exec("grep -e 'Linux' -e 'CrOS' LinuxTraffic.txt");
$thedate = shell_exec("awk '{print $1}' LinuxTraffic.txt");
$theurl = shell_exec("awk '{print $2}' LinuxTraffic.txt");
//$devicetype = shell_exec("awk '/Linux/{print $8}' LinuxTraffic.txt");
//$deviceos = shell_exec("awk '/Linux/{print $9}' LinuxTraffic.txt");
$sourceip = shell_exec("awk '{print $(NF-2)}' LinuxTraffic.txt");
$remoteip = shell_exec("awk '{print $(NF)}' LinuxTraffic.txt");

$devicefinder=explode(PHP_EOL,$devicefinder);
$thedate=explode(PHP_EOL,$thedate);
$single_urls=explode(PHP_EOL,$theurl);
//$device=explode(PHP_EOL,$devicetype);
//$theos=explode(PHP_EOL,$deviceos);
$eachip=explode(PHP_EOL,$sourceip);
$eachip2=explode(PHP_EOL,$remoteip);
//$urlscount=count($single_urls);
//$devicecount=count($device);
$countdate=count($thedate);


if ($maliciousscanner==1)
echo "<table cellpadding=10><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th><th>Malicious?</th></tr>";
else
echo "<table cellpadding=10><tr align=left><th>Date</th><th>DeviceType/OS</th><th>Base URL</th><th>Source IP / Port</th><th>Remote IP/Port</th></tr>";

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
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/raspberrypi.png' width=30 height=30>Raspberry Pi Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/Android/',$devicefinder[$a])) //android image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/android.png' width=30 height=30>Android SmartPhone Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/Tizen/',$devicefinder[$a])) //smart tv
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/smarttv.png' width=30 height=30>Smart TV Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else if (preg_match('/CrOS/',$devicefinder[$a])) //chromebook
{
//echo "it works!<br>";
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/chromeos.png' width=30 height=30>Chromebook OS device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
}
else //penguin image
{
//if (!preg_match('/192.168.1.128/', $single_urls[$a]))
echo "<tr align='left'><td>$thedate[$a]</td><td><img src='images/linuxcomputer' width=30 height=30>Linux Debian/Ubuntu OS Device</td><td>$single_urls[$a]</td><td>$eachip[$a]</td><td>$eachip2[$a]</td>";
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

</script>

