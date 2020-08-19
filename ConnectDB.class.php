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
    }
?>
