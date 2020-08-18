<?php
$file = "import.csv";
$countLines = count(file($file));
//print_r($countLines);
$step = $countLines/100;
$progress = 0;
if (($fp = fopen($file, "r")) !== FALSE) {
	while (($data = fgetcsv($fp, 0, ";")) !== FALSE) {
        print_r(chr(8).chr(8).chr(8).chr(8));
		$list[] = $data;
        $progress = floor($progress+$step);
        if ($progress>100) $progress = 100;
        print_r((string)$progress."%");
        //sleep(1);
	}
	fclose($fp);
	//print_r($list);
}
?>
