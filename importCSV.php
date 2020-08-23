<?php
$file = "import.csv";
include_once('ImportCSV.class.php');
$icsv = new ImportCSV;
$res = $icsv->setFileName($file);
if ($res==FALSE) {
    echo $icsv->getErr();
    die("Файл не установлен\n");
    }
$res = $icsv->importFile();
if ($res==TRUE) echo("Файл импортирован\n");
    else {
        echo $icsv->getErr();
        die("Файл не импортирован\n");
        }

include_once('ConnectDB.class.php');
$conDB = new ConnectDB;

$res = $conDB->createDB();
if ($res==TRUE) echo("База существует\n");
    else {
        echo $conDB->getErr();
        die("База не существует\n");
        }

$res = $conDB->createProfession();
if ($res==TRUE) echo("Таблица Profession существует\n");
    else {
        echo $conDB->getErr();
        die("Таблица Profession не существует\n");
        }

$res = $conDB->createRegion();
if ($res==TRUE) echo("Таблица Region существует\n");
    else {
        echo $conDB->getErr();
        die("Таблица Region не существует\n");
        }

$res = $conDB->createCity();
if ($res==TRUE) echo("Таблица City существует\n");
    else {
        echo $conDB->getErr();
        die("Таблица City не существует\n");
        }

$res = $conDB->createStaff();
if ($res==TRUE) echo("Таблица Staff существует\n");
    else {
        echo $conDB->getErr();
        die("Таблица Staff не существует\n");
        }

$importData = $icsv->getImportData();
if (!is_array($importData)) {
    die("Нет данных импорта\n");
    }

$arrayProfession = array_column($importData, 7);
unset($arrayProfession[0]);
include_once('ProfessionDB.class.php');
$profDB = new ProfessionDB;
$profDB->setConnectDB($conDB);
$res = $profDB->put($arrayProfession);
if ($res==TRUE) echo("Профессии помещены в базу\n");
    else {
        echo $conDB->getErr();
        die("Ошибки записи профессий\n");
        }
$arrayRegion = array_column($importData, 8);
unset($arrayRegion[0]);
$arrayCity = array_column($importData, 9);
unset($arrayCity[0]);
?>
