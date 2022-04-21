<?php

namespace OEAW\Helper;

/**
 * Description of ReportFileHelper
 *
 * @author nczirjak
 */
class ReportFileHelper {
     private static $jsonFiles = array(
        "directories",
        "directoryList",
        "duplicates",
        "error",
        "extensions",
        "files",
        "fileList",
        "fileTypeList"
    );
    private static $htmlFiles = array(
        "directoryList",
        "errorList",
        "fileTypeList",
        "fileList"
    );
    
    private $fileList = [];
    private $dirList = [];
    private $errors = [];
    private $settings;
    
    public function __construct(array $result, \OEAW\Object\SettingsObject $settings) {
        $this->fileList = $result['fileList'];
        $this->dirList = $result['dirList'];
        $this->errors = $result['errors'];
        $this->settings = $settings;
        
    }
    
    public function createReportFiles(): bool {
        
        //create the empty json files
        $this->createReportFile($this::$jsonFiles, $this->settings->getActualReportDir());

        if ($this->settings->getOutputMode() == 1) {
            $this->createReportFile($this::$htmlFiles, $this->settings->getActualReportDir(), "html");
        }
        switch ($this->settings->getOutputMode()) {
            case 0:
                $res = $this->jsonOutput();
                break;
            case 1:
                $res = $this->jsonAndHtmlOutput();
                break;
            case 2:
                $res = $this->ndJsonOutput();
                break;
            default:
                break;
        }
        
        return true;
        
    }

    private function createReportFile(array $list, string $dir, string $extension = "json"): void {
        foreach ($list as $l) {
            try {
                $file = fopen($dir . "/" . $l . "." . $extension, "w");
            } catch (\Exception $ex) {
                throw new Exception("Unable to open " . $dir . "/" . $l . "." . $extension);
            }
            fclose($file);
        }
    }

    public function jsonOutput() {
        
        if (isset($this->fileList) && count($this->fileList) > 0) {
            $this->createFileExtensionListData();
            $this->createFileList();
        }
        
        if (isset($this->dirList) && count($this->dirList) > 0) {
            $this->createDirectoryListJson();
        }
        
        if(isset($this->errors) && count($this->errors) > 0) {
            $this->createErrorsJson();
        }
    }

    private function createErrorsJson() {      
        $report = new \OEAW\Report\FileErrorListReport($this->errors, $this->settings->getActualReportDir()."/error.json");
        $report->__toJson();    
        
    }
    
    private function createDirectoryListJson() {
        $dirs = [];
        foreach ($this->dirList as $k => $v) {
            $dirs[] = $v->toJsonFile();
        }
        $jsonData = array("data" => $dirs);
        $json = json_encode($jsonData, JSON_UNESCAPED_SLASHES);
        file_put_contents($this->settings->getActualReportDir() . "/directoryList.json", $json);
    }
    
    private function createFileExtensionListData() {
        $report = new \OEAW\Report\FileExtensionListReport($this->fileList, $this->settings->getActualReportDir()."/extensions.json");
        $report->__toJson();        
    }

    public function htmlOutput() {
        
    }

    public function ndJsonOutput() {
        
    }

    private function createFileList() {
        $report = new \OEAW\Report\FileListReport($this->fileList, $this->settings->getActualReportDir()."/fileList.json");
        $report->__toJson(); 
    }

}
