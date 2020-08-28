<?php
include_once('GuideDB.class.php');
class CityDB extends GuideDB{
    
    public function __construct(){
        $this->conDB     = NULL;
        $this->err       = '';
        $this->tableName = 'City';
        }
    }
?>
