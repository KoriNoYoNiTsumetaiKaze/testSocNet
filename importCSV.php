<?php
$file = "import.csv";
include_once('ImportCSV.class.php');
$icsv = new ImportCSV;
$res = $icsv->setFileName($file);
if ($res==FALSE) {
    echo $icsv->getErr();
    die("Файл не установлен");
    }
$res = $icsv->importFile();
if ($res==TRUE) echo("Файл импортирован");
    else {
        echo $icsv->getErr();
        die("Файл не импортирован");
        }

include_once('ConnectDB.class.php');
$conDB = new ConnectDB;

$res = $conDB->createDB();
if ($res==TRUE) echo("База существует");
    else {
        echo $conDB->getErr();
        die("База не существует");
        }

$res = $conDB->createProfession();
if ($res==TRUE) echo("Таблица Profession существует");
    else {
        echo $conDB->getErr();
        die("Таблица Profession не существует");
        }

$res = $conDB->createRegion();
if ($res==TRUE) echo("Таблица Region существует");
    else {
        echo $conDB->getErr();
        die("Таблица Region не существует");
        }

$res = $conDB->createCity();
if ($res==TRUE) echo("Таблица City существует");
    else {
        echo $conDB->getErr();
        die("Таблица City не существует");
        }

$res = $conDB->createStaff();
if ($res==TRUE) echo("Таблица Staff существует");
    else {
        echo $conDB->getErr();
        die("Таблица Staff не существует");
        }

?>
