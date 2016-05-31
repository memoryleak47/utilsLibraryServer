<?php
	include "local.php";

	if ($dbhost == "" or $dbuser == "" or $dbpass == "" or $db == "")
	{
		echo "important database variable unset";
		exit;
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
?>
