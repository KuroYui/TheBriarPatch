<?php
session_start();

if (!empty($_POST['user']) && !empty($_POST['passwd']) && !empty($_POST['email']))
{
$hashed_value = hash_hmac('sha512', $_POST['passwd'], $_POST['user']);
$newpass=$hashed_value;
//echo "sha512 hash: ".$newpass."<br>";

$ouruser=$_POST['user'];
//$ouruser=escapeshellarg($ouruser);
$ourpass=$_POST['passwd'];
$ourpass=escapeshellarg($ourpass);
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
$email=$_POST['email'];
$email=escapeshellarg($email);

$value = explode(":", $cat);
//debugging
//echo $ouruser."<br>".$value[0];
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

if ($needle==1)
{
echo "<br><b style='background:LawnGreen'>User already exists.  Refreshing page in 3 seconds."."</b><br>";
session_destroy();
header("refresh:3;url=CreateUser.php");
exit(0);
}

echo "<br><b style='background:LawnGreen'>User created successfully! Redirecting you to the Login page in 5 seconds...</b><br>";
shell_exec("echo -n :$ouruser:$newpass>>../../securedfiles/userandpass");
shell_exec("echo -n :$ouruser:$email>>../../securedfiles/emails");
session_destroy();
header("refresh:5;url=Login.php");
exit(0);

}

?>
<style>

html { 
  background: url(images/cloverrabbit.jpg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>
<body><center>
<h3 style='background:LawnGreen'>TheBriarPatch Secure Create User Form</h3><fieldset>
<form name="form1" method="post" action=""><b style='background:yellow'>
Desired Username: <input type="text" name="user" id="user"><br>Desired Password: <input type="password" name="passwd" id="passwd"><br>
User's email address: <input type="text" name="email" id="email"><br></b>
<input type="submit" value="create user" name="submitter" id="submitter"><br>
<br><b style='background:LawnGreen'><a href="Login.php">Click here to return to the login page</a></b></center></fieldset>
</form>

