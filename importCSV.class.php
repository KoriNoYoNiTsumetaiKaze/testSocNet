<?php
class ImportCSV {
    private $fileName;
    private $err;
    private $progress;
    
    public function __construct(){
        $this->fileName = '';
        $this->err      = '';
        $this->progress = 0;
        }

    public function setFileName($inFile){
        if (!is_string($inFile)){
            $this->setErrTxt('Не верный путь к файлу. Ожидается строка полного пути и имени файла с расширением');
            return FALSE;
            }
        if (trim($inFile)==''){
            $this->setErrTxt('Не задан путь к файлу. Ожидается строка полного пути и имени файла с расширением');
            return FALSE;
            }
        if (!file_exists($inFile)){
            $this->setErrTxt('Файл не сущетвует. Ожидается строка полного пути и имени файла с расширением');
            return FALSE;
            }
        $this->fileName = $inFile;
        return TRUE;
        }

    public function getFileName(){
        return $this->fileName;
        }

    private function setErrTxt($txt=''){
        if (trim($txt)=='') return FALSE;
        $this->err .= "\n".date('Y.m.d H:i:s').' - '.trim($txt)."\n";
        return TRUE;
        }

    public function getErr(){
        return $this->err;
        }

    public function getProgress(){
        return $this->progress;
        }

    public function importFile($showProgress=TRUE){
        $file = $this->getFileName();
        if (trim($file)==''){
            $this->setErrTxt('Не верный путь к файлу. Ожидается строка полного пути и имени файла с расширением');
            return FALSE;
            }
        if (!file_exists($file)){
            $this->setErrTxt('Файл не сущетвует. Ожидается строка полного пути и имени файла с расширением');
            return FALSE;
            }
        $fp = fopen($file, "r");
        if ($fp===FALSE) {
            $this->setErrTxt('Файл не открыт для чтения.');
            return FALSE;
            }
        $countLines     = count(file($file));
        $step           = $countLines/100;
        $progress       = 0;
        $this->progress = $progress;
        while (($data = fgetcsv($fp, 0, ";")) !== FALSE) {
            if ($showProgress==TRUE) print_r(chr(8).chr(8).chr(8).chr(8));
            $progress = floor($progress+$step);
            if ($progress>100) $progress = 100;
            if ($showProgress==TRUE) print_r((string)$progress."%");
            $this->progress = $progress;
            }
        fclose($fp);
        return TRUE;
        }
    }
?>
