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
        "extension",
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
    
    public function createReportFiles(\OEAW\Object\SettingsObject $settings, array $result): bool {

        //create the empty json files
        $this->createReportFile($this::$jsonFiles, $settings->getActualReportDir());

        if ($settings->getOutputMode() == 1) {
            $this->createReportFile($this::$htmlFiles, $settings->getActualReportDir(), "html");
        }
        switch ($settings->getOutputMode()) {
            case 0:
                $res = $this->jsonOutput($result, $settings);
                break;
            case 1:
                $res = $this->jsonAndHtmlOutput($result, $settings);
                break;
            case 2:
                $res = $this->ndJsonOutput($result, $settings);
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

    public function jsonOutput(array $result, \OEAW\Object\SettingsObject $settings) {
        
        if (isset($result['fileList']) && count($result['fileList']) > 0) {
            $this->createDirectoryListJson($result['fileList'], $settings->getActualReportDir() . "/fileList.json");
        }
        
        if (isset($result['dirList']) && count($result['dirList']) > 0) {
            $this->createDirectoryListJson($result['dirList'], $settings->getActualReportDir() . "/directoryList.json");
        }
        
        if(isset($result['errors']) && count($result['errors']) > 0) {
            $this->createErrorsJson($result['errors'], $settings->getActualReportDir()."/error.json");
        }
    }

    private function createErrorsJson(array $data, string $file) {      
        
        $result = array();
        foreach($data as $k => $v) {
            $result[] = array("erroryType" => $v['errorType'], "dir" => $v['dir'], "filename" => $v['filename']);
        } 
        $jsonData = array("data" => $result);
        $json = json_encode($jsonData, JSON_UNESCAPED_SLASHES);
        file_put_contents($file, $json);
    }
    
    private function createDirectoryListJson(array $data, string $file) {
        $dirs = [];
        foreach ($data as $k => $v) {
            $dirs[] = $v->toJsonFile();
        }
        $jsonData = array("data" => $dirs);
        $json = json_encode($jsonData, JSON_UNESCAPED_SLASHES);
        file_put_contents($file, $json);
    }

    public function htmlOutput(array $result) {
        
    }

    public function ndJsonOutput(array $result) {
        
    }
}
