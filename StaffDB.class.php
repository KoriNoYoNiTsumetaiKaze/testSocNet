<?php
include_once('GuideDB.class.php');
class StaffDB extends GuideDB{
    private $profDB;
    private $regionDB;
    private $cityDB;
    
    public function __construct(){
        $this->conDB     = NULL;
        $this->err       = '';
        $this->tableName = 'Staff';
        $this->profDB    = NULL;
        $this->regionDB  = NULL;
        $this->cityDB    = NULL;
        }

    public function setProfDB($inProfDB) {
        $this->profDB = $inProfDB;
        }
    
    public function getProfDB() {
        return $this->profDB;
        }

    public function setRegionDB($inRegionDB) {
        $this->regionDB = $inRegionDB;
        }
    
    public function getRegionDB() {
        return $this->regionDB;
        }
    public function setCityDB($inCityDB) {
        $this->cityDB = $inCityDB;
        }
    
    public function getCityDB() {
        return $this->cityDB;
        }

    public function createTable() {
        $conDB  = $this->getConnectDB();
        if ($conDB==NULL) {
            $this->setErrTxt("Нет объекта подклчения к базе данных");
            return FALSE;
            }
        $sql = "SHOW TABLES LIKE '".$this->getTableName()."'";
        $res = $conDB->sqlArrayAll($sql);
        if ($res===FALSE) {
                $this->setErrTxt("Ошибка проверки наличия таблицы ".$this->getTableName()." ".$conDB->getErr());
                return FALSE;
                }
        if (count($res)>0) return TRUE;
        $sql = "CREATE TABLE ".$this->getTableName()." (ID INT NOT NULL AUTO_INCREMENT,
                                            ACTIVE BOOL DEFAULT TRUE NOT NULL,
                                            name VARCHAR(255),
                                            LAST_NAME VARCHAR(255),
                                            EMAIL VARCHAR(255),
                                            XML_ID VARCHAR(255),
                                            PERSONAL_GENDER ENUM('F', 'M') DEFAULT 'M' NOT NULL,
                                            PERSONAL_BIRTHDAY DATE,
                                            ProfessionId INT,
                                            RegionId INT,
                                            CityId INT,
                                            FOREIGN KEY (ProfessionId) REFERENCES Profession (ID) ON DELETE RESTRICT,
                                            FOREIGN KEY (RegionId) REFERENCES Region (ID) ON DELETE RESTRICT,
                                            FOREIGN KEY (CityId) REFERENCES City (ID) ON DELETE RESTRICT,
                                            PRIMARY KEY (ID))";
        $res = $conDB->sql($sql);
        if ($res==FALSE) {
                $this->setErrTxt("Ошибка создания таблицы ".$this->getTableName()." ".$conDB->getErr());
                return FALSE;
                }
        return TRUE;
        }

    public function selectTableNameAll() {
        $conDB  = $this->getConnectDB();
        if ($conDB==NULL) {
            $this->setErrTxt("Нет объекта подклчения к базе данных");
            return FALSE;
            }
        $sql = "SELECT ID, ACTIVE, name, LAST_NAME, EMAIL, XML_ID,
                        PERSONAL_GENDER, PERSONAL_BIRTHDAY,
                        ProfessionId, RegionId, CityId FROM ".$this->getTableName();
        return $conDB->sql($sql);
        }

    public function put($inData) {
        $conDB  = $this->getConnectDB();
        if ($conDB==NULL) {
            $this->setErrTxt("Нет объекта подклчения к базе данных");
            return FALSE;
            }
        $profDB = $this->getProfDB();
        $arrayProfession = $profDB->selectTableNameArrayAll();
        if ($arrayProfession==FALSE) {
            $this->setErrTxt("Нет данных таблицы ".$profDB->getTableName());
            $arrayProfession = array();
            }
        $arrayProfession = $conDB->convertToSearchArray($arrayProfession);
        $regionDB = $this->getRegionDB();
        $arrayRegion = $regionDB->selectTableNameArrayAll();
        if ($arrayRegion==FALSE) {
            $this->setErrTxt("Нет данных таблицы ".$regionDB->getTableName());
            $arrayRegion = array();
            }
        $arrayRegion = $conDB->convertToSearchArray($arrayRegion);
        $cityDB = $this->getCityDB();
        $arrayCity = $cityDB->selectTableNameArrayAll();
        if ($arrayCity==FALSE) {
            $this->setErrTxt("Нет данных таблицы ".$cityDB->getTableName());
            $arrayCity = array();
            }
        $arrayCity = $conDB->convertToSearchArray($arrayCity);
        $arrayTableName = array();
        foreach ($inData as $v) {
            $k = $v[4];
            if (trim($k)=="") continue;
            if (trim(strtoupper($k))=="XML_ID") continue;
            $v[0] = (trim(strtoupper($v[0]))=="Y" ? 1 : 0);
            $v[6] = date('Y-m-d',strtotime($v[6]));
            $v[7] = $conDB->convertToSearch($v[7]);
            $v[7] = array_search($v[7], $arrayProfession);
            if ($v[7]==FALSE) $v[7] = NULL;
            $v[8] = $conDB->convertToSearch($v[8]);
            $v[8] = array_search($v[8], $arrayRegion);
            if ($v[8]==FALSE) $v[8] = NULL;
            $v[9] = $conDB->convertToSearch($v[9]);
            $v[9] = array_search($v[9], $arrayCity);
            if ($v[9]==FALSE) $v[9] = NULL;
            $arrayTableName[$k] = $v;
            }
        $res = $this->selectTableNameAll();
        if ($res==FALSE) {
            $this->setErrTxt("Нет данных таблицы ".$this->getTableName());
            return FALSE;
            }
        $arrayUpdate = array();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            $k = $row['XML_ID'];
            if (array_key_exists($k, $arrayTableName)) {
                $v = $arrayTableName[$k];
                if (
                    $row['ACTIVE']!=$v[0]
                    || (trim($row['NAME'])!=trim($v['1']))
                    || (trim($row['LAST_NAME'])!=trim($v['2']))
                    || (trim($row['EMAIL'])!=trim($v['3']))
                    || (trim($row['PERSONAL_GENDER'])!=trim($v['5']))
                    || (trim($row['PERSONAL_BIRTHDAY'])!=trim($v[6]))
                    || ($row['ProfessionId']!=$v[7])
                    || ($row['RegionId']!=$v[8])
                    || ($row['CityId']!=$v[9])
                    ) {
                    $arrayUpdate[$row['ID']] = $v;
                    }
                unset($arrayTableName[$k]);
                }
            }
        $flagRes = TRUE;
        if (count($arrayUpdate)>0) {
            $sql = "UPDATE ".$this->getTableName()." SET ACTIVE=:active,
                                                        name=:name,
                                                        LAST_NAME=:lastName,
                                                        EMAIL=:email,
                                                        XML_ID=:xmlId,
                                                        PERSONAL_GENDER=:personalGender,
                                                        PERSONAL_BIRTHDAY=:personalBirthday,
                                                        ProfessionId=:ProfessionId,
                                                        RegionId=:RegionId,
                                                        CityId=:CityId
                                                    WHERE id = :id";
            foreach($arrayUpdate as $k => $v) {
                $param['id']               = $k;
                $param['active']           = $v[0];
                $param['name']             = $v[1];
                $param['lastName']         = $v[2];
                $param['email']            = $v[3];
                $param['xmlId']            = $v[4];
                $param['personalGender']   = $v[5];
                $param['personalBirthday'] = $v[6];
                $param['ProfessionId']     = $v[7];
                $param['RegionId']         = $v[8];
                $param['CityId']           = $v[9];
                $res = $conDB->sqlParam($sql,$param);
                if ($res==FALSE) {
                    $this->setErrTxt("Ошибка обновления таблицы ".$this->getTableName());
                    $flagRes = FALSE;
                    }
                }
            }
        if (count($arrayTableName)>0) {
            $sql = "INSERT ".$this->getTableName()."(ACTIVE, name,
                        LAST_NAME, EMAIL, XML_ID, PERSONAL_GENDER,
                        PERSONAL_BIRTHDAY,ProfessionId,
                        RegionId,CityId) VALUES";
            foreach ($arrayTableName as $v) {
                if ($v[7]==NULL) $ProfessionId = 'NULL';
                    else $ProfessionId = "'$v[7]'";
                if ($v[8]==NULL) $RegionId = 'NULL';
                    else $RegionId = "'$v[8]'";
                if ($v[9]==NULL) $CityId = 'NULL';
                    else $CityId = "'$v[9]'";
                $sql .= "('$v[0]','$v[1]','$v[2]','$v[3]','$v[4]','$v[5]','$v[6]',$ProfessionId,$RegionId,$CityId),";
                }
            $sql = substr($sql,0,-1);
            $res = $conDB->sql($sql);
            if ($res==FALSE) {
                $this->setErrTxt("Ошибка добавления в таблицу ".$this->getTableName());
                $flagRes = FALSE;
                }
            }
        return $flagRes;
        }

    }
?>
