<?php
$cat=shell_exec("cat userandpass");

$value = explode(":", $cat);
for ($a=0;$a<count($value);$a++)
{
echo $value[$a]."\n";
}
$numofusers=count($value);
echo $numofusers/2;

?>
