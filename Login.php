<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html>
<style>

html { 
  background: url(images/rabbit2017.jpg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>
<body><center>
<img src='images/thebriarpatch.png'>
<form name="form1" method="post" id="form1" action="">
<fieldset>
<b style="background:green">Username: <input type="text" name="username" id="username">
Password: <input type="password" name="password" id="password"></b>
<input type="submit" value="Log me in!">
</fieldset>
Default username: <b>BriarPatch</b> and password: <b>BriarPatch</b><br>
<b style='background:LawnGreen'><a href="PWDReset.php">Need to reset your password?  Click here</a><br>
<a href="CreateUser.php">Create new user?  Click here</a></b>
</form>
</center>
<?php

//SQLite3 stuff
//************************************
//check for SQLite3 BriarPatch DATABASE and TABLE existence
//create them if not already existing
//************************************

$DBexists=shell_exec("ls BriarPatch.db 2>&1");
//echo $DBexists;
if (strpos($DBexists, 'cannot') !== false)
{
    echo "<b style='background:yellow'>database not created yet...creating now</b>";

//create DB
$myfile = fopen("BriarPatch.db", "w");
fwrite($myfile);
fclose($myfile);
}

//main class to be used throughout code

   class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('BriarPatch.db');
      }
   }
   $db = new MyDB();
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
  //    echo "Opened database successfully\n";
   }

//************************************************************
//create tables
//************************************************************

//perform select query to determine if EXPLOIT TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='EXPLOITS';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE EXPLOITS does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE EXPLOITS
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      EXPLOIT         TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}


//perform select query to determine if EXPLOITARCHIVES TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='EXPLOITARCHIVES';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE EXPLOITARCHIVES does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE EXPLOITARCHIVES
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      EXPLOIT         TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}





//perform select query to determine if WINDOWS OS TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='WINDOWSOS';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE WINDOWSOS does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE WINDOWSOS
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      DEVICETYPE         TEXT     NOT NULL,
      BASEURL           TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}

//perform select query to determine if WINDOWSOSARCHIVES TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='WINDOWSOSARCHIVES';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE WINDOWSOSARCHIVES does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE WINDOWSOSARCHIVES
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      DEVICETYPE         TEXT     NOT NULL,
      BASEURL           TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}

//perform select query to determine if LINUX OS TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='LINUXOS';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE LINUXOS does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE LINUXOS
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      DEVICETYPE         TEXT     NOT NULL,
      BASEURL           TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}

//perform select query to determine if LINUXOSARCHIVES TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='LINUXOSARCHIVES';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE LINUXOSARCHIVES does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE LINUXOSARCHIVES
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      DEVICETYPE         TEXT     NOT NULL,
      BASEURL           TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}


//perform select query to determine if MACINTOSH OS TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='MACINTOSHOS';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE MACINTOSHOS does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE MACINTOSHOS
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      DEVICETYPE         TEXT     NOT NULL,
      BASEURL           TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}

//perform select query to determine if MACINTOSHOSARCHIVES TABLE exists
$result = $db->query("select count(*) from sqlite_master where type='table' and name='MACINTOSHOSARCHIVES';");
$outputs=print_r($result->fetchArray(), true);
//echo $outputs;

if (strpos($outputs, '[count(*)] => 1') !== false)
{
//echo "TABLE exists!";
}
else
{
        echo "<br><b style='background:yellow'>TABLE MACINTOSHOSARCHIVES does NOT exist...creating now</b>";

   $sql =<<<EOF
      CREATE TABLE MACINTOSHOSARCHIVES
      (DATE           TEXT     NOT NULL UNIQUE ON CONFLICT IGNORE,
      DEVICETYPE         TEXT     NOT NULL,
      BASEURL           TEXT     NOT NULL,
      SOURCE            TEXT     NOT NULL,
      DEST       TEXT     NOT NULL);
EOF;

   $ret = $db->exec($sql);
   if(!$ret){
      echo $db->lastErrorMsg();
   } else {
      echo "<br><b style='background:yellow'>Table created successfully</b>";
   }

}

   $db->close();

//************************************************


echo "<br><b style='background:yellow'>";
$updatecheck=shell_exec("sudo ./update.sh");
echo $updatecheck;
echo "</b>";

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


