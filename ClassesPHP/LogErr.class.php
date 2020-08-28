<?php
class LogErr {
    private $err;
    
    public function __construct(){
        $this->err = '';
        }

    protected function setErrTxt($txt=''){
        if (trim($txt)=='') return FALSE;
        $this->err .= "\n".date('Y.m.d H:i:s').' - '.trim($txt)."\n";
        return TRUE;
        }

    public function getErr(){
        return $this->err;
        }

    public function clearErr(){
        $this->err = '';
        }
    }
?>
