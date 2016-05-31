<?php
	include "local.php";

	if ($dbhost == "" or $dbuser == "" or $dbpass == "" or $db == "")
	{
		echo "important database variable unset";
		exit;
	}

	function authenticate($username, $password)
	{
		return True;
	}

	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($db);

	if (! isset($_GET['cmd'])) exit;

	if ($_GET['cmd'] == "getFiles" && isset($_GET['conf']))
	{
		foreach (glob("confs/".$_GET['conf']."/*") as $file)
		{
			echo "$file ";
		}
	}
	else if ($_GET['cmd'] == "addUser" && isset($_GET['username']) && isset($_GET['password']))
	{
		mysql_query("INSERT INTO ulUsers (username, password) VALUES('".$_GET['username']."', '".$_GET['password']."')");
	}
	else if ($_GET['cmd'] == "deleteUser" && isset($_GET['username']) && isset($_GET['password']) && authenticate($_GET['username'], $_GET['password']))
	{
		mysql_query("DELETE FROM ulUsers WHERE username='".$_GET['username']."' AND password='".$_GET['password']."'");
	}
	else if ($_GET['cmd'] == "addConf" && isset($_GET['confname']) && isset($_GET['username']) && isset($_GET['password']) && authenticate($_GET['username'], $_GET['password']))
	{
		mysql_query("INSERT INTO ulConfs (confname, owner, collaborators) VALUES('".$_GET['confname']."', '".$_GET['username']."', '')");
	}
	else if ($_GET['cmd'] == "deleteConf" && isset($_GET['confname']) && isset($_GET['username']) && isset($_GET['password']) && authenticate($_GET['username'], $_GET['password']))
	{
		mysql_query("DELETE FROM ulConfs WHERE confname='".$_GET['confname']."' AND owner='".$_GET['username']."'");
	}
?>
