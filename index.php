﻿<?php
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

	function deleteDirectory($dir)
		{
			if (!file_exists($dir))
			{
				return true;
			}
			if (!is_dir($dir))
			{
				return unlink($dir);
			}
				foreach (scandir($dir) as $item)
			{
				if ($item == '.' || $item == '..')
				{
					continue;
				}
				if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item))
				{
					return false;
				}
			}
			return rmdir($dir);
		}

	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($db);

	if (! isset($_GET['cmd'])) error('cmd is undefined');

	if ($_GET['cmd'] == "createUser")
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
	else if ($_GET['cmd'] == "createConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' already exists');

		mkdir('confs/'.$_GET['confname']);
		mysql_query("INSERT INTO ulConfs (confname, owner, collaborators) VALUES('".$_GET['confname']."', '".$_GET['username']."', '')");
	}
	else if ($_GET['cmd'] == "deleteConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');

		deleteDirectory('confs/'.$_GET['confname']);
		mysql_query("DELETE FROM ulConfs WHERE confname='".$_GET['confname']."' AND owner='".$_GET['username']."'");
	}
	else if ($_GET['cmd'] == "setConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! isset($_GET['username'])) error('username is undefined');
		if (! isset($_GET['password'])) error('password is undefined');
		if (! authenticate($_GET['username'], $_GET['password'])) error('wrong username and password combination');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');

		$zip = new ZipArchive;
		$res = $zip->open($_FILES['zippyupload']['tmp_name']);
		if ($res === TRUE)
		{
			deleteDirectory('confs/'.$_GET['confname']);
			mkdir('confs/'.$_GET['confname']);
			$zip->extractTo('confs/'.$_GET['confname']);
			$zip->close();
		}
		else
		{
			echo 'Failed to upload file';
		}
	}
	else if ($_GET['cmd'] == "getConf")
	{
		if (! isset($_GET['confname'])) error('confname is undefined');
		if (! exists('ulConfs', 'confname', $_GET['confname'])) error('conf \''.$_GET['confname'].'\' not found');
		$zipfilename = "zippy.zip";
		$zip = new ZipArchive;
		$zip->open($zipfilename, ZipArchive::CREATE);
		$zip->addFile('confs/'.$_GET['confname']);
		$zip->close();
		echo file_get_contents($zipfilename);
	}
	else
	{
		error('unknown cmd='.$_GET['cmd']);
	}
?>
