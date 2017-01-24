<?php
echo "Please enter your desired username:";
$username = fgets(STDIN);
echo "Please enter your desired password:";
$password = fgets(STDIN);
$username=trim($username);
$password=trim($password);
$macky=hash_hmac('sha512', $password, $username);
echo $macky."\r\n";
?>
