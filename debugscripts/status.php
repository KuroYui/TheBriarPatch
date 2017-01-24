<?php

$bropid=0;
$suripid=0;
$proclist=shell_exec("ps aux");

$locatebrosuri=explode(PHP_EOL,$proclist);
$proccount=count($locatebrosuri);
for ($i=0;$i<$proccount-1;$i++)
{
$surirunning=preg_match("/suricata/",$locatebrosuri[$i]);
$brorunning=preg_match("/bro/",$locatebrosuri[$i]);
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

if ($bropid!=1)
echo "<p align='left'><img src='redx.png' width=30 height=30><b>bro is not currently running</b></p>";
else
echo "<p align='left'><img src='greencheck.png' width=30 height=30><b>bro is running!</b></p>";
if ($suripid!=1)
echo "<p align='left'><img src='redx.png' width=30 height=30><b>suricata is not running</b></p>";
else
echo "<p align='left'><img src='greencheck.png' width=30 height=30><b>suricata is running!</b></p>";

//check for existence of specific device traffic

echo "<p align='left'>Check this box to refresh every 60 seconds automatically:";
echo "<input type='checkbox' id='refresher' name='refresher' onClick=document.getElementById('getdevice').submit();><br>";
echo "(<b>Note:</b> If you change your mind and wish to turn off automatic refresh, simply change the 1 -> 0 in the <b>'refreshornot'</b> file in this directory)";
echo "</p>";


?>
