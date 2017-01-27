<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html>
<body><center>
<img src='images/thebriarpatch.png'>
<form name="form1" method="post" id="form1" action="">
<fieldset>
<b>Username: <input type="text" name="username" id="username">
Password: <input type="password" name="password" id="password"></b>
<input type="submit" value="Log me in!">
</fieldset>
Default username: <b>BriarPatch</b> and password: <b>BriarPatch</b><br>
<a href="PWDReset.php">Need to reset your password?  Click here</a><br>
<a href="CreateUser.php">Create new user?  Click here</a>
</form>
</center>
<?php
echo "<br><center><b style='background:orange'>";
$updatecheck=shell_exec("sudo ./update.sh");
echo $updatecheck;
echo "</center></b>";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
if (!empty($_POST['username']) && !empty($_POST['password']))
{
// Set session variables
$_SESSION["authuser"] = $_POST['username'];
$_SESSION["authpass"] = hash_hmac('sha512', $_POST['password'], $_POST['username']);
header("Location: Validate.php");
}
else
{
echo "<center><b style='background:orange'>no blank fields please</center></b><br>";
return false;
}
}
?>

</body>
</html>


