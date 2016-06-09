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

	function sqlenc($word)
	{
		return str_replace("'", "\'", str_replace("\\", "\\\\", $word));
	}

	function authenticate($username, $password)
	{
		$user=mysql_fetch_array(mysql_query("SELECT * FROM ulUsers WHERE username='".sqlenc($username)."' AND password='".sqlenc($password)."'"));
		return isset($user['username']);
	}

	function exists($table, $column, $value)
	{
		$thing=mysql_fetch_array(mysql_query("SELECT * FROM ".sqlenc($table)." WHERE ".sqlenc($column)."='".sqlenc($value)."'"));
		return isset($thing[$column]);
	}

	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($db);

	if (! isset($_GET['cmd'])) error('cmd is undefined');

	if ($_GET['cmd'] == "createUser")
	{
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (exists('ulUsers', 'username', $_GET['username'])) error('user \''.$_GET['username'].'\' already exists');

		mysql_query("INSERT INTO ulUsers (username, password) VALUES('".sqlenc($_GET['username'])."', '".sqlenc($_GET['password'])."')");
	}
	else if ($_GET['cmd'] == "deleteUser")
	{
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulUsers', 'username', $_GET['username'])) error('user \''.$_GET['username'].'\' not found');

		mysql_query("DELETE FROM ulUsers WHERE username='".sqlenc($_GET['username'])."'");
		$q = mysql_query("SELECT * FROM ulConfs WHERE owner='".sqlenc($_GET['username'])."'");
		while ($conf = mysql_fetch_assoc($q))
		{
			unlink('confs/'.$conf["confname"].'.zip');
		}
		mysql_query("DELETE FROM ulConfs WHERE owner='".sqlenc($_GET['username'])."'");
	}
	else if ($_GET['cmd'] == "createConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' already exists');

		if (! file_exists("confs")) mkdir("confs");
		touch('confs/'.$_GET['confname'].".zip");
		mysql_query("INSERT INTO ulConfs (confname, owner, collaborators) VALUES('".sqlenc($_GET['confname'])."', '".sqlenc($_GET['username'])."', '')");
	}
	else if ($_GET['cmd'] == "deleteConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');

		unlink('confs/'.$_GET['confname'].'.zip');
		mysql_query("DELETE FROM ulConfs WHERE confname='".sqlenc($_GET['confname'])."' AND owner='".sqlenc($_GET['username'])."'");
	}
	else if ($_GET['cmd'] == "setConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');

		move_uploaded_file($_FILES['zippy']['tmp_name'], "confs/".$_GET['confname'].".zip");
	}
	else
	{
		error('unknown cmd='.$_GET['cmd']);
	}
?>
