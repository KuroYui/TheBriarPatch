<?php
session_start();

//get username and pass and checkem'
$cat=shell_exec("cat ../../securedfiles/userandpass");
$cat=trim($cat);
$value = explode(":", $cat);
for ($q=0;$q<count($value);$q++)
{
//debug stuff
//*****************************************************************************************
//echo $value[$q]."<br>";
//if ($_SESSION["authuser"]==$value[$q] && hash_equals($value[$q+1],$_SESSION["authpass"]))
//echo "authuser=".$_SESSION["authuser"]." password: ".$value[$q+1];
//$numofusers=count($value);
//echo $numofusers/2;
//*****************************************************************************************

//echo $value[$q].$value[$q+1]."<br>";
if ($_SESSION["authuser"]==$value[$q] && hash_equals($value[$q+1],$_SESSION["authpass"]))
    {
        //echo $_SESSION["authpass"]."<br>";
        //echo "<i style='font-size:12px'>Logged in as: <b style='background:yellow'>".$_SESSION['authuser']."</i></b>";
        header("Location: TheBriarPatch.php");
    }

}

{
//echo $value[$q];
echo "Sorry wrong username and password combination...<br>";
echo "Redirecting to Login page in 5 seconds...<br>";
header("refresh:5;url=Login.php");
exit(0);
}

?>

