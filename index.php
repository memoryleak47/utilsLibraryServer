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

	function exists($table, $column, $value)
	{
		$thing=mysql_fetch_array(mysql_query("SELECT * FROM ".$table." WHERE ".$column."='".$value."'"));
		return isset($thing[$column]);
	}

	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($db);

	if (! isset($_GET['cmd'])) error('cmd is undefined');

	if ($_GET['cmd'] == "getFiles")
	{
		if (! isset($_GET['confname'])) error('confname is undefined.');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');

		foreach (glob("confs/".$_GET['confname']."/*") as $file)
		{
			echo "$file ";
		}
	}
	else if ($_GET['cmd'] == "addUser")
	{
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (exists('ulUsers', 'username', $_GET['username'])) error('user \''.$_GET['username'].'\' already exists');

		mysql_query("INSERT INTO ulUsers (username, password) VALUES('".$_GET['username']."', '".$_GET['password']."')");
	}
	else if ($_GET['cmd'] == "deleteUser")
	{
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulUsers', 'username', $_GET['username'])) error('user \''.$_GET['username'].'\' not found');

		mysql_query("DELETE FROM ulUsers WHERE username='".$_GET['username']."' AND password='".$_GET['password']."'");
	}
	else if ($_GET['cmd'] == "addConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' already exists');

		mysql_query("INSERT INTO ulConfs (confname, owner, collaborators) VALUES('".$_GET['confname']."', '".$_GET['username']."', '')");
	}
	else if ($_GET['cmd'] == "deleteConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');

		mysql_query("DELETE FROM ulConfs WHERE confname='".$_GET['confname']."' AND owner='".$_GET['username']."'");
	}
	else
	{
		error('unknown cmd='.$_GET['cmd']);
	}
?>
