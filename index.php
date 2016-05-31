<?php
	include "local.php";

	function error($txt)
	{
		echo $txt;
		exit;
	}

	if ($dbhost == "") error('$dbhost is undefined');
	if ($dbuser == "") error('$dbuser is undefined');
	if ($dbpass == "") error('$dbpass is undefined');
	if ($db == "") error('$db is undefined');

	function authenticate($username, $password)
	{
		$user=mysql_fetch_array(mysql_query("SELECT * FROM ulUsers WHERE username='".$username."' AND password='".$password."'"));
		return isset($user['username']);
	}

	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($db);

	if (! isset($_GET['cmd'])) error('cmd is undefined');

	if ($_GET['cmd'] == "getFiles")
	{
		if (! isset($_GET['conf'])) error('conf is undefined.');

		foreach (glob("confs/".$_GET['conf']."/*") as $file)
		{
			echo "$file ";
		}
	}
	else if ($_GET['cmd'] == "addUser")
	{
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');

		mysql_query("INSERT INTO ulUsers (username, password) VALUES('".$_GET['username']."', '".$_GET['password']."')");
	}
	else if ($_GET['cmd'] == "deleteUser")
	{
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');

		mysql_query("DELETE FROM ulUsers WHERE username='".$_GET['username']."' AND password='".$_GET['password']."'");
	}
	else if ($_GET['cmd'] == "addConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');

		mysql_query("INSERT INTO ulConfs (confname, owner, collaborators) VALUES('".$_GET['confname']."', '".$_GET['username']."', '')");
	}
	else if ($_GET['cmd'] == "deleteConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');

		mysql_query("DELETE FROM ulConfs WHERE confname='".$_GET['confname']."' AND owner='".$_GET['username']."'");
	}
	else
	{
		error('unknown cmd='.$_GET['cmd']);
	}
?>
