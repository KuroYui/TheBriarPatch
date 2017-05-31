<?php
session_start();

if (!empty($_POST['user']) && !empty($_POST['passwd']))
{
$hashed_value = hash_hmac('sha512', $_POST['passwd'], $_POST['user']);
$newpass=$hashed_value;
//echo "sha512 hash: ".$newpass."<br>";

$ouruser=$_POST['user'];
$ourpass=$_POST['passwd'];
$cat=shell_exec("cat ../../securedfiles/userandpass");
$cat=trim($cat);
$needle=0;
//echo $cat;
//{
//echo "User not found in user list"."<br>";
//session_destroy();
//header("refresh:1;url=PWDReset.php");
//exit(0);
//}

$value = explode(":", $cat);
for ($q=0;$q<count($value);$q++)
{
if ($value[$q] == $ouruser)
{
$needle=1;
//echo "found the needle!<br>";
//echo $value[$q]."<br>";
}

//echo $value[$q]."<br>";
}

if ($needle!=1)
{
echo "<br><b style='background:LawnGreen'>User not found in user list.  Refreshing page in 3 seconds."."</b><br>";
session_destroy();
header("refresh:3;url=PWDReset.php");
exit(0);
}

$numbers = range(1, 10);
shuffle($numbers);
$finalnum=implode($numbers);

//echo "<br>".$finalnum."<br>";

//grab email associated with user
$cat2=shell_exec("cat ../../securedfiles/emails");
$cat2=trim($cat2);
$value2 = explode(":", $cat2);
$needle2="blank";

for ($r=0;$r<count($value2);$r++)
{
if ($value2[$r]==$ouruser)
$needle2=$value2[$r+1];
}

if ($needle2=="blank")
{
echo "<br><b style='background:LawnGreen'>this user does not have an email account setup yet.  Please add an email account and come back.</b>";
session_destroy();
header("refresh:3;url=PWDReset.php");
exit(0);
}


//send temporary password reset key
echo "<br><b style='background:LawnGreen'>Sending user an email with their temporary, one-time passcode now!  Please check your inbox</b>";
//echo "email found: ".$needle2."<br>";
shell_exec("echo $finalnum | mail -s 'Your BriarPatch temporary one-time passcode' $needle2");

$_SESSION['finalnum']=$finalnum;
$_SESSION['theusername']=$ouruser;
$_SESSION['thepassword']=$newpass;


echo '<form name="form2" method="POST" action=""><br>';
echo "<br><b style='background:LawnGreen'>Please enter your temporary, one-time passcode here: "."<input type='password' name='passreset' id='passreset'><input type='submit' value='change password'></b>";
echo "</form>";
}


if (!empty($_POST['passreset']))
{

if ($_SESSION['finalnum']==$_POST['passreset'])
{
echo "<br><b style='background:LawnGreen'>Passcode confirmed!  Resetting password now.</b>";
$changepassword=$_SESSION['thepassword'];
$theuser=$_SESSION['theusername'];
//set new password for user
$cat=shell_exec("cat ../../securedfiles/userandpass");
$cat=trim($cat);
$value = explode(":", $cat);
shell_exec("> ../../securedfiles/userandpass");
for ($q=0;$q<count($value);$q++)
{

if ($value[$q]!=$theuser)
{

if ($q != count($value)-1)
shell_exec("echo -n $value[$q]:>>../../securedfiles/userandpass");
else
shell_exec("echo -n $value[$q]>>../../securedfiles/userandpass");

}
else
{

if ($q+2 < count($value))
shell_exec("echo -n $theuser:$changepassword:>>../../securedfiles/userandpass");
else
shell_exec("echo -n $theuser:$changepassword>>../../securedfiles/userandpass");
$q=$q+1;
}





}



session_destroy();
echo "<br><b style='background:LawnGreen'>Sending you back to the login page in 3 seconds...</b>";
header("refresh:3;url=Login.php");
}
else
{
echo "<br><b style='background:LawnGreen'>nope...please try again</b>";
session_destroy();
header("refresh:1;url=PWDReset.php");
}


}

?>
<style>

html { 
  background: url(images/rabbit4.jpg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>
<body><center>
<h3 style='background:LawnGreen'>TheBriarPatch Secure Password Reset Form</h3>
<fieldset>
<form name="form1" method="post" action="">
<b style='background:yellow'>Username: <input type="text" name="user" id="user">New Password: <input type="password" name="passwd" id="passwd"></b><input type="submit" value="reset password" name="submitter" id="submitter"><br>
<br><b style='background:LawnGreen'><a href="Login.php">click here to return to the login page</a></b>
</center>
</fieldset>
</form>

