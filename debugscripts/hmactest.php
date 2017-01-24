<?php
session_start();
?>

<?php
if (!empty($_POST['passwd']))
{
$hashed_value = hash_hmac('sha512', $_POST['passwd'], 'BriarPatch');
$_SESSION['pass']=$hashed_value;
echo "sha512 hash: ".$_SESSION['pass'];
//
}
?>
<form name="form1" method="post" action="">
<input type="password" name="passwd" id="passwd"><input type="submit">
</form>
