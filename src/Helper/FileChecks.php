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
    private $fileObj;
    private $actualDirectory;
    private $settings;
    private static $errorCodes = array(
        0 => "general error",
        1 => "duplications"
    );

    /**
     * 
     * @param string $actualDirectory
     * @param object $fileObj
     * @param \OEAW\Object\SettingsObject $settings
     * @return array
     */
    public function start(string $actualDirectory, object $fileObj, \OEAW\Object\SettingsObject $settings): array {
        $this->fileObj = $fileObj;
        $this->actualDirectory = $actualDirectory;
        $this->settings = $settings;
        $this->obj = $fileObj;
        $this->runChecks();
        return array("fileObj" => $this->obj, "errors" => $this->errors);
    }

    private function runChecks() {

        if ($this->obj->isZipFile()) {
            $this->checkZipFiles();
        }

        if ($this->obj->isPdfFile()) {
            $this->checkPdfFile();
        }

        //mime
        $this->checkMimeWithExtensionBySignatures();

        //rar
        $this->isRarFile();

        //blacklist
        $this->checkBlackList();

        //check PW protected XLSX, DOCX
        $this->isPasswordProtectedXLSXDOCX();

        //bagit
        if ($this->obj->isBagItFile()) {
            // $this->checkBagitFile();
        }

        $this->isFileDamaged();
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
                $this->error[] = array("errorType" => "Zip_Password_Error",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
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

            $this->error[] = array("errorType" => "Bagit File error:" . $ex->getMessage(), "errorCode" => 0,  "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    private function isPasswordProtectedXLSXDOCX(): void {
        if (($this->obj->getExtension() == "xlsx" || $this->obj->getExtension() == "docx") && $this->obj->getType() == "application/CDFV2-encrypted") {
            $this->error[] = array("errorType" => "This document (XLSX,DOCX) is password protected",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    private function isRarFile(): void {
        if ($this->obj->getExtension() == "rar" || $this->obj->getType() == "application/rar") {
            $this->error[] = array("errorType" => "This is a RAR file! Please check it manually!",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

    private function isFileDamaged(): void {
        if ($this->obj->getSize() < 0) {
            $this->error[] = array("errorType" => "File damaged! Please check it manually!",  "errorCode" => 0, "filename" => $this->obj->getFileName(), "dir" => $this->obj->getDirectory());
        }
    }

}
