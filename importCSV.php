<?php
$file = $argv[1];
include_once('ClassesPHP/ImportCSV.class.php');
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

include_once('ClassesPHP/ConnectDB.class.php');
$conDB = new ConnectDB;

$res = $conDB->createDB();
if ($res==TRUE) echo("База существует\n");
    else {
        echo $conDB->getErr();
        die("База не существует\n");
        }

include_once('ClassesPHP/ProfessionDB.class.php');
$profDB = new ProfessionDB;
$profDB->setConnectDB($conDB);
$res = $profDB->createTable();
if ($res==TRUE) echo("Таблица Profession существует\n");
    else {
        echo $profDB->getErr();
        die("Таблица Profession не существует\n");
        }

include_once('ClassesPHP/RegionDB.class.php');
$regionDB = new RegionDB;
$regionDB->setConnectDB($conDB);
$res = $regionDB->createTable();
if ($res==TRUE) echo("Таблица Region существует\n");
    else {
        echo $regionDB->getErr();
        die("Таблица Region не существует\n");
        }

include_once('ClassesPHP/CityDB.class.php');
$cityDB = new CityDB;
$cityDB->setConnectDB($conDB);
$res = $cityDB->createTable();
if ($res==TRUE) echo("Таблица City существует\n");
    else {
        echo $cityDB->getErr();
        die("Таблица City не существует\n");
        }

include_once('ClassesPHP/StaffDB.class.php');
$staffDB = new StaffDB;
$staffDB->setConnectDB($conDB);
$res = $staffDB->createTable();
if ($res==TRUE) echo("Таблица Staff существует\n");
    else {
        echo $cityDB->getErr();
        die("Таблица Staff не существует\n");
        }

$importData = $icsv->getImportData();
if (!is_array($importData)) {
    die("Нет данных импорта\n");
    }

$arrayProfession = array_column($importData, 7);
unset($arrayProfession[0]);
$res = $profDB->put($arrayProfession);
if ($res==TRUE) echo("Профессии помещены в базу\n");
    else {
        echo $conDB->getErr();
        die("Ошибки записи профессий\n");
        }

$arrayRegion = array_column($importData, 8);
unset($arrayRegion[0]);
$res = $regionDB->put($arrayRegion);
if ($res==TRUE) echo("Регионы помещены в базу\n");
    else {
        echo $conDB->getErr();
        die("Ошибки записи регионов\n");
        }

$arrayCity = array_column($importData, 9);
unset($arrayCity[0]);
$res = $cityDB->put($arrayCity);
if ($res==TRUE) echo("Города помещены в базу\n");
    else {
        echo $conDB->getErr();
        die("Ошибки записи городов\n");
        }

$staffDB->setProfDB($profDB);
$staffDB->setRegionDB($regionDB);
$staffDB->setCityDB($cityDB);
$res = $staffDB->put($importData);
if ($res==TRUE) echo("Сотрудники помещены в базу\n");
    else {
        echo $conDB->getErr();
        die("Ошибки записи сотрудников\n");
        }
?>
