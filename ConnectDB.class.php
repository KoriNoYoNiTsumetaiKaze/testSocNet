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

    public function createProfession() {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $sql = "SHOW TABLES LIKE 'Profession'";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll();
            if (count($result)>0) return TRUE;
            
            $sql = "CREATE TABLE Profession ( ID INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, PRIMARY KEY (ID))";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            } catch (PDOException $e) {
                $this->setErrTxt('Cоздание таблицы Profession не удалось: '.$e->getMessage());
                return FALSE;
                }
        return TRUE;
        }

    public function createRegion() {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $sql = "SHOW TABLES LIKE 'Region'";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll();
            if (count($result)>0) return TRUE;
            
            $sql = "CREATE TABLE Region ( ID INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, PRIMARY KEY (ID))";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            } catch (PDOException $e) {
                $this->setErrTxt('Cоздание таблицы Region не удалось: '.$e->getMessage());
                return FALSE;
                }
        return TRUE;
        }

    public function createCity() {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $sql = "SHOW TABLES LIKE 'City'";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll();
            if (count($result)>0) return TRUE;
            
            $sql = "CREATE TABLE City ( ID INT NOT NULL AUTO_INCREMENT, name VARCHAR(255) NOT NULL, PRIMARY KEY (ID))";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            } catch (PDOException $e) {
                $this->setErrTxt('Cоздание таблицы City не удалось: '.$e->getMessage());
                return FALSE;
                }
        return TRUE;
        }

    public function createStaff() {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $sql = "SHOW TABLES LIKE 'Staff'";
            $stm = $pdo->prepare($sql);
            $stm->execute();
            $result = $stm->fetchAll();
            if (count($result)>0) return TRUE;
            
            $sql = "CREATE TABLE Staff ( ID INT NOT NULL AUTO_INCREMENT,
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
            $stm = $pdo->prepare($sql);
            $stm->execute();
            } catch (PDOException $e) {
                $this->setErrTxt('Cоздание таблицы Staff не удалось: '.$e->getMessage());
                return FALSE;
                }
        return TRUE;
        }
    
    public function convertToSearch($inTxT="") {
        $outTxT = trim($inTxT);
        $outTxT = strtolower($outTxT);
        while (strpos($outTxT, "  ")!==FALSE) {
            $outTxT = str_replace("  ", " ", $outTxT);
            }
        return $outTxT;
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

    public function sqlParam($sql,$param) {
        $pdo = $this->getPDO();
        if ($pdo==FALSE) {
                $this->setErrTxt('Подключение к базе данных не получено');
                return FALSE;
                }
        try {
            $stm = $pdo->prepare($sql);
            $stm->execute($param);
            } catch (PDOException $e) {
                $this->setErrTxt('Не удалось выполнить запрос: '.$e->getMessage());
                return FALSE;
                }
        return TRUE;
        }

    }
?>
