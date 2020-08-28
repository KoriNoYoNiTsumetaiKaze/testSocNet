<?php
include_once('GuideDB.class.php');
class RegionDB extends GuideDB{
    
    public function __construct(){
        $this->conDB     = NULL;
        $this->err       = '';
        $this->tableName = 'Region';
        }
    }
?>
