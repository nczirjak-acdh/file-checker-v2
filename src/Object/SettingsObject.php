<?php

namespace OEAW\Object;

/**
 * Description of SettingsObject
 *
 * @author nczirjak
 */
class SettingsObject {

    private $signatureDir;
    private $tmpDir;
    private $reportDir;
    private $blackList;
    private $pdfSize = 80000000;
    private $zipSize = 100000000;
    private $directoryToCheck;
    private $outputMode;
    private $actualReportDir;
    private $mimeTypes = [];

    private static $argsToCheck = array(
        "signatureDir", "tmpDir", "reportDir", "blackList", 
        "pdfSize", "zipSize", "directoryToCheck", "outputMode"
        );

    public function __construct(array $args) {
        $this->setProperties($args);
    }
    
    /**
     * Set the properties based on the console args
     * @param array $args
     * @return void
     */
    private function setProperties(array $args): void {
        foreach($this::$argsToCheck as $a) {
            if(isset($args[$a])) {
                $functionName = 'set'.ucfirst($a);
                $this->$functionName($args[$a]);
            }
        }
    }
    
    private function setSignatureDir(string $signatureDir): void {
        $this->signatureDir = $signatureDir;
    }

    private function setTmpDir(string $tmpDir): void {
        $this->tmpDir = $tmpDir;
    }

    private function setReportDir(string $reportDir): void {
        $this->reportDir = $reportDir;
    }

    private function setBlackList(array $blackList): void {
        $this->blackList = $blackList;
    }

    private function setPdfSize(int $pdfSize): void {
        $this->pdfSize = $pdfSize;
    }

    private function setZipSize(int $zipSize): void {
        $this->zipSize = $zipSize;
    }

    private function setDirectoryToCheck(string $directoryToCheck): void {
        $this->directoryToCheck = $directoryToCheck;
    }

    private function setOutputMode(int $outputMode): void {
        $this->outputMode = $outputMode;
    }

    public function getSignatureDir() {
        return $this->signatureDir;
    }

    public function getTmpDir() {
        return $this->tmpDir;
    }

    public function getReportDir() {
        return $this->reportDir;
    }

    public function getBlackList() {
        return $this->blackList;
    }

    public function getPdfSize() {
        return $this->pdfSize;
    }

    public function getZipSize() {
        return $this->zipSize;
    }

    public function getDirectoryToCheck() {
        if (substr($this->directoryToCheck, -1) != "/") {
            $this->directoryToCheck .= "/";
        }
        return $this->directoryToCheck;
    }

    public function getOutputMode() {
        return $this->outputMode;
    }

    public function isTmpDirReadable(): bool {
        if (is_dir($this->tmpDir) && is_writable($this->tmpDir)) {
            return true;
        } 
        return false;
    }
    
    public function isReportDirReadable(): bool {
        if (is_dir($this->reportDir) && is_writable($this->reportDir)) {
            return true;
        } 
        return false;
    }
    
    public function getActualReportDir() {
        return $this->actualReportDir;
    }
 
     /**
     * Create report dir files
     * @return void
     */
    public function createReportDirForActualReport(): bool {
        $this->actualReportDir = $this->reportDir . '/' . date('Y_m_d_H_i_s');
        if (!@mkdir($this->actualReportDir)) {
            return false;
        }
        if (!@mkdir($this->actualReportDir . '/js')) {
            return false;
        }
        if (!@mkdir($this->actualReportDir . '/css')){
            return false;
        }
        
        return true;
    }
    
    public function getMimeTypeByExtension(string $extension): bool  {
       
        if (!in_array(strtolower($extension), $this->mimeTypes)) {
            return false;
        }
        return true;
    }

    public function setMimeTypes($mimeTypes): void {
        $this->mimeTypes = $mimeTypes;
    }


    
}
