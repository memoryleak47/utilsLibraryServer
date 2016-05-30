<?php
	if (! isset($_GET['cmd'])) exit;

	if ($_GET['cmd'] == "getFiles" && isset($_GET['conf']))
	{
		echo "confs/".$_GET['conf'].'<br/>';
		foreach (glob("confs/".$_GET['conf']."/*") as $file)
		{
			echo $file;
		}
	}
?>
