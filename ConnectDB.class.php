<?php
include_once('LogErr.class.php');
class ConnectDB extends LogErr{
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;
    private $dsn;
    private $opt;
    private $pdo;
    
    public function __construct(){
        $this->host    = '127.0.0.1';
        $this->db      = 'testSocNet';
        $this->user    = 'root';
        $this->pass    = 'Admin2@19';
        $this->charset = 'utf8';
        $this->dsn     = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $this->opt     = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            ];
        $this->pdo = NULL;
        $this->err = '';
        }
    
    public function getPDO() {
        if ($this->pdo!=NULL) return $this->pdo;
        try {
            $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->opt);
            } catch (PDOException $e) {
                $this->pdo = NULL;
                $this->setErrTxt('Подключение не удалось: '.$e->getMessage());
                return FALSE;
                }
        return $this->pdo;
        }
    
    public function createDB() {
        $dsn = "mysql:host=$this->host;charset=$this->charset";
        try {
            $pdo = new PDO($dsn, $this->user, $this->pass, $this->opt);
            } catch (PDOException $e) {
                $this->setErrTxt('Подключение для создания базы данных не удалось: '.$e->getMessage());
                return FALSE;
                }
        try {
            $sql = "CREATE DATABASE IF NOT EXISTS ".$this->db;
            $stm = $pdo->prepare($sql);
            $stm->execute();
            } catch (PDOException $e) {
                $this->setErrTxt('Cоздание базы данных не удалось: '.$e->getMessage());
                return FALSE;
                }
        return TRUE;
        }
    
    public function convertToSearch($inTxT="") {
        $outTxT = trim($inTxT);
        $outTxT = mb_strtolower($outTxT);
        while (strpos($outTxT, "  ")!==FALSE) {
            $outTxT = str_replace("  ", " ", $outTxT);
            }
        return $outTxT;
        }

    public function convertToSearchArray($inArr) {
        if (!is_array($inArr)) {
            $this->setErrTxt("Входящие данные не массив конвертация для поиска не возможна");
            return FALSE;
            }
        $outArr = array();
        foreach($inArr as $k => $v) {
            $outArr[$k] = $this->convertToSearch($v);
            }
        return $outArr;
        }

    public function sql($sql) {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $stm = $pdo->prepare($sql);
            $stm->execute();
            return $stm;
            } catch (PDOException $e) {
                $this->setErrTxt('Не удалось выполнить запрос: '.$e->getMessage());
                return FALSE;
                }
        }

    public function sqlArrayAll($sql) {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $stm = $pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll();
            } catch (PDOException $e) {
                $this->setErrTxt('Не удалось выполнить запрос: '.$e->getMessage());
                return FALSE;
                }
        }

    public function sqlParam($sql,$param) {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $stm = $pdo->prepare($sql);
            $stm->execute($param);
            return $stm;
            } catch (PDOException $e) {
                $this->setErrTxt('Не удалось выполнить запрос: '.$e->getMessage());
                return FALSE;
                }
        }

    public function sqlParamArrayAll($sql,$param) {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $stm = $pdo->prepare($sql);
            $stm->execute($param);
            return $stm->fetchAll();
            } catch (PDOException $e) {
                $this->setErrTxt('Не удалось выполнить запрос: '.$e->getMessage());
                return FALSE;
                }
        }

    }
?>
