<?php
	if (! isset($_GET['cmd'])) exit;
	if ($_GET['cmd'] == "getFiles" && isset($_GET['conf']))
	{
		foreach (glob(dirname(__FILE__)."/confs/".$_GET['conf']."/*") as $file)
		{
			echo "$file \r\n";
		}
	}
?>
