<?php

$brodirs=shell_exec("ls -1 -d /opt/nsm/bro/logs/*");
$brodirs=trim($brodirs);
$brodirs=explode(PHP_EOL,$brodirs);
$brodirscount=count($brodirs);
//echo $brodirs[0],$brodirs[1],$brodirs[2];
echo $brodirscount;




?>
