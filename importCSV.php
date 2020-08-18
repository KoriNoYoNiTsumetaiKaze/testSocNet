<?php
if (($fp = fopen("import.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($fp, 0, ";")) !== FALSE) {
		$list[] = $data;
	}
	fclose($fp);
	print_r($list);
}
?>
