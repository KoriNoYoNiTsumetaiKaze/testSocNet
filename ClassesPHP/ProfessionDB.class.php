<?php
include_once('GuideDB.class.php');
class ProfessionDB extends GuideDB{
    
    public function __construct(){
        $this->conDB     = NULL;
        $this->err       = '';
        $this->tableName = 'Profession';
        }
    }
?>
