<?php
include_once('LogErr.class.php');
abstract class GuideDB extends LogErr{
    protected $conDB;
    protected $tableName;
    
    public function setConnectDB($inConDB) {
        $this->conDB = $inConDB;
        }
    
    public function getConnectDB() {
        return $this->conDB;
        }

    public function getTableName() {
        return $this->tableName;
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
        $sql = "CREATE TABLE ".$this->getTableName()." ( ID INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, PRIMARY KEY (ID))";
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
        $sql = "SELECT id, name FROM ".$this->getTableName();
        return $conDB->sql($sql);
        }

    public function selectTableNameArrayAll() {
        $conDB  = $this->getConnectDB();
        if ($conDB==NULL) {
            $this->setErrTxt("Нет объекта подклчения к базе данных");
            return FALSE;
            }
        $sql = "SELECT id, name FROM ".$this->getTableName();
        $res = $conDB->sqlArrayAll($sql);
        $arrayRes = array();
        foreach ($res as $v) {
            $arrayRes[$v['id']] = $v['name'];
            }
        return $arrayRes;
        }

    public function put($inData) {
        $conDB  = $this->getConnectDB();
        if ($conDB==NULL) {
            $this->setErrTxt("Нет объекта подклчения к базе данных");
            return FALSE;
            }
        $arrayTableName = array();
        foreach ($inData as $v) {
            if (trim($v)=="") continue;
            $res = $conDB->convertToSearch($v);
            if (!array_key_exists($res, $arrayTableName)) {
                $arrayTableName[$res] = $v;
                }
            }
        $res = $this->selectTableNameAll();
        if ($res==FALSE) {
            $this->setErrTxt("Нет данных таблицы ".$this->getTableName());
            return FALSE;
            }
        $arrayUpdate = array();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            $v = $conDB->convertToSearch($row['name']);
            if (array_key_exists($v, $arrayTableName)) {
                if (trim($row['name'])!=trim($arrayTableName[$v])) {
                    $arrayUpdate[$row['ID']] = $arrayTableName[$v];
                    }
                unset($arrayTableName[$v]);
                }
            }
        $flagRes = TRUE;
        if (count($arrayUpdate)>0) {
            $sql = "UPDATE ".$this->getTableName()." SET name = :name WHERE id = :id";
            foreach($arrayUpdate as $k => $v) {
                $param['id']   = $k;
                $param['name'] = $v;
                $res = $conDB->sqlParam($sql,$param);
                if ($res==FALSE) {
                    $this->setErrTxt("Ошибка обновления таблицы ".$this->getTableName());
                    $flagRes = FALSE;
                    }
                }
            }
        if (count($arrayTableName)>0) {
            $sql = "INSERT ".$this->getTableName()."(name) VALUES";
            foreach ($arrayTableName as $v) {
                $sql .= "('$v'),";
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
