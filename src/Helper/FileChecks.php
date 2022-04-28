<?php

namespace OEAW\Helper;

/**
 * Description of FileChecks
 *
 * @author nczirjak
 */
class FileChecks {

    private $obj;
    private $errors = [];
    private $files;
    private $settings;
    private $progressBar;
    private static $errorCodes = array(
        0 => "general error",
        1 => "duplications"
    );

    /**
     * 
     * @param array $files
     * @param \OEAW\Object\SettingsObject $settings
     * @return array
     */
    public function start(array $files, \OEAW\Object\SettingsObject $settings): array {
        $this->files = $files;
        $this->settings = $settings;
        $this->runChecks();
        return $this->errors;
    }

    private function runChecks() {

        echo "check duplications... \n";
        $this->checkDuplications();
        
        $this->progressBar = new \ProgressBar\Manager(0, count($this->files));
        foreach($this->files as $k => $obj) {
            
            echo $obj->getFileName()."\n";
                
            
            $this->obj = $obj;
            echo "zip check... \n";
            if ($this->obj->isZipFile()) {
                $this->checkZipFiles();
            }

            echo "pdf check... \n";
            if ($this->obj->isPdfFile()) {
                $this->checkPdfFile();
            }

            echo "mime check... \n";
            $this->checkMimeWithExtensionBySignatures();

            echo "rar check... \n";
            $this->isRarFile();

            echo "blacklist check... \n";
            $this->checkBlackList();

            echo "password protected XLS DOCX check... \n";
            $this->isPasswordProtectedXLSXDOCX();

            echo "bagit file check... \n";
            if ($this->obj->isBagItFile()) {
                 $this->checkBagitFile();
            }
            echo "Damaged file check... \n";
            $this->isFileDamaged();
            
            echo "Check XML.. \n";
            $this->checkXML();
            
            $this->progressBar->advance();
            echo "\n";
            
            echo '<pre>';
            var_dump($this->errors);
            echo '</pre>';
        }
    }

    private function checkDuplications() {

        $new = array();
        //we get the filenames
        foreach ($this->files as $k => $value) {
            $new[$k] = serialize(strtolower($value->getFileName()));
        }

        //remove the duplicates
        $sorted = array_unique($new);

        foreach ($this->files as $k => $v) {
            //check which key is missing from the list
            if (!isset($sorted[$k])) {
                $this->errors[] = array("errorType" => "Duplicated Filename!", "errorCode" => 1, "filename" => $v->getFileName(), "dir" => $v->getDirectory());
            }
        }
    }
    
    /**
     * Check blacklisted files
     * @return void
     */
    private function checkBlackList(): void {

        foreach ($this->settings->getBlackList() as $bl) {
            if (strtolower($bl) == strtolower($this->obj->getExtension())) {
                $this->errors[] = array("errorType" => "File is black listed",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
            }
        }
    }

    /**
     * Check if the mime is equal with the signature files provied data
     * @return void
     */
    private function checkMimeWithExtensionBySignatures(): void {
        if (!$this->settings->getMimeTypeByExtension($this->obj->getExtension())) {
            $this->errors[] = array("errorType" => "Extension is wrong",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    /**
     * check the zip files, we extract them to know if it is pwd protected or not
     * 
     * If we have a pw protected one then we will put it to the $pwZips array
     * 
     */
    private function checkZipFiles() {

        $za = new \ZipArchive();
        if ($za->open($this->obj->getFilenameAndDir(), \ZIPARCHIVE::CREATE) !== TRUE) {
            $this->error[] = array("errorType" => "Zip_Open_Error",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        } else {
            $za->extractTo($this->settings->getTmpDir());
            //the zip file has a password
            if ($za->status == 26) {
                //$pwZips[] = $f;
                $this->errors[] = array("errorType" => "Zip_Password_Error",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
            }
            //get the files in the tmpDir and remove them
            $files = glob($this->settings->getTmpDir() . '\*'); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file))
                    unlink($file); // delete file
            }
        }
    }

    private function checkPdfFile(): void {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $parser->parseFile($this->obj->getFilenameAndDir());
        } catch (\Exception $ex) {
            $this->errors[] = array("errorType" => "PDF error:" . $ex->getMessage(), "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory(), "errorMSG" => $ex->getMessage());
        }
    }

    private function checkBagitFile(): void {

        try {
            $bag = \whikloj\BagItTools\Bag::load('./aa');
        } catch (\Exception | \whikloj\BagItTools\Exceptions\BagItException $ex) {

            $this->errors[] = array("errorType" => "Bagit File error:" . $ex->getMessage(), "errorCode" => 0,  "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    private function isPasswordProtectedXLSXDOCX(): void {
        if (($this->obj->getExtension() == "xlsx" || $this->obj->getExtension() == "docx") && $this->obj->getType() == "application/CDFV2-encrypted") {
            $this->errors[] = array("errorType" => "This document (XLSX,DOCX) is password protected",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    private function isRarFile(): void {
        if ($this->obj->getExtension() == "rar" || $this->obj->getType() == "application/rar") {
            $this->errors[] = array("errorType" => "This is a RAR file! Please check it manually!",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    private function isFileDamaged(): void {
        if ($this->obj->getSize() < 0) {
            $this->errors[] = array("errorType" => "File damaged! Please check it manually!",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }
    
    private function checkXML(): void {
        if ($this->obj->getExtension() == "xml" || $this->obj->getType() == "text/xml" ) {
            $xml = new \OEAW\Helper\XMLChecker($this->obj->getFilenameAndDir(), $this->obj->getDirectory());
            if($xml->validateXML() === false) {
                $this->errors[] = $xml->getErrors();
            }
        }
    }
    
    

}
