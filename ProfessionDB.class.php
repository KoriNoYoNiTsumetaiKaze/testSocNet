<?php
include_once('LogErr.class.php');
class ProfessionDB extends LogErr{
    private $conDB;
    
    public function __construct(){
        $this->conDB = NULL;
        $this->err   = '';
        }
    
    public function setConnectDB($inConDB) {
        $this->conDB = $inConDB;
        }
    
    public function getConnectDB() {
        return $this->conDB;
        }

    public function put($inData) {
        $conDB  = $this->getConnectDB();
        if ($conDB==NULL) {
            $this->setErrTxt("Нет объекта подклчения к базе данных");
            return FALSE;
            }
        $arrayProfession = array();
        foreach ($inData as $v) {
            if (trim($v)=="") continue;
            $res = $conDB->convertToSearch($v);
            if (!array_key_exists($res, $arrayProfession)) {
                $arrayProfession[$res] = $v;
                }
            }
        $sql = "SELECT id, name FROM Profession";
        $res = $conDB->sql($sql);
        if ($res==FALSE) {
            $this->setErrTxt("Нет данных таблицы Profession");
            return FALSE;
            }
        $arrayUpdate = array();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            $v = $conDB->convertToSearch($row['name']);
            if (array_key_exists($v, $arrayProfession)) {
                if (trim($row['name'])!=trim($arrayProfession[$v])) {
                    $arrayUpdate[$row['id']] = $arrayProfession[$v];
                    }
                unset($arrayProfession[$v]);
                }
            }
        $flagRes = TRUE;
        if (count($arrayUpdate)>0) {
            $sql = "UPDATE Profession SET name = :name WHERE id = :id";
            foreach($arrayUpdate as $k => $v) {
                $param['id']   = $k;
                $param['name'] = $v;
                $res = $conDB->sqlParam($sql,$param);
                if ($res==FALSE) {
                    $this->setErrTxt("Ошибка обновления таблицы Profession");
                    $flagRes = FALSE;
                    }
                }
            }
        if (count($arrayProfession)>0) {
            $sql = "INSERT Profession(name) VALUES";
            foreach ($arrayProfession as $v) {
                $sql .= "('$v'),";
                }
            $sql = substr($sql,0,-1);
            $res = $conDB->sql($sql);
            if ($res==FALSE) {
                $this->setErrTxt("Ошибка добавления в таблицу Profession");
                $flagRes = FALSE;
                }
            }
        return $flagRes;
        }
    }
?>
