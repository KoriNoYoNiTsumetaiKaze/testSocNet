<?php
$file = "import.csv";
include_once('importCSV.class.php');
$icsv = new ImportCSV;
$res = $icsv->setFileName($file);
if ($res==FALSE) die("Файл не установлен");
$res = $icsv->importFile();
if ($res==TRUE) echo("Файл импортирован");
    else echo("Файл не импортирован");
?>
